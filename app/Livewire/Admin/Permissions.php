<?php

namespace App\Livewire\Admin;

use App\Support\Admin\Access\BootstrapsAccessManagement;
use App\Models\Permission;
use App\Models\Role;
use Livewire\Component;

class Permissions extends Component
{
    use BootstrapsAccessManagement;

    public $selectedRoleId = null;

    public $search = '';

    public $permissionStates = [];

    public function render()
    {
        $roles = Role::orderBy('id', 'ASC')->get();

        if (! $this->selectedRoleId && $roles->isNotEmpty()) {
            $this->selectedRoleId = $roles->first()->id;
            $this->loadRolePermissions();
        }

        $query = Permission::query();
        if (! empty($this->search)) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        $permissions = $query->orderBy('id', 'ASC')->get();

        foreach ($permissions as $permission) {
            if (! array_key_exists($permission->id, $this->permissionStates)) {
                $this->permissionStates[$permission->id] = false;
            }
        }

        return view('livewire.admin.permissions', [
            'roles' => $roles,
            'permissions' => $permissions,
        ])
            ->extends('app')
            ->section('content');
    }

    public function updatedSelectedRoleId()
    {
        $this->loadRolePermissions();
    }

    public function selectRole($roleId)
    {
        $this->selectedRoleId = $roleId;
        $this->loadRolePermissions();
    }

    public function savePermissions()
    {
        $this->validate([
            'selectedRoleId' => 'required|exists:roles,id',
        ]);

        $permissionIds = collect($this->permissionStates)
            ->filter(fn ($value) => (bool) $value)
            ->keys()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if ($permissionIds !== []) {
            $loginPermissionId = Permission::query()
                ->where('slug', config('access-control.admin_login_permission'))
                ->value('id');

            if ($loginPermissionId) {
                $permissionIds = collect($permissionIds)
                    ->push((int) $loginPermissionId)
                    ->unique()
                    ->values()
                    ->all();
            }
        }

        $role = Role::find($this->selectedRoleId);
        if (! $role) {
            return;
        }

        $role->permissions()->sync($permissionIds);
        $this->dispatch('saved');
    }

    protected function loadRolePermissions()
    {
        $this->permissionStates = [];

        if (! $this->selectedRoleId) {
            return;
        }

        $role = Role::find($this->selectedRoleId);
        if (! $role) {
            return;
        }

        $assignedPermissionIds = $role->permissions()->pluck('permissions.id')->toArray();
        foreach ($assignedPermissionIds as $permissionId) {
            $this->permissionStates[$permissionId] = true;
        }
    }

    public function mount()
    {
        $this->bootstrapAccessManagement();
    }
}
