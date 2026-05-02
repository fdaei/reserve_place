@push('head')
    <style>
        .location-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .location-toolbar-card,
        .location-tree-card {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        }

        .location-toolbar-card {
            padding: 14px;
        }

        .location-toolbar-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .location-toolbar-row .form-control {
            flex: 1 1 auto;
        }

        .location-tree-card {
            padding: 18px;
        }

        .location-tree-stack {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .location-country-card {
            border: 1px solid var(--admin-border);
            border-radius: 16px;
            background: #f8fafc;
            padding: 14px;
        }

        .location-country-head,
        .location-province-item,
        .location-country-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .location-country-title,
        .location-province-name {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .location-country-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--admin-text);
        }

        .location-country-title i,
        .location-province-name i,
        .location-inline-link i {
            color: var(--admin-primary);
        }

        .location-province-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px dashed #dbe3ef;
        }

        .location-province-item {
            padding: 8px 0;
            border-bottom: 1px solid #edf2f7;
            color: #334155;
            font-size: 0.88rem;
        }

        .location-province-item:last-child {
            border-bottom: none;
        }

        .location-inline-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 10px;
            background: #e0f2fe;
            color: #0369a1;
            text-decoration: none;
        }

        .location-country-footer {
            justify-content: flex-start;
            margin-top: 14px;
        }

        .location-empty-compact {
            padding: 18px 16px;
            margin-top: 12px;
        }

        @media (max-width: 720px) {
            .location-toolbar-row {
                flex-wrap: wrap;
            }

            .location-toolbar-row .form-control {
                width: 100%;
                flex-basis: 100%;
            }
        }
    </style>
@endpush

<div class="section location-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-map-marker"></i>
                مدیریت موقعیت‌ها
            </h2>
        </div>
    </div>

    <div class="location-toolbar-card">
        <div class="location-toolbar-row">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="form-control"
                placeholder="جستجو کشور/استان/شهر"
            >
            <button type="button" class="btn btn-primary" wire:click="$refresh">جستجو</button>
            <button type="button" class="btn btn-success" wire:click="setForm('add')">کشور جدید</button>
        </div>
    </div>

    <div class="location-tree-card">
        <div class="location-tree-stack">
            @forelse($tree as $branch)
                <article class="location-country-card">
                    <div class="location-country-head">
                        <div class="location-country-title">
                            <i class="fa fa-flag"></i>
                            <span>{{ $branch['country']->name }}</span>
                        </div>

                        <div class="table-actions">
                            <button
                                type="button"
                                class="btn btn-sm btn-warning"
                                wire:click="setForm('edit', '{{ $branch['country']->id }}')"
                            >
                                <i class="fa fa-edit"></i>
                            </button>
                            <a href="{{ url('admin/provinces/' . $branch['country']->id) }}" class="btn btn-sm btn-info">
                                <i class="fa fa-link"></i>
                            </a>
                            <button
                                type="button"
                                class="btn btn-sm btn-danger location-remove-btn"
                                data-id="{{ $branch['country']->id }}"
                                data-title="{{ $branch['country']->name }}"
                            >
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    @if($branch['provinces']->isNotEmpty())
                        <div class="location-province-list">
                            @foreach($branch['provinces'] as $province)
                                <div class="location-province-item">
                                    <div class="location-province-name">
                                        <i class="fa fa-map-o"></i>
                                        <span>{{ $province->name }}</span>
                                    </div>

                                    <a
                                        href="{{ url('admin/provinces/' . $branch['country']->id) }}"
                                        class="location-inline-link"
                                        title="مدیریت استان‌های {{ $branch['country']->name }}"
                                    >
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="admin-empty-state location-empty-compact">
                            <h4>استانی ثبت نشده است</h4>
                            <p>برای این کشور هنوز استانی تعریف نشده است.</p>
                        </div>
                    @endif

                    <div class="location-country-footer">
                        <a href="{{ url('admin/provinces/' . $branch['country']->id) }}" class="btn btn-primary btn-sm">
                            استان جدید
                        </a>
                    </div>
                </article>
            @empty
                <div class="admin-empty-state">
                    <h4>موردی برای نمایش پیدا نشد</h4>
                    <p>جستجو را تغییر دهید یا یک کشور جدید ثبت کنید.</p>
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
                            افزودن کشور
                        @else
                            ویرایش کشور
                        @endif
                    </h5>
                    <span wire:click="setForm('empty')" type="button" class="close"
                          data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </span>
                </div>
                <div class="modal-body">
                    <div style="margin: 4px">
                        <label>نام کشور:
                            <input type="text" wire:model="name" class="form-control">
                        </label>
                        @error('name')
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
        $(document).on("click", ".location-remove-btn", function () {
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
