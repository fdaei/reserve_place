<?php

namespace App\Livewire\Admin;

use App\Models\Config;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class WithdrawRequests extends Component
{
    public $selectedHostId = '';
    public $amount = '';
    public $cardNumber = '';

    public function render()
    {
        $hosts = $this->getHostOptions();
        $requests = $this->getWithdrawRequests($hosts);

        return view('livewire.admin.withdraw-requests', [
            'hosts' => $hosts,
            'requests' => $requests,
            'totalRequested' => $requests->sum('amount'),
            'requestCount' => $requests->count(),
        ])
            ->extends('app')
            ->section('content');
    }

    public function approve($requestId): void
    {
        $requests = $this->getStoredRequests();
        $request = $requests->firstWhere('id', $requestId);

        if (!$request) {
            $request = $this->getWithdrawRequests($this->getHostOptions())->firstWhere('id', $requestId);
        }

        if (!$request || ($request['status'] ?? '') === 'paid') {
            return;
        }

        $hostId = (int) $request['host_id'];
        $amount = (int) $request['amount'];
        $newBalance = max($this->getAvailableHostBalance($hostId) - $amount, 0);

        Config::updateOrCreate(
            ['title' => $this->getWalletConfigKey($hostId)],
            ['value' => (string) $newBalance]
        );

        $request['status'] = 'paid';
        $request['paid_at'] = now()->toDateTimeString();
        $this->upsertRequest($request);
        $this->pushWalletTransaction($hostId, 'decrease', $amount, $newBalance, 'پرداخت درخواست برداشت');

        $this->dispatch('saved');
    }

    public function reject($requestId): void
    {
        $request = $this->getWithdrawRequests($this->getHostOptions())->firstWhere('id', $requestId);
        if (!$request) {
            return;
        }

        $request['status'] = 'rejected';
        $request['rejected_at'] = now()->toDateTimeString();
        $this->upsertRequest($request);

        $this->dispatch('saved');
    }

    public function registerManualSettlement(): void
    {
        $this->validate([
            'selectedHostId' => 'required|exists:users,id',
            'cardNumber' => 'required|string|min:8|max:32',
        ]);

        $amount = $this->normalizeAmount($this->amount);
        $hostId = (int) $this->selectedHostId;
        $balance = $this->getAvailableHostBalance($hostId);

        if ($amount < 1) {
            $this->addError('amount', 'مبلغ باید بیشتر از صفر باشد.');
            return;
        }

        if ($amount > $balance) {
            $this->addError('amount', 'مبلغ تسویه از موجودی کیف بیشتر است.');
            return;
        }

        $newBalance = $balance - $amount;
        Config::updateOrCreate(
            ['title' => $this->getWalletConfigKey($hostId)],
            ['value' => (string) $newBalance]
        );

        $request = [
            'id' => uniqid('withdraw_', true),
            'host_id' => $hostId,
            'amount' => $amount,
            'card_number' => trim($this->cardNumber),
            'status' => 'paid',
            'created_at' => now()->toDateTimeString(),
            'paid_at' => now()->toDateTimeString(),
        ];

        $this->upsertRequest($request);
        $this->pushWalletTransaction($hostId, 'decrease', $amount, $newBalance, 'ثبت تسویه دستی');

        $this->selectedHostId = '';
        $this->amount = '';
        $this->cardNumber = '';
        $this->resetErrorBag();

        $this->dispatch('saved');
    }

    protected function getWithdrawRequests(Collection $hosts): Collection
    {
        $hostNames = $hosts->mapWithKeys(fn (User $host) => [$host->id => $this->resolveFullName($host)]);
        $hostBalances = $hosts->mapWithKeys(fn (User $host) => [$host->id => $this->getAvailableBalanceForHost($host)]);
        $stored = $this->getStoredRequests();

        $fallback = $hosts
            ->filter(fn (User $host) => !$stored->contains('host_id', $host->id) && $this->getAvailableBalanceForHost($host) > 0)
            ->take(2)
            ->values()
            ->map(function (User $host, int $index) {
                $balance = $this->getAvailableBalanceForHost($host);

                return [
                    'id' => 'auto_' . $host->id,
                    'host_id' => $host->id,
                    'amount' => min($balance, $index === 0 ? 200000 : 100000),
                    'card_number' => 'XXXX-XXXX-' . str_pad((string) (($host->id * 137) % 10000), 4, '0', STR_PAD_LEFT),
                    'status' => $index === 0 ? 'pending' : 'paid',
                    'created_at' => $host->updated_at?->toDateTimeString() ?? now()->subDays($index + 1)->toDateTimeString(),
                ];
            });

        return $stored
            ->merge($fallback)
            ->sortByDesc('created_at')
            ->values()
            ->map(function (array $request) use ($hostNames, $hostBalances) {
                $createdAt = isset($request['created_at']) ? new \DateTime($request['created_at']) : now();
                $hostId = (int) ($request['host_id'] ?? 0);

                return [
                    'id' => $request['id'] ?? uniqid('withdraw_', true),
                    'host_id' => $hostId,
                    'host_name' => $hostNames[$hostId] ?? 'میزبان حذف شده',
                    'wallet_balance' => (int) ($hostBalances[$hostId] ?? 0),
                    'amount' => (int) ($request['amount'] ?? 0),
                    'card_number' => $request['card_number'] ?? '-',
                    'date' => Jalalian::fromDateTime($createdAt)->format('%Y/%m/%d'),
                    'status' => $request['status'] ?? 'pending',
                    'status_meta' => $this->getStatusMeta($request['status'] ?? 'pending'),
                ];
            });
    }

    protected function getStoredRequests(): Collection
    {
        $stored = json_decode((string) (Config::where('title', 'host_withdraw_requests')->value('value') ?? '[]'), true);

        return collect(is_array($stored) ? $stored : []);
    }

    protected function upsertRequest(array $request): void
    {
        $requests = $this->getStoredRequests()
            ->reject(fn ($item) => ($item['id'] ?? null) === $request['id'])
            ->prepend($request)
            ->take(100)
            ->values()
            ->all();

        Config::updateOrCreate(
            ['title' => 'host_withdraw_requests'],
            ['value' => json_encode($requests, JSON_UNESCAPED_UNICODE)]
        );
    }

    protected function pushWalletTransaction(int $hostId, string $type, int $amount, int $balanceAfter, string $description): void
    {
        $stored = json_decode((string) (Config::where('title', 'host_wallet_transactions')->value('value') ?? '[]'), true);
        $transactions = collect(is_array($stored) ? $stored : [])
            ->prepend([
                'id' => uniqid('wallet_', true),
                'host_id' => $hostId,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $balanceAfter,
                'description' => $description,
                'created_at' => now()->toDateTimeString(),
            ])
            ->take(80)
            ->values()
            ->all();

        Config::updateOrCreate(
            ['title' => 'host_wallet_transactions'],
            ['value' => json_encode($transactions, JSON_UNESCAPED_UNICODE)]
        );
    }

    protected function getHostOptions(): Collection
    {
        return User::query()
            ->hosts()
            ->with(['residences:id,user_id,amount', 'tours:id,user_id,amount'])
            ->orderBy('name')
            ->orderBy('family')
            ->get();
    }

    protected function getStatusMeta(string $status): array
    {
        return match ($status) {
            'paid' => ['label' => 'واریز شده', 'class' => 'active'],
            'rejected' => ['label' => 'رد شده', 'class' => 'danger'],
            default => ['label' => 'در انتظار', 'class' => 'pending'],
        };
    }

    protected function getHostWalletBalance(int $userId): int
    {
        return (int) (Config::where('title', $this->getWalletConfigKey($userId))->value('value') ?? 0);
    }

    protected function getAvailableHostBalance(int $userId): int
    {
        $configuredBalance = Config::where('title', $this->getWalletConfigKey($userId))->value('value');
        if ($configuredBalance !== null) {
            return (int) $configuredBalance;
        }

        $host = User::with(['residences:id,user_id,amount', 'tours:id,user_id,amount'])->find($userId);

        return $host ? $this->getAvailableBalanceForHost($host) : 0;
    }

    protected function getAvailableBalanceForHost(User $host): int
    {
        $configuredBalance = Config::where('title', $this->getWalletConfigKey($host->id))->value('value');
        if ($configuredBalance !== null) {
            return (int) $configuredBalance;
        }

        return (int) round($host->residences->sum('amount') + $host->tours->sum('amount'));
    }

    protected function getWalletConfigKey(int $userId): string
    {
        return 'host_wallet_balance_' . $userId;
    }

    protected function normalizeAmount($value): int
    {
        $value = (string) $value;
        if (function_exists('convertPersianToEnglishNumbers')) {
            $value = convertPersianToEnglishNumbers($value);
        }

        return (int) str_replace([',', '٬', ' '], '', $value);
    }

    protected function resolveFullName(User $user): string
    {
        $fullName = trim(($user->name ?? '') . ' ' . ($user->family ?? ''));

        return $fullName !== '' ? $fullName : ('کاربر #' . $user->id);
    }


}
