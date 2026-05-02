<?php

namespace App\Livewire\Admin;

use App\Models\City;
use App\Models\Images;
use App\Models\Province;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Tours extends Component
{
    use WithPagination;

    public $search = '';
    public $province = 0;
    public $city = 0;
    public $incomeModel = 'all';
    public $status = 'all';
    public $sort = 'latest';



    public function render()
    {
        $query = Tour::query()
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

        if ($this->incomeModel === 'paid') {
            $query->where('vip', 1);
        } elseif ($this->incomeModel === 'free') {
            $query->where('vip', 0);
        }

        if ($this->status === 'active') {
            $query->where('status', 1);
        } elseif ($this->status === 'pending') {
            $query->where('status', 0);
        }

        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('id');
                break;
            case 'cheap':
                $query->orderBy('amount')->orderByDesc('id');
                break;
            case 'expensive':
                $query->orderByDesc('amount')->orderByDesc('id');
                break;
            case 'popular':
                $query->orderByDesc('calls')->orderByDesc('id');
                break;
            default:
                $query->latest('id');
                break;
        }

        return view('livewire.admin.tours', [
            'list' => $query->paginate(10),
            'provinces' => Province::where('country_id', 1)->orderBy('name')->get(),
            'cities' => City::query()
                ->when((int) $this->province !== 0, fn ($cityQuery) => $cityQuery->where('province_id', $this->province))
                ->orderBy('name')
                ->get(),
            'stats' => [
                'total' => Tour::count(),
                'active' => Tour::where('status', 1)->count(),
                'pending' => Tour::where('status', 0)->count(),
            ],
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
        $this->reset(['search', 'province', 'city', 'incomeModel']);
        $this->status = 'all';
        $this->sort = 'latest';
        $this->resetPage();
    }

    public function approve($id)
    {
        $tour = Tour::findOrFail($id);
        $tour->update(['status' => 1]);

        session()->flash('admin_success', 'تور با موفقیت تایید شد.');
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

    public function remove($id)
    {
        $tour = Tour::with('images')->findOrFail($id);

        foreach ($tour->images as $image) {
            if (Storage::disk('public')->exists('tours/' . $image->url)) {
                Storage::disk('public')->delete('tours/' . $image->url);
            }
        }

        Images::where('tour_id', $id)->delete();
        $tour->delete();

        session()->flash('admin_success', 'تور با موفقیت حذف شد.');
        $this->resetPage();
    }
}
