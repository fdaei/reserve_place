<?php

namespace App\Livewire\Admin;

use App\Models\Country;
use App\Models\Friend;
use App\Models\Images;
use App\Models\OptionValue;
use App\Models\Province;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class TravelPartners extends Component
{
    use WithPagination;

    public $search = '';
    public $country = 0;
    public $province = 0;
    public $incomeModel = 'all';
    public $status = 'all';
    public $sort = 'latest';



    public function render()
    {
        $query = Friend::query()
            ->with([
                'country:id,name',
                'province:id,name',
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

        if ((int) $this->country !== 0) {
            $query->where('country_id', $this->country);
        }

        if ($this->status === 'active') {
            $query->where('status', 1);
        } elseif ($this->status === 'pending') {
            $query->where('status', 0);
        }

        if ($this->incomeModel === 'paid') {
            $query->where('vip', 1);
        } elseif ($this->incomeModel === 'free') {
            $query->where('vip', 0);
        }

        switch ($this->sort) {
            case 'oldest':
                $query->orderBy('id');
                break;
            case 'popular':
                $query->orderByDesc('calls')->orderByDesc('id');
                break;
            default:
                $query->latest('id');
                break;
        }

        return view('livewire.admin.travel-partners', [
            'list' => $query->paginate(10),
            'countries' => Country::orderBy('name')->get(),
            'stats' => [
                'total' => Friend::count(),
                'active' => Friend::where('status', 1)->count(),
                'pending' => Friend::where('status', 0)->count(),
            ],
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName = null)
    {
        $this->resetPage();
    }

    public function updatedCountry()
    {
        $this->province = 0;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'country', 'province', 'incomeModel']);
        $this->status = 'all';
        $this->sort = 'latest';
        $this->resetPage();
    }

    public function approve($id)
    {
        $friend = Friend::findOrFail($id);
        $friend->update(['status' => 1]);

        session()->flash('admin_success', 'درخواست همسفر با موفقیت تایید شد.');
        $this->resetPage();
    }

    public function edit($id)
    {
        $friend = Friend::findOrFail($id);
        $user = User::findOrFail($friend->user_id);

        Auth::logout();
        Auth::login($user);

        return redirect('/edit-friend/' . $friend->id);
    }

    public function remove($id)
    {
        $friend = Friend::with('images')->findOrFail($id);

        foreach ($friend->images as $image) {
            if (Storage::disk('public')->exists('friends/' . $image->url)) {
                Storage::disk('public')->delete('friends/' . $image->url);
            }
        }

        Images::where('friend_id', $id)->delete();
        OptionValue::where('friend_id', $id)->delete();
        $friend->delete();

        session()->flash('admin_success', 'درخواست همسفر با موفقیت حذف شد.');
        $this->resetPage();
    }
}
