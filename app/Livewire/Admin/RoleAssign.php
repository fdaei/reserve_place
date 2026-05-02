<?php

namespace App\Livewire\Admin;

use App\Support\Admin\Access\BootstrapsAccessManagement;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class RoleAssign extends Component
{
    use BootstrapsAccessManagement;
    use WithPagination;

    public $search = '';

    public $selectedRoles = [];

    public function render()
    {
        $roles = Role::orderBy('id', 'ASC')->get();

        $query = User::query()->with(['roles.permissions']);
        if (! empty($this->search)) {
            $query->where(function ($builder) {
                $builder->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('family', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%');
            });
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);

        foreach ($list as $user) {
            if (! array_key_exists($user->id, $this->selectedRoles)) {
                $this->selectedRoles[$user->id] = $user->roles->first()?->id ?? '';
            }
        }

        return view('livewire.admin.role-assign', [
            'list' => $list,
            'roles' => $roles,
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'search') {
            $this->resetPage();
        }
    }

    public function saveRole($userId)
    {
        $roleId = $this->selectedRoles[$userId] ?? null;

        $user = User::find($userId);
        if (! $user) {
            return;
        }

        if (empty($roleId)) {
            $user->roles()->detach();
            $this->dispatch('saved');

            return;
        }

        $this->validate([
            'selectedRoles.'.$userId => 'required|exists:roles,id',
        ]);

        $user->roles()->sync([(int) $roleId]);
        $this->dispatch('saved');
    }

    public function getUserPermissionsSummary($user)
    {
        $permissions = $user->roles
            ->flatMap(fn ($role) => $role->permissions->pluck('name'))
            ->unique()
            ->values();

        if ($permissions->isEmpty()) {
            return 'بدون دسترسی';
        }

        $summary = $permissions->take(3)->implode('، ');
        if ($permissions->count() > 3) {
            $summary .= ' ...';
        }

        return $summary;
    }

    public function mount()
    {
        $this->bootstrapAccessManagement();
    }
}
