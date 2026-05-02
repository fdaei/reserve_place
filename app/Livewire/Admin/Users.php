<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';

    protected $listeners = ['remove'];

    public function render()
    {
        $query = User::query()
            ->regularCustomers()
            ->with([
                'residences:id,user_id,vip',
                'tours:id,user_id,vip',
                'foodstores:id,user_id,vip',
                'friends:id,user_id,vip',
            ])
            ->withCount([
                'residences',
                'tours',
                'foodstores',
                'friends',
                'tickets',
            ]);

        if ($this->search !== '') {
            $search = trim($this->search);
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('family', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($this->statusFilter === 'active') {
            $query->where(function ($builder) {
                $builder
                    ->has('residences')
                    ->orHas('tours')
                    ->orHas('foodstores')
                    ->orHas('friends')
                    ->orHas('tickets');
            });
        }

        if ($this->statusFilter === 'pending') {
            $query->whereDoesntHave('residences')
                ->whereDoesntHave('tours')
                ->whereDoesntHave('foodstores')
                ->whereDoesntHave('friends')
                ->whereDoesntHave('tickets');
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);
        $list->setCollection(
            $list->getCollection()->map(function (User $user) {
                $user->row_status = $this->resolveStatusMeta($user);
                $user->row_model_usage = $this->resolveModelUsage($user);
                $user->row_full_name = $this->resolveFullName($user);
                return $user;
            })
        );

        return view('livewire.admin.users', [
            'list' => $list,
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'statusFilter'], true)) {
            $this->resetPage();
        }
    }

    public function login($id)
    {
        Auth::logout();
        $user = User::findOrFail($id);
        Auth::login($user);
        return redirect('profile');
    }

    public function remove($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        $this->dispatch('removed');
    }

    protected function resolveFullName(User $user): string
    {
        $fullName = trim(($user->name ?? '') . ' ' . ($user->family ?? ''));
        return $fullName !== '' ? $fullName : ('کاربر #' . $user->id);
    }

    protected function resolveStatusMeta(User $user): array
    {
        $activityCount = (int) ($user->residences_count ?? 0)
            + (int) ($user->tours_count ?? 0)
            + (int) ($user->foodstores_count ?? 0)
            + (int) ($user->friends_count ?? 0)
            + (int) ($user->tickets_count ?? 0);

        return [
            'label' => $activityCount > 0 ? 'فعال' : 'در انتظار',
            'class' => $activityCount > 0 ? 'active' : 'pending',
        ];
    }

    protected function resolveModelUsage(User $user): string
    {
        $modes = collect();

        collect([$user->residences, $user->tours, $user->foodstores, $user->friends])
            ->flatten(1)
            ->each(function ($item) use ($modes) {
                $modes->push((int) ($item->vip ?? 0) === 1 ? 'دلاری' : 'رایگان');
            });

        if ($modes->isEmpty()) {
            return 'رایگان';
        }

        return $modes->unique()->values()->implode('، ');
    }


}
