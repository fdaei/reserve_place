<?php

namespace App\Livewire\Admin;

use App\Models\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class Discounts extends Component
{
    public $editingId = null;
    public $code = '';
    public $value = '';
    public $scope = 'all';
    public $expiresAt = '';

    protected $listeners = ['remove'];

    public function render()
    {
        return view('livewire.admin.discounts', [
            'discounts' => $this->getDiscounts(),
        ])
            ->extends('app')
            ->section('content');
    }

    public function saveDiscount(): void
    {
        $this->validate([
            'code' => 'required|string|min:2|max:40',
            'value' => 'required|string|max:20',
            'scope' => 'required|string|max:30',
            'expiresAt' => 'nullable|string|max:20',
        ]);

        $discounts = $this->getStoredDiscounts();
        $id = $this->editingId ?: uniqid('discount_', true);
        $normalizedCode = strtoupper(trim($this->code));

        $duplicate = $discounts
            ->first(fn ($item) => strtoupper($item['code'] ?? '') === $normalizedCode && ($item['id'] ?? null) !== $id);

        if ($duplicate) {
            $this->addError('code', 'این کد تخفیف قبلاً ثبت شده است.');
            return;
        }

        $previous = $discounts->firstWhere('id', $id);
        $discount = [
            'id' => $id,
            'code' => $normalizedCode,
            'value' => $this->normalizeDiscountValue($this->value),
            'scope' => $this->scope,
            'expires_at' => $this->normalizeDate($this->expiresAt),
            'usage_count' => (int) ($previous['usage_count'] ?? 0),
            'status' => $previous['status'] ?? 'active',
            'created_at' => $previous['created_at'] ?? now()->toDateTimeString(),
        ];

        $this->storeDiscounts(
            $discounts
                ->reject(fn ($item) => ($item['id'] ?? null) === $id)
                ->prepend($discount)
                ->values()
        );

        $this->resetForm();
        $this->dispatch('saved');
    }

    public function editDiscount($id): void
    {
        $discount = $this->getDiscounts()->firstWhere('id', $id);
        if (!$discount) {
            return;
        }

        $this->editingId = $discount['id'];
        $this->code = $discount['code'];
        $this->value = (string) $discount['value'];
        $this->scope = $discount['scope'];
        $this->expiresAt = $discount['expires_at'];
    }

    public function toggleStatus($id): void
    {
        $discounts = $this->getDiscounts()
            ->map(function (array $discount) use ($id) {
                if (($discount['id'] ?? null) === $id) {
                    $discount['status'] = ($discount['status'] ?? 'active') === 'active' ? 'inactive' : 'active';
                }

                return $discount;
            });

        $this->storeDiscounts($discounts);
        $this->dispatch('saved');
    }

    public function remove($id): void
    {
        $this->storeDiscounts(
            $this->getDiscounts()
                ->reject(fn ($item) => ($item['id'] ?? null) === $id)
                ->values()
        );

        $this->dispatch('removed');
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->code = '';
        $this->value = '';
        $this->scope = 'all';
        $this->expiresAt = '';
        $this->resetErrorBag();
    }

    protected function getDiscounts(): Collection
    {
        $discounts = $this->getStoredDiscounts();

        if ($discounts->isEmpty()) {
            $discounts = collect([
                [
                    'id' => 'sample_welcome10',
                    'code' => 'WELCOME10',
                    'value' => '10٪',
                    'scope' => 'all',
                    'expires_at' => '1405/06/01',
                    'usage_count' => 45,
                    'status' => 'active',
                    'created_at' => now()->subDays(10)->toDateTimeString(),
                ],
            ]);
        }

        return $discounts
            ->values()
            ->map(function (array $discount) {
                $status = $discount['status'] ?? 'active';

                return [
                    'id' => $discount['id'] ?? uniqid('discount_', true),
                    'code' => $discount['code'] ?? '-',
                    'value' => $discount['value'] ?? '-',
                    'scope' => $discount['scope'] ?? 'all',
                    'scope_label' => $this->getScopeLabel($discount['scope'] ?? 'all'),
                    'expires_at' => $discount['expires_at'] ?? '-',
                    'usage_count' => (int) ($discount['usage_count'] ?? 0),
                    'status' => $status,
                    'status_meta' => $status === 'active'
                        ? ['label' => 'فعال', 'class' => 'active']
                        : ['label' => 'غیرفعال', 'class' => 'danger'],
                ];
            });
    }

    protected function getStoredDiscounts(): Collection
    {
        $stored = json_decode((string) (Config::where('title', 'admin_discount_codes')->value('value') ?? '[]'), true);

        return collect(is_array($stored) ? $stored : []);
    }

    protected function storeDiscounts(Collection $discounts): void
    {
        Config::updateOrCreate(
            ['title' => 'admin_discount_codes'],
            ['value' => json_encode($discounts->values()->all(), JSON_UNESCAPED_UNICODE)]
        );
    }

    protected function normalizeDiscountValue(string $value): string
    {
        $value = trim($value);
        if (function_exists('convertPersianToEnglishNumbers')) {
            $value = convertPersianToEnglishNumbers($value);
        }

        return str_contains($value, '%') || str_contains($value, '٪') ? $value : $value . '٪';
    }

    protected function normalizeDate(?string $date): string
    {
        $date = trim((string) $date);
        if ($date === '') {
            return '-';
        }

        if (function_exists('convertPersianToEnglishNumbers')) {
            $date = convertPersianToEnglishNumbers($date);
        }

        return str_replace('-', '/', $date);
    }

    protected function getScopeLabel(string $scope): string
    {
        return match ($scope) {
            'residence' => 'اقامتگاه',
            'tour' => 'تور',
            'foodstore' => 'رستوران',
            default => 'همه',
        };
    }


}
