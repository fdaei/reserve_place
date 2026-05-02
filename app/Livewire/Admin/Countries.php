<?php

namespace App\Livewire\Admin;

use App\Models\City;
use App\Models\Country;
use App\Models\Province;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class Countries extends Component
{
    public $search = '';
    public $form = 'empty';
    public $id;
    public $name;

    public function render()
    {
        $search = trim($this->search);
        $matchedCountryIds = collect();
        $matchedProvinceIds = collect();

        if ($search !== '') {
            $matchedCountryIds = Country::query()
                ->where('name', 'like', '%' . $search . '%')
                ->pluck('id');

            $matchedProvinceIds = Province::query()
                ->where('name', 'like', '%' . $search . '%')
                ->pluck('id');

            $matchedProvinceIdsFromCities = City::query()
                ->where('name', 'like', '%' . $search . '%')
                ->pluck('province_id');

            $matchedProvinceIds = $matchedProvinceIds
                ->merge($matchedProvinceIdsFromCities)
                ->filter()
                ->unique()
                ->values();

            $matchedCountryIds = $matchedCountryIds
                ->merge(
                    Province::query()
                        ->whereIn('id', $matchedProvinceIds)
                        ->pluck('country_id')
                )
                ->filter()
                ->unique()
                ->values();
        }

        $countriesQuery = Country::query();
        if ($search !== '') {
            $countriesQuery->whereIn('id', $matchedCountryIds);
        }

        $countries = $countriesQuery->orderBy('name')->get();
        $countryIds = $countries->pluck('id');

        $allProvinces = Province::query()
            ->whereIn('country_id', $countryIds)
            ->orderBy('name')
            ->get()
            ->groupBy('country_id');

        $matchedProvinces = Province::query()
            ->whereIn('country_id', $countryIds)
            ->when($search !== '', fn ($query) => $query->whereIn('id', $matchedProvinceIds))
            ->orderBy('name')
            ->get()
            ->groupBy('country_id');

        $tree = $countries->map(function (Country $country) use ($search, $matchedCountryIds, $allProvinces, $matchedProvinces) {
            $provinces = $search !== '' && !$matchedCountryIds->contains($country->id)
                ? ($matchedProvinces->get($country->id) ?? collect())
                : ($allProvinces->get($country->id) ?? collect());

            return [
                'country' => $country,
                'provinces' => $provinces,
            ];
        });

        return view('livewire.admin.countries', [
            'tree' => $tree,
        ])
            ->extends('app')
            ->section('content');
    }

    protected $listeners = ['remove'];

    public function remove($id)
    {
        Country::findOrFail($id)->delete();
        $this->dispatch('removed');
    }

    public function setForm($form, $id = null)
    {
        $this->form = $form;

        if ($form === 'edit') {
            $model = Country::findOrFail($id);
            $this->id = $id;
            $this->name = $model->name;
            return;
        }

        $this->id = null;
        $this->name = '';
    }

    public function add()
    {
        $this->validate([
            'name' => 'required|string|min:2|max:80',
        ]);

        Country::create([
            'name' => trim($this->name),
        ]);

        $this->setForm('empty');
        $this->dispatch('create');
    }

    public function edit()
    {
        $this->validate([
            'name' => 'required|string|min:2|max:80',
        ]);

        Country::findOrFail($this->id)->update([
            'name' => trim($this->name),
        ]);

        $this->setForm('empty');
        $this->dispatch('edited');
    }


}
