<?php

namespace App\Livewire\Admin\Finance;

use App\Models\User;
use App\Services\Admin\HostWalletService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class HostWallet extends Component
{
    use WithPagination;

    public string $search = '';

    public string $hostFilter = 'all';

    public bool $chargeModalOpen = false;

    public string $chargeHostId = '';

    public string $chargeAmount = '';

    public string $chargeDescription = '';

    protected HostWalletService $walletService;

    protected $queryString = [
        'search' => ['except' => ''],
        'hostFilter' => ['except' => 'all'],
    ];

    protected array $validationAttributes = [
        'chargeHostId' => 'میزبان',
        'chargeAmount' => 'مبلغ',
        'chargeDescription' => 'توضیحات',
    ];

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('finance-manage'), 403);
    }

    public function boot(HostWalletService $walletService): void
    {
        $this->walletService = $walletService;
    }

    public function render()
    {
        $hosts = $this->hostQuery()
            ->paginate(12);

        $hosts->setCollection(
            $hosts->getCollection()->map(fn (User $host) => $this->decorateHost($host))
        );

        return view('livewire.admin.finance.host-wallet', [
            'hosts' => $hosts,
            'hostOptions' => $this->hostOptions(),
            'stats' => $this->walletService->systemStats(),
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'hostFilter'], true)) {
            $this->resetPage();
        }
    }

    public function openChargeModal(?int $hostId = null): void
    {
        $this->resetErrorBag();
        $this->chargeHostId = $hostId ? (string) $hostId : '';
        $this->chargeAmount = '';
        $this->chargeDescription = '';
        $this->chargeModalOpen = true;
    }

    public function closeChargeModal(): void
    {
        $this->chargeModalOpen = false;
        $this->resetErrorBag();
    }

    public function chargeWallet(): void
    {
        $this->validate([
            'chargeHostId' => ['required', 'exists:users,id'],
            'chargeDescription' => ['nullable', 'string', 'max:500'],
        ], [
            'required' => 'وارد کردن :attribute الزامی است.',
            'exists' => ':attribute انتخاب شده معتبر نیست.',
            'max' => ':attribute بیش از حد مجاز است.',
        ]);

        $amount = $this->walletService->normalizeAmount($this->chargeAmount);
        if ($amount < 1) {
            $this->addError('chargeAmount', 'وارد کردن مبلغ کیف پول الزامی است.');

            return;
        }

        $hostId = (int) $this->chargeHostId;
        $this->walletService->createManualCharge($hostId, $amount, $this->chargeDescription);

        $this->closeChargeModal();
        $this->dispatch('wallet-charged');
    }

    protected function hostQuery(): Builder
    {
        return User::query()
            ->where(function (Builder $query) {
                $query
                    ->whereHas('roles', fn (Builder $role) => $role->where('slug', config('access-control.host_role')))
                    ->orHas('residences')
                    ->orHas('tours')
                    ->orHas('foodstores');
            })
            ->when($this->hostFilter !== 'all', fn (Builder $query) => $query->whereKey((int) $this->hostFilter))
            ->when(trim($this->search) !== '', function (Builder $query) {
                $search = trim($this->search);

                $query->where(function (Builder $builder) use ($search) {
                    $builder
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('family', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                });
            })
            ->with(['walletTransactions', 'withdrawRequests'])
            ->withCount('residences')
            ->orderBy('name')
            ->orderBy('family');
    }

    protected function hostOptions()
    {
        return $this->hostQuery()
            ->select(['id', 'name', 'family', 'phone'])
            ->get();
    }

    protected function decorateHost(User $host): User
    {
        $summary = $this->walletService->summaryForHost($host);

        $host->row_total_balance = $summary['total'];
        $host->row_blocked_balance = $summary['blocked'];
        $host->row_withdrawable_balance = $summary['withdrawable'];
        $host->row_active_withdrawal = $host->withdrawRequests->firstWhere('status', 'pending');
        $host->row_full_name = $this->resolveFullName($host);

        return $host;
    }

    protected function resolveFullName(User $user): string
    {
        $fullName = trim(($user->name ?? '').' '.($user->family ?? ''));

        return $fullName !== '' ? $fullName : 'کاربر #'.$user->id;
    }
}
