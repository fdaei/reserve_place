<?php

namespace App\Livewire\Admin;

use App\Models\City;
use App\Models\Province;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PendingTours extends Component
{
    use WithPagination;

    public $search = '';
    public $province = 0;
    public $city = 0;



    public function render()
    {
        $query = Tour::query()
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

        return view('livewire.admin.pending-tours', [
            'list' => $query->latest('id')->paginate(10),
            'provinces' => Province::where('country_id', 1)->orderBy('name')->get(),
            'cities' => City::query()
                ->when((int) $this->province !== 0, fn ($cityQuery) => $cityQuery->where('province_id', $this->province))
                ->orderBy('name')
                ->get(),
            'pendingCount' => Tour::where('status', 0)->count(),
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
        $tour = Tour::findOrFail($id);
        $tour->update(['status' => 1]);

        session()->flash('admin_success', 'تور تایید شد و اکنون در سایت فعال است.');
        $this->resetPage();
    }

    public function edit($id)
    {
        $tour = Tour::findOrFail($id);
        $user = User::findOrFail($tour->user_id);

        Auth::logout();
        Auth::login($user);

        return redirect('/edit-tour/' . $tour->id);
    }

    public function reject($id)
    {
        $tour = Tour::findOrFail($id);
        $tour->update(['status' => 2]);

        session()->flash('admin_success', 'تور رد شد و وضعیت آن در سامانه ذخیره شد.');
        $this->resetPage();
    }
}
