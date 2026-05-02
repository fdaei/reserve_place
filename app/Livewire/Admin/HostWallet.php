<?php

namespace App\Livewire\Admin;

use App\Models\Config;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class HostWallet extends Component
{
    public $selectedHostId = '';
    public $amount = '';
    public $description = '';
    public $hostFilter = 'all';

    public function render()
    {
        $hosts = $this->getHostOptions();
        $transactions = $this->getTransactions($hosts);

        if ($this->hostFilter !== 'all') {
            $transactions = $transactions
                ->where('host_id', (int) $this->hostFilter)
                ->values();
        }

        return view('livewire.admin.host-wallet', [
            'hosts' => $hosts,
            'transactions' => $transactions,
            'totalBalance' => $hosts->sum(fn (User $host) => $this->getHostWalletBalance($host->id)),
            'pendingWithdrawals' => $hosts->sum(fn (User $host) => max($this->calculateHostIncome($host) - $this->getHostSettledIncome($host->id), 0)),
        ])
            ->extends('app')
            ->section('content');
    }

    public function increaseBalance(): void
    {
        $this->validate([
            'selectedHostId' => 'required|exists:users,id',
            'description' => 'nullable|string|max:190',
        ]);

        $amount = $this->normalizeAmount($this->amount);
        if ($amount < 1) {
            $this->addError('amount', 'مبلغ باید بیشتر از صفر باشد.');
            return;
        }

        $hostId = (int) $this->selectedHostId;
        $newBalance = $this->getHostWalletBalance($hostId) + $amount;

        Config::updateOrCreate(
            ['title' => $this->getWalletConfigKey($hostId)],
            ['value' => (string) $newBalance]
        );

        $this->pushTransaction([
            'id' => uniqid('wallet_', true),
            'host_id' => $hostId,
            'type' => 'increase',
            'amount' => $amount,
            'balance_after' => $newBalance,
            'description' => trim($this->description) !== '' ? trim($this->description) : 'افزایش موجودی کیف پول',
            'created_at' => now()->toDateTimeString(),
        ]);

        $this->selectedHostId = '';
        $this->amount = '';
        $this->description = '';
        $this->resetErrorBag();

        $this->dispatch('saved');
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

    protected function getTransactions(Collection $hosts): Collection
    {
        $hostNames = $hosts->mapWithKeys(fn (User $host) => [$host->id => $this->resolveFullName($host)]);
        $stored = json_decode((string) (Config::where('title', 'host_wallet_transactions')->value('value') ?? '[]'), true);
        $transactions = collect(is_array($stored) ? $stored : []);

        if ($transactions->isEmpty()) {
            $transactions = $this->buildSnapshotTransactions($hosts);
        }

        return $transactions
            ->sortByDesc('created_at')
            ->values()
            ->map(function (array $transaction) use ($hostNames) {
                $type = $transaction['type'] ?? 'increase';
                $createdAt = isset($transaction['created_at']) ? new \DateTime($transaction['created_at']) : now();

                return [
                    'id' => $transaction['id'] ?? uniqid('wallet_', true),
                    'host_id' => (int) ($transaction['host_id'] ?? 0),
                    'host_name' => $hostNames[(int) ($transaction['host_id'] ?? 0)] ?? 'میزبان حذف شده',
                    'date' => Jalalian::fromDateTime($createdAt)->format('%Y/%m/%d'),
                    'type' => $type,
                    'type_label' => $type === 'decrease' ? 'کاهش' : 'افزایش',
                    'type_class' => $type === 'decrease' ? 'danger' : 'active',
                    'amount' => (int) ($transaction['amount'] ?? 0),
                    'balance_after' => (int) ($transaction['balance_after'] ?? 0),
                    'description' => $transaction['description'] ?? '-',
                ];
            });
    }

    protected function buildSnapshotTransactions(Collection $hosts): Collection
    {
        return $hosts
            ->filter(fn (User $host) => $this->getHostWalletBalance($host->id) > 0)
            ->map(function (User $host) {
                $balance = $this->getHostWalletBalance($host->id);

                return [
                    'id' => 'snapshot_' . $host->id,
                    'host_id' => $host->id,
                    'type' => 'increase',
                    'amount' => $balance,
                    'balance_after' => $balance,
                    'description' => 'موجودی ثبت شده کیف پول میزبان',
                    'created_at' => $host->updated_at?->toDateTimeString() ?? now()->toDateTimeString(),
                ];
            })
            ->values();
    }

    protected function pushTransaction(array $transaction): void
    {
        $stored = json_decode((string) (Config::where('title', 'host_wallet_transactions')->value('value') ?? '[]'), true);
        $transactions = collect(is_array($stored) ? $stored : [])
            ->prepend($transaction)
            ->take(80)
            ->values()
            ->all();

        Config::updateOrCreate(
            ['title' => 'host_wallet_transactions'],
            ['value' => json_encode($transactions, JSON_UNESCAPED_UNICODE)]
        );
    }

    protected function calculateHostIncome(User $host): int
    {
        return (int) round($host->residences->sum('amount') + $host->tours->sum('amount'));
    }

    protected function getHostWalletBalance(int $userId): int
    {
        return (int) (Config::where('title', $this->getWalletConfigKey($userId))->value('value') ?? 0);
    }

    protected function getHostSettledIncome(int $userId): int
    {
        return (int) (Config::where('title', $this->getSettledIncomeConfigKey($userId))->value('value') ?? 0);
    }

    protected function getWalletConfigKey(int $userId): string
    {
        return 'host_wallet_balance_' . $userId;
    }

    protected function getSettledIncomeConfigKey(int $userId): string
    {
        return 'host_settled_income_' . $userId;
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
