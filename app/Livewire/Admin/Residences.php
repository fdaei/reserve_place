<?php

namespace App\Livewire\Admin;

use App\Models\City;
use App\Models\Images;
use App\Models\OptionValue;
use App\Models\Province;
use App\Models\Residence;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Residences extends Component
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
        $query = Residence::query()
            ->with([
                'province:id,name',
                'city:id,name',
                'admin:id,name,family,phone',
            ]);

        if ($this->search !== '') {
            $search = trim($this->search);

            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
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
            $query->where('vip', true);
        } elseif ($this->incomeModel === 'free') {
            $query->where('vip', false);
        }

        if ($this->status === 'active') {
            $query->where('status', true);
        } elseif ($this->status === 'pending') {
            $query->where('status', false);
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

        $list = $query->paginate(10);

        return view('livewire.admin.residences', [
            'list' => $list,
            'provinces' => Province::where('country_id', 1)->orderBy('name')->get(),
            'cities' => City::query()
                ->when((int) $this->province !== 0, fn ($cityQuery) => $cityQuery->where('province_id', $this->province))
                ->orderBy('name')
                ->get(),
            'stats' => [
                'total' => Residence::count(),
                'active' => Residence::where('status', true)->count(),
                'pending' => Residence::where('status', false)->count(),
            ],
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName)
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
        $residence = Residence::findOrFail($id);
        $residence->update(['status' => true]);

        session()->flash('admin_success', 'اقامتگاه با موفقیت تایید شد.');
    }

    public function edit($id)
    {
        $residence = Residence::findOrFail($id);
        $user = User::findOrFail($residence->user_id);

        Auth::logout();
        Auth::login($user);

        return redirect('/edit-residence/' . $residence->id);
    }

    public function remove($id)
    {
        $residence = Residence::with('images')->findOrFail($id);

        foreach ($residence->images as $image) {
            if (Storage::disk('public')->exists('residences/' . $image->url)) {
                Storage::disk('public')->delete('residences/' . $image->url);
            }
        }

        Images::where('residence_id', $id)->delete();
        OptionValue::where('residence_id', $id)->delete();
        $residence->delete();

        session()->flash('admin_success', 'اقامتگاه با موفقیت حذف شد.');
        $this->resetPage();
    }
}
