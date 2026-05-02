<?php

namespace App\Livewire\Admin\Finance;

use App\Models\Settlement;
use App\Models\User;
use App\Support\Admin\AdminResourceRegistry;
use App\Support\Admin\PersianDate;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Settlements extends Component
{
    use WithPagination;

    public string $search = '';

    public string $hostFilter = 'all';

    public string $statusFilter = 'all';

    public string $fromDate = '';

    public string $toDate = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'hostFilter' => ['except' => 'all'],
        'statusFilter' => ['except' => 'all'],
        'fromDate' => ['except' => ''],
        'toDate' => ['except' => ''],
    ];

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('finance-manage'), 403);
    }

    public function render()
    {
        $query = $this->settlementQuery();

        return view('livewire.admin.finance.settlements', [
            'settlements' => $query->latest('id')->paginate(12),
            'hosts' => $this->hostOptions(),
            'stats' => $this->stats(),
            'statusOptions' => [
                'pending' => 'در انتظار بررسی',
                'approved' => 'تایید شده',
                'paid' => 'واریز شده',
                'rejected' => 'رد شده',
            ],
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'hostFilter', 'statusFilter', 'fromDate', 'toDate'], true)) {
            $this->resetPage();
        }
    }

    protected function settlementQuery(): Builder
    {
        return Settlement::query()
            ->with(['host', 'withdrawRequest'])
            ->when($this->hostFilter !== 'all', fn (Builder $query) => $query->where('host_id', (int) $this->hostFilter))
            ->when($this->statusFilter !== 'all', fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->when(trim($this->search) !== '', function (Builder $query) {
                $search = trim($this->search);

                $query->where(function (Builder $builder) use ($search) {
                    $builder
                        ->where('iban', 'like', '%'.$search.'%')
                        ->orWhere('card_number', 'like', '%'.$search.'%')
                        ->orWhere('account_owner', 'like', '%'.$search.'%')
                        ->orWhereHas('host', function (Builder $host) use ($search) {
                            $host
                                ->where('name', 'like', '%'.$search.'%')
                                ->orWhere('family', 'like', '%'.$search.'%')
                                ->orWhere('phone', 'like', '%'.$search.'%');
                        });
                });
            })
            ->when($this->parsedDate($this->fromDate), fn (Builder $query, string $date) => $query->where('requested_at', '>=', $date.' 00:00:00'))
            ->when($this->parsedDate($this->toDate), fn (Builder $query, string $date) => $query->where('requested_at', '<=', $date.' 23:59:59'));
    }

    protected function hostOptions()
    {
        return User::query()
            ->where(function (Builder $query) {
                $query
                    ->whereHas('roles', fn (Builder $role) => $role->where('slug', config('access-control.host_role')))
                    ->orHas('residences')
                    ->orHas('tours')
                    ->orHas('foodstores');
            })
            ->orderBy('name')
            ->orderBy('family')
            ->get(['id', 'name', 'family', 'phone']);
    }

    protected function stats(): array
    {
        return [
            'total' => (int) Settlement::query()->sum('amount'),
            'pending' => (int) Settlement::query()->where('status', 'pending')->sum('amount'),
            'paid' => (int) Settlement::query()->where('status', 'paid')->sum('amount'),
            'count' => Settlement::query()->count(),
        ];
    }

    protected function parsedDate(string $value): ?string
    {
        return PersianDate::parse($value);
    }

    public function statusLabel(?string $status): string
    {
        return AdminResourceRegistry::statusLabel($status);
    }

    public function statusClass(?string $status): string
    {
        return AdminResourceRegistry::statusClass($status);
    }
}
