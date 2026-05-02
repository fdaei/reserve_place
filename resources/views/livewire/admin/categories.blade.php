@push('head')
    <style>
        .amenity-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .amenity-toolbar-card,
        .amenity-list-card {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        }

        .amenity-toolbar-card {
            padding: 14px;
        }

        .amenity-toolbar-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .amenity-toolbar-row .form-control {
            flex: 1 1 auto;
        }

        .amenity-type-note {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            color: var(--admin-muted);
            font-size: 0.78rem;
        }

        .amenity-list-card {
            padding: 18px;
        }

        .amenity-items {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .amenity-item {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 42px;
            padding: 8px 12px;
            border-radius: 12px;
            border: 1px solid var(--admin-border);
            background: #f8fafc;
            color: #1e293b;
        }

        .amenity-item-title {
            font-size: 0.87rem;
            font-weight: 600;
        }

        .amenity-item-meta {
            color: var(--admin-muted);
            font-size: 0.72rem;
        }

        .amenity-item-actions {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .amenity-action-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 9px;
            background: #e0f2fe;
            color: #0369a1;
            text-decoration: none;
        }

        @media (max-width: 720px) {
            .amenity-toolbar-row {
                flex-wrap: wrap;
            }

            .amenity-toolbar-row .form-control {
                width: 100%;
                flex-basis: 100%;
            }
        }
    </style>
@endpush

<div class="section amenity-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-tags"></i>
                مدیریت امکانات
            </h2>
            <span class="amenity-type-note">
                <i class="fa fa-circle"></i>
                نوع محتوا: {{ $typeLabel }}
            </span>
        </div>
    </div>

    <div class="amenity-toolbar-card">
        <div class="amenity-toolbar-row">
            <input
                type="text"
                wire:model="title"
                class="form-control"
                placeholder="نام امکان جدید (مثلاً استخر)"
            >
            <button type="button" class="btn btn-success" wire:click="add">افزودن</button>
        </div>
        @error('title')
            <div class="text-danger text-error" style="margin-top: 10px;">{{ $message }}</div>
        @enderror
    </div>

    <div class="amenity-list-card">
        <div class="amenity-items">
            @forelse($list as $item)
                <div class="amenity-item">
                    <span class="amenity-item-title">{{ $item->title }}</span>
                    <span class="amenity-item-meta">{{ $item->option_count }} مورد</span>

                    <div class="amenity-item-actions">
                        <button
                            type="button"
                            class="btn btn-sm btn-warning"
                            wire:click="setForm('edit','{{ $item->id }}')"
                        >
                            <i class="fa fa-edit"></i>
                        </button>
                        @if($type == 'residence')
                            <a href="{{ url('admin/tools/' . $item->id) }}" class="amenity-action-link">
                                <i class="fa fa-list"></i>
                            </a>
                        @elseif($type == 'foodstore')
                            <a href="{{ url('admin/tools-foodstore/' . $item->id) }}" class="amenity-action-link">
                                <i class="fa fa-list"></i>
                            </a>
                        @elseif($type == 'friend')
                            <a href="{{ url('admin/tools-friends/' . $item->id) }}" class="amenity-action-link">
                                <i class="fa fa-list"></i>
                            </a>
                        @endif
                        <button
                            type="button"
                            class="btn btn-sm btn-danger amenity-remove-btn"
                            data-id="{{ $item->id }}"
                            data-title="{{ $item->title }}"
                        >
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="admin-empty-state" style="width: 100%;">
                    <h4>هنوز امکانی ثبت نشده است</h4>
                    <p>اولین مورد را از نوار بالای صفحه اضافه کنید.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade {{ $form != 'empty' ? 'show' : '' }}" id="exampleModal" tabindex="-1"
         aria-labelledby="exampleModalLabel" aria-hidden="true"
         style="{{ $form != 'empty' ? 'display: block;' : '' }}">
        <div class="modal-dialog">
            <form wire:submit="{{ $form }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        @if($form == 'add')
                            افزودن امکان
                        @else
                            ویرایش امکان
                        @endif
                    </h5>
                    <span wire:click="setForm('empty')" type="button" class="close"
                          data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </span>
                </div>
                <div class="modal-body">
                    <div style="margin: 4px">
                        <label>نام امکان:
                            <input type="text" wire:model="title" class="form-control">
                        </label>
                        @error('title')
                            <div class="text-danger text-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <span wire:click="setForm('empty')" type="button" class="btn btn-secondary"
                          data-dismiss="modal">لغو</span>
                    <button class="btn btn-primary">ذخیره</button>
                </div>
            </form>
        </div>
    </div>

    @script
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
        $(document).on("click", ".amenity-remove-btn", function () {
            let id = $(this).attr("data-id")
            let title = $(this).attr("data-title")
            Swal.fire({
                icon: "warning",
                title: 'هشدار',
                text: `از حذف کردن  ${title} اطمینان دارید؟`,
                confirmButtonText: "لغو",
                denyButtonText: "حذف کردن",
                showDenyButton: true,
                background: '#333',
                color: '#fff',
                confirmButtonColor: '#3085d6',
            }).then(res => {
                if (res.isDenied) {
                    Livewire.dispatch("remove", {id: id});
                }
            })
        });

        Livewire.on("edited", event => {
            Toast.fire({
                icon: 'success',
                title: 'اطلاعات با موفقیت بروز شد'
            })
        })
        Livewire.on("create", event => {
            Toast.fire({
                icon: 'success',
                title: 'اطلاعات موفقیت ثبت شد'
            })
        })

        Livewire.on("removed", event => {
            Toast.fire({
                icon: 'success',
                title: 'سطر با موفقیت حذف شد'
            })
        })
    </script>
    @endscript

</div>
