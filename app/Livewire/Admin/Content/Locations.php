<?php

namespace App\Livewire\Admin\Content;

use App\Models\City;
use App\Models\PopularCity;
use App\Models\Province;
use Livewire\Component;
use Livewire\WithPagination;

class Locations extends Component
{
    use WithPagination;

    public string $activeTab = 'provinces';

    public string $provinceSearch = '';

    public string $provinceStatus = 'all';

    public string $citySearch = '';

    public string $cityProvince = 'all';

    public string $cityStatus = 'all';

    public string $popularSearch = '';

    public string $popularProvince = 'all';

    public string $popularStatus = 'all';

    public array $popularOrders = [];

    protected $queryString = [
        'activeTab' => ['except' => 'provinces'],
        'provinceSearch' => ['except' => ''],
        'provinceStatus' => ['except' => 'all'],
        'citySearch' => ['except' => ''],
        'cityProvince' => ['except' => 'all'],
        'cityStatus' => ['except' => 'all'],
        'popularSearch' => ['except' => ''],
        'popularProvince' => ['except' => 'all'],
        'popularStatus' => ['except' => 'all'],
    ];

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasPermissionBySlug(config('access-control.content_manage_permission')), 403);
    }

    public function render()
    {
        $provinces = Province::query()
            ->with('country')
            ->withCount(['cities', 'residences'])
            ->when(trim($this->provinceSearch) !== '', fn ($query) => $query->where('name', 'like', '%'.trim($this->provinceSearch).'%'))
            ->when($this->provinceStatus !== 'all', fn ($query) => $query->where('is_use', (bool) $this->provinceStatus))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10, ['*'], 'provincesPage');

        $cities = City::query()
            ->with('province')
            ->withCount('residences')
            ->when(trim($this->citySearch) !== '', fn ($query) => $query->where('name', 'like', '%'.trim($this->citySearch).'%'))
            ->when($this->cityProvince !== 'all', fn ($query) => $query->where('province_id', (int) $this->cityProvince))
            ->when($this->cityStatus !== 'all', fn ($query) => $query->where('is_use', (bool) $this->cityStatus))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10, ['*'], 'citiesPage');

        $popularCities = PopularCity::query()
            ->with('city.province')
            ->when(trim($this->popularSearch) !== '', function ($query) {
                $search = trim($this->popularSearch);

                $query->whereHas('city', fn ($city) => $city->where('name', 'like', '%'.$search.'%'));
            })
            ->when($this->popularProvince !== 'all', fn ($query) => $query->whereHas('city', fn ($city) => $city->where('province_id', (int) $this->popularProvince)))
            ->when($this->popularStatus !== 'all', fn ($query) => $query->where('status', (bool) $this->popularStatus))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(10, ['*'], 'popularPage');

        foreach ($popularCities as $popularCity) {
            $this->popularOrders[$popularCity->id] ??= (string) $popularCity->sort_order;
        }

        return view('livewire.admin.content.locations', [
            'provinces' => $provinces,
            'cities' => $cities,
            'popularCities' => $popularCities,
            'provinceOptions' => Province::query()->orderBy('name')->pluck('name', 'id'),
        ])
            ->extends('app')
            ->section('content');
    }

    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['provinces', 'cities', 'popular'], true)) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function updated($propertyName): void
    {
        if (str_starts_with($propertyName, 'province')) {
            $this->resetPage('provincesPage');
        }

        if (str_starts_with($propertyName, 'city')) {
            $this->resetPage('citiesPage');
        }

        if (str_starts_with($propertyName, 'popular')) {
            $this->resetPage('popularPage');
        }
    }

    public function toggleProvince(int $id): void
    {
        $province = Province::query()->find($id);
        if (! $province) {
            return;
        }

        $province->update(['is_use' => ! (bool) $province->is_use]);
        $this->dispatch('locations-saved');
    }

    public function toggleCity(int $id): void
    {
        $city = City::query()->find($id);
        if (! $city) {
            return;
        }

        $city->update(['is_use' => ! (bool) $city->is_use]);
        $this->dispatch('locations-saved');
    }

    public function togglePopularCity(int $id): void
    {
        $popularCity = PopularCity::query()->find($id);
        if (! $popularCity) {
            return;
        }

        $popularCity->update(['status' => ! (bool) $popularCity->status]);
        $this->dispatch('locations-saved');
    }

    public function savePopularOrder(): void
    {
        foreach ($this->popularOrders as $id => $order) {
            PopularCity::query()
                ->whereKey((int) $id)
                ->update([
                    'sort_order' => max(0, (int) preg_replace('/[^\d]+/', '', convertPersianToEnglishNumbers((string) $order))),
                ]);
        }

        $this->dispatch('locations-saved');
    }
}
