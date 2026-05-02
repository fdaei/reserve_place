<?php

namespace App\Livewire\Admin;

use App\Models\Config;
use App\Models\Residence;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class Commissions extends Component
{
    public $dateFrom = '';
    public $dateTo = '';

    public function render()
    {
        $rows = $this->getCommissionRows();

        if (trim($this->dateFrom) !== '') {
            $rows = $rows
                ->filter(fn ($row) => $row['date'] >= $this->normalizeDate($this->dateFrom))
                ->values();
        }

        if (trim($this->dateTo) !== '') {
            $rows = $rows
                ->filter(fn ($row) => $row['date'] <= $this->normalizeDate($this->dateTo))
                ->values();
        }

        return view('livewire.admin.commissions', [
            'rows' => $rows,
        ])
            ->extends('app')
            ->section('content');
    }

    protected function getCommissionRows(): Collection
    {
        $commissionPercent = $this->getCommissionPercent();

        return collect()
            ->merge($this->getServiceRows(Residence::class, 'اقامتگاه', $commissionPercent))
            ->merge($this->getServiceRows(Tour::class, 'تور', $commissionPercent))
            ->sortByDesc('timestamp')
            ->take(20)
            ->values()
            ->map(function (array $row, int $index) {
                $row['code'] = '#' . (1000 + $index);

                return $row;
            });
    }

    protected function getServiceRows(string $modelClass, string $serviceLabel, int $commissionPercent): Collection
    {
        return $modelClass::query()
            ->with('admin:id,name,family')
            ->where('amount', '>', 0)
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(function ($item) use ($serviceLabel, $commissionPercent) {
                $amount = (int) ($item->amount ?? 0);
                $createdAt = $item->created_at ?? now();

                return [
                    'service' => $serviceLabel,
                    'host' => $this->resolveFullName($item->admin),
                    'base_amount' => $amount,
                    'commission_percent' => $commissionPercent,
                    'commission_amount' => (int) round($amount * $commissionPercent / 100),
                    'date' => Jalalian::fromDateTime($createdAt)->format('%Y/%m/%d'),
                    'timestamp' => $createdAt->timestamp,
                ];
            });
    }

    protected function getCommissionPercent(): int
    {
        $value = (string) (Config::where('title', 'commissionReserve')->value('value') ?? '10');
        if (function_exists('convertPersianToEnglishNumbers')) {
            $value = convertPersianToEnglishNumbers($value);
        }

        if (trim($value) === '') {
            return 10;
        }

        return max((int) str_replace(['%', ' '], '', $value), 0);
    }

    protected function normalizeDate(string $date): string
    {
        $date = trim($date);
        if (function_exists('convertPersianToEnglishNumbers')) {
            $date = convertPersianToEnglishNumbers($date);
        }

        return str_replace('-', '/', $date);
    }

    protected function resolveFullName(?User $user): string
    {
        if (!$user) {
            return 'بدون میزبان';
        }

        $fullName = trim(($user->name ?? '') . ' ' . ($user->family ?? ''));

        return $fullName !== '' ? $fullName : ('کاربر #' . $user->id);
    }


}
