<div class="section">
    <h2>
        <i class="fas fa-user-check"></i>
        اتصال نقش به کارمند
    </h2>

    <div class="card">
        <div class="card-body">
            <div class="row flex-nowrap flex-row justify-content-between">
                <div style="margin: 4px">
                    <label>جستجوی کارمند:
                        <input type="text" wire:model.live="search" class="form-control" placeholder="نام، نام خانوادگی یا تلفن">
                    </label>
                </div>
            </div>
        </div>
    </div>

    <table class="data-table responsive-table">
        <thead>
        <tr>
            <th>کارمند</th>
            <th>نقش فعلی</th>
            <th>دسترسی‌ها</th>
            <th>تغییر نقش</th>
            <th>عملیات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($list as $item)
            @php
                $currentRole = $item->roles->first();
            @endphp
            <tr>
                <td data-label="کارمند">
                    {{ trim(($item->name ?? '') . ' ' . ($item->family ?? '')) ?: 'کاربر #' . $item->id }}
                </td>
                <td data-label="نقش فعلی">
                    @if($currentRole)
                        <span class="badge badge-purple">{{ $currentRole->name }}</span>
                    @else
                        <span class="badge badge-warning">بدون نقش</span>
                    @endif
                </td>
                <td data-label="دسترسی‌ها">{{ $this->getUserPermissionsSummary($item) }}</td>
                <td data-label="تغییر نقش">
                    <select wire:model="selectedRoles.{{ $item->id }}" class="form-select role-select">
                        <option value="">بدون نقش</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td data-label="عملیات">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="saveRole('{{ $item->id }}')">
                        ذخیره
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">کارمندی یافت نشد.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="card">
        <div class="card-body">
            {{ $list->links('vendor.pagination.default') }}
        </div>
    </div>

    @script
    <script>
        const assignToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        Livewire.on('saved', () => {
            assignToast.fire({
                icon: 'success',
                title: 'نقش کارمند با موفقیت ذخیره شد'
            });
        });
    </script>
    @endscript
</div>

