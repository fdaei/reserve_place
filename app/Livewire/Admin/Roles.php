<?php

namespace App\Livewire\Admin;

use App\Support\Admin\Access\BootstrapsAccessManagement;
use App\Models\Role;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Roles extends Component
{
    use BootstrapsAccessManagement;
    use WithPagination;

    public $search = '';

    public $form = 'empty';

    public $id;

    public $name;

    protected $listeners = ['remove'];

    public function render()
    {
        $query = Role::query()->withCount('users');

        if (! empty($this->search)) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        $list = $query->orderBy('id', 'ASC')->paginate(10);

        return view('livewire.admin.roles', [
            'list' => $list,
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

    public function setForm($form, $id = null)
    {
        $this->form = $form;

        if ($form === 'edit' && $id) {
            $model = Role::find($id);
            if (! $model) {
                $this->setForm('empty');

                return;
            }
            $this->id = $id;
            $this->name = $model->name;

            return;
        }

        if ($form === 'add') {
            $this->id = null;
            $this->name = null;

            return;
        }

        if ($form === 'empty') {
            $this->id = null;
            $this->name = null;
        }
    }

    public function add()
    {
        $this->validate([
            'name' => 'required|string|min:2|max:80|unique:roles,name',
        ]);

        Role::create([
            'name' => trim($this->name),
            'slug' => $this->makeUniqueSlug($this->name),
        ]);

        $this->setForm('empty');
        $this->dispatch('create');
    }

    public function edit()
    {
        $this->validate([
            'name' => 'required|string|min:2|max:80|unique:roles,name,'.$this->id,
        ]);

        $model = Role::find($this->id);
        if (! $model) {
            $this->setForm('empty');

            return;
        }

        $model->update([
            'name' => trim($this->name),
            'slug' => $this->makeUniqueSlug($this->name, $this->id),
        ]);

        $this->setForm('empty');
        $this->dispatch('edited');
    }

    public function remove($id)
    {
        $role = Role::find($id);
        if (! $role) {
            return;
        }

        $role->users()->detach();
        $role->permissions()->detach();
        $role->delete();

        $this->dispatch('removed');
    }

    protected function makeUniqueSlug($name, $ignoreId = null)
    {
        $baseSlug = Str::slug($name);
        if (empty($baseSlug)) {
            $baseSlug = 'role';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (
            Role::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function mount()
    {
        $this->bootstrapAccessManagement();
    }
}
