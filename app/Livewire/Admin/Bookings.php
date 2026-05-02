<?php

namespace App\Livewire\Admin;

use App\Models\Config;
use App\Models\FoodStore;
use App\Models\Residence;
use App\Models\Tour;
use App\Models\User;
use App\Support\Admin\PersianDate;
use Illuminate\Support\Collection;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class Bookings extends Component
{
    public $serviceFilter = 'all';

    public $modelFilter = 'all';

    public $dateFrom = '';

    public $dateTo = '';

    public function render()
    {
        $bookings = $this->getBookingRows();

        if ($this->serviceFilter !== 'all') {
            $bookings = $bookings->where('type', $this->serviceFilter)->values();
        }

        if ($this->modelFilter !== 'all') {
            $bookings = $bookings->where('model', $this->modelFilter)->values();
        }

        if (trim($this->dateFrom) !== '') {
            $dateFrom = $this->normalizeDate($this->dateFrom);
            $bookings = $bookings
                ->filter(fn ($booking) => $booking['date_key'] >= $dateFrom)
                ->values();
        }

        if (trim($this->dateTo) !== '') {
            $dateTo = $this->normalizeDate($this->dateTo);
            $bookings = $bookings
                ->filter(fn ($booking) => $booking['date_key'] <= $dateTo)
                ->values();
        }

        return view('livewire.admin.bookings', [
            'bookings' => $bookings,
        ])
            ->extends('app')
            ->section('content');
    }

    protected function getBookingRows(): Collection
    {
        return collect()
            ->merge($this->getServiceRows(Residence::class, 'residence'))
            ->merge($this->getServiceRows(Tour::class, 'tour'))
            ->merge($this->getServiceRows(FoodStore::class, 'foodstore'))
            ->sortByDesc('timestamp')
            ->take(20)
            ->values()
            ->map(function (array $row, int $index) {
                $row['code'] = '#'.str_pad((string) (($index + 1) * 3 + $row['id']), 3, '0', STR_PAD_LEFT);
                $row['status'] = $this->getConfigValue(
                    $this->getConfigKey('booking_status', $row['type'], $row['id']),
                    'completed'
                );
                $row['status_meta'] = $this->getStatusMeta($row['status']);
                $row['employee'] = $this->getConfigValue(
                    $this->getConfigKey('booking_request_employee', $row['type'], $row['id']),
                    '-'
                );

                return $row;
            });
    }

    protected function getServiceRows(string $modelClass, string $type): Collection
    {
        return $modelClass::query()
            ->with('admin:id,name,family,phone')
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn ($item) => $this->makeServiceRow($item, $type));
    }

    protected function makeServiceRow($item, string $type): array
    {
        $host = $item->admin;
        $createdAt = $item->created_at ?? now();
        $isPaid = (int) ($item->vip ?? 0) === 1;

        return [
            'id' => $item->id,
            'type' => $type,
            'type_label' => $this->getTypeLabel($type),
            'service_name' => $item->title ?: ('سرویس #'.$item->id),
            'user_name' => $this->resolveFullName($host),
            'amount' => (int) ($item->amount ?? 0),
            'model' => $isPaid ? 'professional' : 'free',
            'model_label' => $isPaid ? 'حرفه‌ای' : 'رایگان',
            'date' => Jalalian::fromDateTime($createdAt)->format('%Y/%m/%d'),
            'date_key' => $createdAt->toDateString(),
            'timestamp' => $createdAt->timestamp,
        ];
    }

    protected function getStatusMeta(string $status): array
    {
        return match ($status) {
            'pending' => ['label' => 'در انتظار', 'class' => 'pending'],
            'cancelled' => ['label' => 'لغو شده', 'class' => 'danger'],
            default => ['label' => 'تکمیل شده', 'class' => 'active'],
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

    protected function normalizeDate(string $date): string
    {
        return PersianDate::parse($date) ?: trim($date);
    }

    protected function resolveFullName(?User $user): string
    {
        if (! $user) {
            return 'بدون کاربر';
        }

        $fullName = trim(($user->name ?? '').' '.($user->family ?? ''));

        return $fullName !== '' ? $fullName : ('کاربر #'.$user->id);
    }

    public function mount()
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('bookings-manage'), 403);
    }
}
