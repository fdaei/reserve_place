<?php

namespace App\Livewire\Admin;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PendingPartners extends Component
{
    use WithPagination;



    public function render()
    {
        return view('livewire.admin.pending-partners', [
            'list' => Friend::query()
                ->where('status', 0)
                ->with([
                    'country:id,name',
                    'province:id,name',
                    'admin:id,name,family,phone',
                ])
                ->latest('id')
                ->paginate(10),
            'pendingCount' => Friend::where('status', 0)->count(),
        ])
            ->extends('app')
            ->section('content');
    }

    public function approve($id)
    {
        $friend = Friend::findOrFail($id);
        $friend->update(['status' => 1]);

        session()->flash('admin_success', 'درخواست همسفر تایید شد و اکنون در سایت فعال است.');
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

    public function reject($id)
    {
        $friend = Friend::findOrFail($id);
        $friend->update(['status' => 2]);

        session()->flash('admin_success', 'درخواست همسفر رد شد و وضعیت آن در سامانه ذخیره شد.');
        $this->resetPage();
    }
}
