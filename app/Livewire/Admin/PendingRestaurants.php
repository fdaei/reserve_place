<?php

namespace App\Livewire\Admin;

use App\Models\City;
use App\Models\FoodStore;
use App\Models\Images;
use App\Models\OptionValue;
use App\Models\Province;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class PendingRestaurants extends Component
{
    use WithPagination;

    public $search = '';
    public $province = 0;
    public $city = 0;



    public function render()
    {
        $query = FoodStore::query()
            ->where('status', 0)
            ->with([
                'province:id,name',
                'city:id,name',
                'admin:id,name,family,phone',
            ]);

        if ($this->search !== '') {
            $search = trim($this->search);

            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhere('id', $search)
                    ->orWhereHas('admin', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('family', 'like', '%' . $search . '%');
                    });
            });
        }

        if ((int) $this->province !== 0) {
            $query->where('province_id', $this->province);
        }

        if ((int) $this->city !== 0) {
            $query->where('city_id', $this->city);
        }

        return view('livewire.admin.pending-restaurants', [
            'list' => $query->latest('id')->paginate(10),
            'provinces' => Province::where('country_id', 1)->orderBy('name')->get(),
            'cities' => City::query()
                ->when((int) $this->province !== 0, fn ($cityQuery) => $cityQuery->where('province_id', $this->province))
                ->orderBy('name')
                ->get(),
            'pendingCount' => FoodStore::where('status', 0)->count(),
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName = null)
    {
        $this->resetPage();
    }

    public function updatedProvince()
    {
        $this->city = 0;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'province', 'city']);
        $this->resetPage();
    }

    public function approve($id)
    {
        $store = FoodStore::findOrFail($id);
        $store->update(['status' => 1]);

        session()->flash('admin_success', 'رستوران تایید شد و اکنون در سایت فعال است.');
        $this->resetPage();
    }

    public function edit($id)
    {
        $store = FoodStore::findOrFail($id);
        $user = User::findOrFail($store->user_id);

        Auth::logout();
        Auth::login($user);

        return redirect('/edit-foodstore/' . $store->id);
    }

    public function reject($id)
    {
        $store = FoodStore::with('images')->findOrFail($id);

        foreach ($store->images as $image) {
            if (Storage::disk('public')->exists('food_store/' . $image->url)) {
                Storage::disk('public')->delete('food_store/' . $image->url);
            }
        }

        Images::where('store_id', $id)->delete();
        OptionValue::where('foodstore_id', $id)->delete();
        $store->delete();

        session()->flash('admin_success', 'رستوران رد و از سامانه حذف شد.');
        $this->resetPage();
    }
}
