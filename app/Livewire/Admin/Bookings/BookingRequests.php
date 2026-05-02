<?php

namespace App\Livewire\Admin\Bookings;

use App\Models\BookingRequest;
use App\Models\User;
use App\Services\Admin\ReservationRequestService;
use App\Support\Admin\AdminResourceRegistry;
use App\Support\Admin\PersianDate;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class BookingRequests extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public string $hostFilter = 'all';

    public string $fromDate = '';

    public string $toDate = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'hostFilter' => ['except' => 'all'],
        'fromDate' => ['except' => ''],
        'toDate' => ['except' => ''],
    ];

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('bookings-manage'), 403);
    }

    public function render()
    {
        return view('livewire.admin.bookings.booking-requests', [
            'requests' => $this->requestQuery()->latest('id')->paginate(12),
            'hosts' => $this->hostOptions(),
            'statusOptions' => BookingRequest::statuses(),
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'statusFilter', 'hostFilter', 'fromDate', 'toDate'], true)) {
            $this->resetPage();
        }
    }

    public function approveRequest(int $id): void
    {
        $request = BookingRequest::query()->find($id);
        if (! $request) {
            return;
        }

        $request->update([
            'status' => 'approved',
            'host_approval_status' => 'manual_approved',
        ]);

        app(ReservationRequestService::class)->approve($request->refresh());
        $this->dispatch('booking-request-saved');
    }

    public function rejectRequest(int $id): void
    {
        BookingRequest::query()
            ->whereKey($id)
            ->update([
                'status' => 'rejected',
                'host_approval_status' => 'rejected',
            ]);

        $this->dispatch('booking-request-saved');
    }

    public function markPaid(int $id): void
    {
        $request = BookingRequest::query()->find($id);
        if (! $request) {
            return;
        }

        $request->update(['payment_status' => 'paid']);
        app(ReservationRequestService::class)->recordPayment($request->refresh());
        $this->dispatch('booking-request-saved');
    }

    public function releaseAmount(int $id): void
    {
        $request = BookingRequest::query()->find($id);
        if (! $request) {
            return;
        }

        app(ReservationRequestService::class)->releaseAmount($request);
        $this->dispatch('booking-request-saved');
    }

    public function settleWithHost(int $id): void
    {
        $request = BookingRequest::query()->find($id);
        if (! $request) {
            return;
        }

        app(ReservationRequestService::class)->settleWithHost($request);
        $this->dispatch('booking-request-saved');
    }

    protected function requestQuery(): Builder
    {
        return BookingRequest::query()
            ->with(['customer', 'host', 'assignee', 'bookable'])
            ->when(trim($this->search) !== '', fn (Builder $query) => $this->applySearch($query, trim($this->search)))
            ->when($this->statusFilter !== 'all', fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->when($this->hostFilter !== 'all', fn (Builder $query) => $query->where('host_id', (int) $this->hostFilter))
            ->when($this->parsedDate($this->fromDate), fn (Builder $query, string $date) => $query->where('starts_at', '>=', $date.' 00:00:00'))
            ->when($this->parsedDate($this->toDate), fn (Builder $query, string $date) => $query->where('starts_at', '<=', $date.' 23:59:59'));
    }

    protected function applySearch(Builder $query, string $search): void
    {
        $query->where(function (Builder $builder) use ($search) {
            $builder
                ->where('request_number', 'like', '%'.$search.'%')
                ->orWhere('guest_name', 'like', '%'.$search.'%')
                ->orWhere('guest_phone', 'like', '%'.$search.'%')
                ->orWhereHas('host', function (Builder $host) use ($search) {
                    $host
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('family', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                })
                ->orWhereHas('bookable', fn (Builder $bookable) => $bookable->where('title', 'like', '%'.$search.'%'));
        });
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
