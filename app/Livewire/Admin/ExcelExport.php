<?php

namespace App\Livewire\Admin;

use App\Models\Config;
use App\Models\FoodStore;
use App\Models\Residence;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class ExcelExport extends Component
{
    public $financialReport = 'booking_requests';
    public $userReport = 'users';
    public $dateFrom = '';
    public $dateTo = '';

    public function render()
    {
        return view('livewire.admin.export')
            ->extends('app')
            ->section('content');
    }

    public function downloadFinancialExport()
    {
        [$headers, $rows] = match ($this->financialReport) {
            'bookings' => $this->getBookingsExport(),
            'withdraws' => $this->getWithdrawsExport(),
            'commissions' => $this->getCommissionsExport(),
            'wallet' => $this->getWalletExport(),
            default => $this->getBookingRequestsExport(),
        };

        $rows = $this->filterRowsByDate($rows);

        return $this->downloadCsv($headers, $rows, 'financial-report-' . now()->format('Y-m-d') . '.csv');
    }

    public function downloadUsersExport()
    {
        [$headers, $rows] = match ($this->userReport) {
            'hosts' => $this->getHostsExport(),
            'employees' => $this->getEmployeesExport(),
            default => $this->getUsersExport(),
        };

        return $this->downloadCsv($headers, $rows, 'users-report-' . now()->format('Y-m-d') . '.csv');
    }

    protected function getBookingRequestsExport(): array
    {
        return [
            ['کد', 'خدمت', 'میزبان', 'مبلغ', 'تاریخ', 'وضعیت'],
            $this->getServiceRows()
                ->map(fn ($row, $index) => [
                    '#' . (2000 + $index),
                    $row['service'],
                    $row['host'],
                    $row['amount'],
                    $row['date'],
                    'در انتظار بررسی',
                ]),
        ];
    }

    protected function getBookingsExport(): array
    {
        return [
            ['کد', 'خدمت', 'میزبان', 'مبلغ', 'تاریخ', 'وضعیت'],
            $this->getServiceRows()
                ->map(fn ($row, $index) => [
                    '#' . (1000 + $index),
                    $row['service'],
                    $row['host'],
                    $row['amount'],
                    $row['date'],
                    'تکمیل شده',
                ]),
        ];
    }

    protected function getWithdrawsExport(): array
    {
        $stored = json_decode((string) (Config::where('title', 'host_withdraw_requests')->value('value') ?? '[]'), true);
        $hosts = User::query()->pluck('name', 'id');

        return [
            ['میزبان', 'مبلغ', 'شماره کارت', 'تاریخ', 'وضعیت'],
            collect(is_array($stored) ? $stored : [])->map(function (array $row) use ($hosts) {
                $createdAt = isset($row['created_at']) ? new \DateTime($row['created_at']) : now();

                return [
                    $hosts[(int) ($row['host_id'] ?? 0)] ?? 'میزبان حذف شده',
                    (int) ($row['amount'] ?? 0),
                    $row['card_number'] ?? '-',
                    Jalalian::fromDateTime($createdAt)->format('%Y/%m/%d'),
                    $this->getWithdrawStatusLabel($row['status'] ?? 'pending'),
                ];
            }),
        ];
    }

    protected function getCommissionsExport(): array
    {
        $percent = $this->getCommissionPercent();

        return [
            ['کد رزرو', 'خدمت', 'میزبان', 'مبلغ پایه', 'درصد کمیسیون', 'مبلغ کمیسیون', 'تاریخ'],
            $this->getServiceRows()
                ->filter(fn ($row) => $row['amount'] > 0)
                ->map(fn ($row, $index) => [
                    '#' . (1000 + $index),
                    $row['service'],
                    $row['host'],
                    $row['amount'],
                    $percent . '%',
                    (int) round($row['amount'] * $percent / 100),
                    $row['date'],
                ]),
        ];
    }

    protected function getWalletExport(): array
    {
        $stored = json_decode((string) (Config::where('title', 'host_wallet_transactions')->value('value') ?? '[]'), true);
        $hosts = User::query()->pluck('name', 'id');

        return [
            ['تاریخ', 'میزبان', 'نوع', 'مبلغ', 'موجودی بعدی', 'توضیحات'],
            collect(is_array($stored) ? $stored : [])->map(function (array $row) use ($hosts) {
                $createdAt = isset($row['created_at']) ? new \DateTime($row['created_at']) : now();

                return [
                    Jalalian::fromDateTime($createdAt)->format('%Y/%m/%d'),
                    $hosts[(int) ($row['host_id'] ?? 0)] ?? 'میزبان حذف شده',
                    ($row['type'] ?? 'increase') === 'decrease' ? 'کاهش' : 'افزایش',
                    (int) ($row['amount'] ?? 0),
                    (int) ($row['balance_after'] ?? 0),
                    $row['description'] ?? '-',
                ];
            }),
        ];
    }

    protected function getUsersExport(): array
    {
        return [
            ['نام', 'موبایل', 'تاریخ ثبت'],
            User::query()
                ->regularCustomers()
                ->latest('id')
                ->get()
                ->map(fn (User $user) => [
                    $this->resolveFullName($user),
                    $user->phone,
                    Jalalian::fromDateTime($user->created_at ?? now())->format('%Y/%m/%d'),
                ]),
        ];
    }

    protected function getHostsExport(): array
    {
        return [
            ['نام', 'موبایل', 'تعداد اقامتگاه', 'تعداد تور', 'تعداد رستوران'],
            User::query()
                ->hosts()
                ->withCount(['residences', 'tours', 'foodstores'])
                ->latest('id')
                ->get()
                ->map(fn (User $user) => [
                    $this->resolveFullName($user),
                    $user->phone,
                    $user->residences_count,
                    $user->tours_count,
                    $user->foodstores_count,
                ]),
        ];
    }

    protected function getEmployeesExport(): array
    {
        return [
            ['نام', 'موبایل', 'نقش'],
            User::query()
                ->with('roles')
                ->whereHas('roles')
                ->latest('id')
                ->get()
                ->map(fn (User $user) => [
                    $this->resolveFullName($user),
                    $user->phone,
                    $user->roles->first()?->name ?? 'بدون نقش',
                ]),
        ];
    }

    protected function getServiceRows(): Collection
    {
        return collect()
            ->merge($this->getRowsForModel(Residence::class, 'اقامتگاه'))
            ->merge($this->getRowsForModel(Tour::class, 'تور'))
            ->merge($this->getRowsForModel(FoodStore::class, 'رستوران'))
            ->sortByDesc('timestamp')
            ->values();
    }

    protected function getRowsForModel(string $modelClass, string $serviceLabel): Collection
    {
        return $modelClass::query()
            ->with('admin:id,name,family')
            ->latest('id')
            ->limit(20)
            ->get()
            ->map(function ($item) use ($serviceLabel) {
                $createdAt = $item->created_at ?? now();

                return [
                    'service' => $serviceLabel,
                    'host' => $this->resolveFullName($item->admin),
                    'amount' => (int) ($item->amount ?? 0),
                    'date' => Jalalian::fromDateTime($createdAt)->format('%Y/%m/%d'),
                    'timestamp' => $createdAt->timestamp,
                ];
            });
    }

    protected function filterRowsByDate(Collection $rows): Collection
    {
        if (trim($this->dateFrom) !== '') {
            $from = $this->normalizeDate($this->dateFrom);
            $rows = $rows->filter(fn ($row) => $this->extractDateFromRow($row) >= $from);
        }

        if (trim($this->dateTo) !== '') {
            $to = $this->normalizeDate($this->dateTo);
            $rows = $rows->filter(fn ($row) => $this->extractDateFromRow($row) <= $to);
        }

        return $rows->values();
    }

    protected function extractDateFromRow(array $row): string
    {
        return (string) (collect($row)->first(
            fn ($value) => is_string($value) && preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $value)
        ) ?? '');
    }

    protected function downloadCsv(array $headers, Collection $rows, string $filename)
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, array_map(fn ($value) => (string) $value, $row));
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
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

    protected function getWithdrawStatusLabel(string $status): string
    {
        return match ($status) {
            'paid' => 'واریز شده',
            'rejected' => 'رد شده',
            default => 'در انتظار',
        };
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
            return 'بدون نام';
        }

        $fullName = trim(($user->name ?? '') . ' ' . ($user->family ?? ''));

        return $fullName !== '' ? $fullName : ('کاربر #' . $user->id);
    }


}
