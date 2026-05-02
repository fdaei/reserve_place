<?php

namespace App\Livewire\Admin;

use App\Models\Config;
use App\Models\FoodStore;
use App\Models\Residence;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class BookingRequests extends Component
{
    public $statusFilter = 'all';

    public $hostFilter = 'all';

    public function render()
    {
        $requests = $this->getBookingRequestRows();

        if ($this->statusFilter !== 'all') {
            $requests = $requests->where('status', $this->statusFilter)->values();
        }

        if ($this->hostFilter !== 'all') {
            $requests = $requests->where('host_id', (int) $this->hostFilter)->values();
        }

        return view('livewire.admin.booking-requests', [
            'requests' => $requests,
            'hosts' => $this->getHostOptions(),
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName = null)
    {
        $this->resetValidation();
    }

    public function assignToMe($type, $id): void
    {
        $this->setRequestStatus($type, $id, 'assigned');
        $this->setRequestEmployee($type, $id, auth()->user()?->name ?: 'ادمین');

        $this->dispatch('saved');
    }

    public function markCalled($type, $id): void
    {
        $this->setRequestStatus($type, $id, 'called');
        $this->setRequestEmployee($type, $id, auth()->user()?->name ?: 'ادمین');

        $this->dispatch('saved');
    }

    public function completeBooking($type, $id): void
    {
        $this->setRequestStatus($type, $id, 'completed');
        Config::updateOrCreate(
            ['title' => $this->getConfigKey('booking_status', $type, $id)],
            ['value' => 'completed']
        );

        $this->dispatch('saved');
    }

    public function cancelRequest($type, $id): void
    {
        $this->setRequestStatus($type, $id, 'cancelled');

        $this->dispatch('saved');
    }

    protected function getBookingRequestRows(): Collection
    {
        return collect()
            ->merge($this->getServiceRows(Residence::class, 'residence'))
            ->merge($this->getServiceRows(Tour::class, 'tour'))
            ->merge($this->getServiceRows(FoodStore::class, 'foodstore'))
            ->sortByDesc('timestamp')
            ->take(12)
            ->values()
            ->map(function (array $row, int $index) {
                $row['status'] = $this->getConfigValue(
                    $this->getConfigKey('booking_request_status', $row['type'], $row['id']),
                    $index % 2 === 0 ? 'pending' : 'called'
                );
                $row['employee'] = $this->getConfigValue(
                    $this->getConfigKey('booking_request_employee', $row['type'], $row['id']),
                    '-'
                );
                $row['status_meta'] = $this->getStatusMeta($row['status']);

                return $row;
            });
    }

    protected function getServiceRows(string $modelClass, string $type): Collection
    {
        return $modelClass::query()
            ->with('admin:id,name,family,phone')
            ->latest('id')
            ->limit(6)
            ->get()
            ->map(fn ($item) => $this->makeServiceRow($item, $type));
    }

    protected function makeServiceRow($item, string $type): array
    {
        $host = $item->admin;
        $createdAt = $item->created_at ?? now();

        return [
            'id' => $item->id,
            'type' => $type,
            'type_label' => $this->getTypeLabel($type),
            'title' => $item->title ?: ('سرویس #'.$item->id),
            'host_id' => (int) $item->user_id,
            'host_name' => $this->resolveFullName($host),
            'host_phone' => $host->phone ?? '',
            'amount' => (int) ($item->amount ?? 0),
            'model_label' => (int) ($item->vip ?? 0) === 1 ? 'حرفه‌ای' : 'رایگان',
            'date' => Jalalian::fromDateTime($createdAt)->format('%Y/%m/%d'),
            'timestamp' => $createdAt->timestamp,
        ];
    }

    protected function getHostOptions(): Collection
    {
        return User::query()
            ->hosts()
            ->orderBy('name')
            ->orderBy('family')
            ->get();
    }

    protected function setRequestStatus($type, $id, string $status): void
    {
        Config::updateOrCreate(
            ['title' => $this->getConfigKey('booking_request_status', $type, $id)],
            ['value' => $status]
        );
    }

    protected function setRequestEmployee($type, $id, string $employee): void
    {
        Config::updateOrCreate(
            ['title' => $this->getConfigKey('booking_request_employee', $type, $id)],
            ['value' => $employee]
        );
    }

    protected function getStatusMeta(string $status): array
    {
        return match ($status) {
            'assigned' => ['label' => 'اختصاص داده شده', 'class' => 'info'],
            'called' => ['label' => 'تماس گرفته شد', 'class' => 'active'],
            'completed' => ['label' => 'تکمیل شده', 'class' => 'active'],
            'cancelled' => ['label' => 'لغو شده', 'class' => 'danger'],
            default => ['label' => 'در انتظار بررسی', 'class' => 'pending'],
        };
    }

    protected function getTypeLabel(string $type): string
    {
        return config('entity-types.service_types.'.$type, config('entity-types.service_types.residence'));
    }

    protected function getConfigValue(string $key, string $default = ''): string
    {
        return (string) (Config::where('title', $key)->value('value') ?? $default);
    }

    protected function getConfigKey(string $prefix, string $type, $id): string
    {
        return $prefix.'_'.$type.'_'.$id;
    }

    protected function resolveFullName(?User $user): string
    {
        if (! $user) {
            return 'بدون میزبان';
        }

        $fullName = trim(($user->name ?? '').' '.($user->family ?? ''));

        return $fullName !== '' ? $fullName : ('کاربر #'.$user->id);
    }
}
