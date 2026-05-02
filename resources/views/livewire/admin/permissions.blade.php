<div class="permissions-page">
    <div class="section active-section permissions-section" id="permissions">
        <h3 class="permissions-title">
            <i class="fa fa-key"></i>
            مدیریت مجوزها
        </h3>

        <form wire:submit="savePermissions" class="permissions-form">
            <div class="permission-tabs">
                @foreach($roles as $role)
                    <button
                        type="button"
                        wire:click="selectRole('{{ $role->id }}')"
                        @class([
                            'permission-tab',
                            'active' => (string)$selectedRoleId === (string)$role->id,
                        ])
                    >
                        {{ $role->name }}
                    </button>
                @endforeach
            </div>

            <div class="permission-matrix">
                @forelse($permissions as $permission)
                    <div class="role-row">
                        <div class="role-name">{{ $permission->name }}</div>

                        <div class="permissions">
                            <label class="permission-item">
                                <input type="checkbox" wire:model="permissionStates.{{ $permission->id }}">
                            </label>
                        </div>
                    </div>
                @empty
                    <div class="admin-empty-state">
                        <h4>مجوزی یافت نشد</h4>
                        <p>هنوز مجوزی برای نمایش وجود ندارد.</p>
                    </div>
                @endforelse
            </div>

            <div class="permission-actions">
                <button class="btn btn-primary permission-save-btn">ذخیره تغییرات</button>
            </div>
        </form>
    </div>

    @script
    <script>
        const permissionToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        Livewire.on('saved', () => {
            permissionToast.fire({
                icon: 'success',
                title: 'مجوزها با موفقیت ذخیره شدند'
            });
        });
    </script>
    @endscript
</div>
