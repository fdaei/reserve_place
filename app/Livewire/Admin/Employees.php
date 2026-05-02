<?php

namespace App\Livewire\Admin;

use App\Support\Admin\Access\BootstrapsAccessManagement;
use App\Models\Role;
use App\Models\User;
use App\Rules\NationalCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Livewire\WithPagination;

class Employees extends Component
{
    use WithPagination;
    use BootstrapsAccessManagement;

    public $search = '';
    public $roleFilter = 'all';
    public $form = 'empty';
    public $id;
    public $name = '';
    public $family = '';
    public $phone = '';
    public $nationalCode = '';
    public $selectedRole = '';

    protected $listeners = ['remove'];

    protected array $validationAttributes = [
        'name' => 'نام',
        'family' => 'نام خانوادگی',
        'phone' => 'موبایل',
        'nationalCode' => 'کد ملی',
        'selectedRole' => 'نقش',
    ];

    public function render()
    {
        $roles = Role::orderBy('id')->get();

        $query = User::query()->with(['roles.permissions'])->whereHas('roles');

        if ($this->search !== '') {
            $search = trim($this->search);
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('family', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($this->roleFilter !== 'all') {
            $query->whereHas('roles', fn ($builder) => $builder->where('id', $this->roleFilter));
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);
        $list->setCollection(
            $list->getCollection()->map(function (User $user) {
                $user->row_full_name = $this->resolveFullName($user);
                $user->row_role_name = $user->roles->first()?->name ?? 'بدون نقش';
                $user->row_permissions = $this->getUserPermissionsSummary($user);
                $user->row_status = ['label' => 'آنلاین', 'class' => 'active'];
                $user->row_last_active = $this->formatLastActive($user->updated_at ?? $user->created_at);
                return $user;
            })
        );

        return view('livewire.admin.employees', [
            'list' => $list,
            'roles' => $roles,
        ])
            ->extends('app')
            ->section('content');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'roleFilter'], true)) {
            $this->resetPage();
        }
    }

    public function setForm($form, $id = null)
    {
        $this->form = $form;

        if ($form === 'edit' && $id) {
            $user = User::with('roles')->findOrFail($id);
            $this->id = $user->id;
            $this->name = $user->name ?? '';
            $this->family = $user->family ?? '';
            $this->phone = $user->phone ?? '';
            $this->nationalCode = $this->normalizeDigits($user->national_code ?? '');
            $this->selectedRole = (string) ($user->roles->first()?->id ?? '');
            return;
        }

        $this->id = null;
        $this->name = '';
        $this->family = '';
        $this->phone = '';
        $this->nationalCode = '';
        $this->selectedRole = '';
    }

    public function add()
    {
        $this->normalizeEmployeeInput();

        $this->validate([
            'name' => 'nullable|string|max:80',
            'family' => 'nullable|string|max:80',
            'phone' => 'required|string|max:30|unique:users,phone',
            'nationalCode' => ['nullable', 'digits:10', new NationalCode()],
            'selectedRole' => 'required|exists:roles,id',
        ], $this->validationMessages());

        $user = User::create([
            'name' => trim($this->name),
            'family' => trim($this->family),
            'phone' => trim($this->phone),
            'national_code' => trim($this->nationalCode),
        ]);

        $user->roles()->sync([(int) $this->selectedRole]);

        $this->setForm('empty');
        $this->dispatch('create');
    }

    public function edit()
    {
        $this->normalizeEmployeeInput();

        $this->validate([
            'name' => 'nullable|string|max:80',
            'family' => 'nullable|string|max:80',
            'phone' => 'required|string|max:30|unique:users,phone,' . $this->id,
            'nationalCode' => ['nullable', 'digits:10', new NationalCode()],
            'selectedRole' => 'required|exists:roles,id',
        ], $this->validationMessages());

        $user = User::findOrFail($this->id);
        $user->update([
            'name' => trim($this->name),
            'family' => trim($this->family),
            'phone' => trim($this->phone),
            'national_code' => trim($this->nationalCode),
        ]);

        $user->roles()->sync([(int) $this->selectedRole]);

        $this->setForm('empty');
        $this->dispatch('edited');
    }

    public function remove($id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach();
        $user->delete();
        $this->dispatch('removed');
    }

    protected function resolveFullName(User $user): string
    {
        $fullName = trim(($user->name ?? '') . ' ' . ($user->family ?? ''));
        return $fullName !== '' ? $fullName : ('کاربر #' . $user->id);
    }

    protected function getUserPermissionsSummary(User $user): string
    {
        $permissions = $user->roles
            ->flatMap(fn ($role) => $role->permissions->pluck('name'))
            ->unique()
            ->values();

        if ($permissions->isEmpty()) {
            return 'بدون دسترسی';
        }

        $summary = $permissions->take(2)->implode(' و ');
        if ($permissions->count() > 2) {
            $summary .= ' ...';
        }

        return $summary;
    }

    protected function formatLastActive(?Carbon $date): string
    {
        if (!$date) {
            return 'نامشخص';
        }

        $minutes = $date->diffInMinutes(now());
        if ($minutes < 1) {
            return 'اکنون';
        }

        if ($minutes < 60) {
            return $minutes . ' دقیقه پیش';
        }

        $hours = (int) floor($minutes / 60);
        if ($hours < 24) {
            return $hours . ' ساعت پیش';
        }

        $days = (int) floor($hours / 24);
        return $days . ' روز پیش';
    }

    public function mount()
    {
        $this->bootstrapAccessManagement();
    }

    protected function normalizeEmployeeInput(): void
    {
        $this->phone = $this->normalizeDigits($this->phone);
        $this->nationalCode = $this->normalizeDigits($this->nationalCode);
    }

    protected function normalizeDigits(?string $value): string
    {
        return preg_replace('/\D+/', '', convertPersianToEnglishNumbers((string) $value)) ?? '';
    }

    protected function validationMessages(): array
    {
        return [
            'required' => 'وارد کردن :attribute الزامی است.',
            'exists' => ':attribute انتخاب شده معتبر نیست.',
            'unique' => ':attribute قبلاً ثبت شده است.',
            'digits' => ':attribute باید دقیقاً ۱۰ رقم باشد.',
            'max' => ':attribute بیشتر از مقدار مجاز است.',
            'string' => ':attribute باید متن معتبر باشد.',
        ];
    }
}
