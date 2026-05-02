@push('head')
    <style>
        .reviews-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .reviews-table-card {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .reviews-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 18px 8px;
        }

        .reviews-head h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--admin-text);
        }

        .reviews-head h3 i {
            color: var(--admin-primary);
        }

        .reviews-toolbar {
            display: flex;
            justify-content: flex-end;
            padding: 0 18px 14px;
        }

        .reviews-toolbar .form-control {
            width: min(320px, 100%);
        }

        .reviews-score {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            color: #1f2937;
        }

        .reviews-score i {
            color: #f59e0b;
        }

        .reviews-text {
            color: #334155;
            font-size: 0.84rem;
        }

        .reviews-service,
        .reviews-user {
            color: #334155;
            font-size: 0.84rem;
            font-weight: 500;
        }

        .reviews-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .reviews-actions .btn:hover,
        .reviews-actions .btn:focus {
            transform: none;
            box-shadow: none;
        }

        .reviews-empty-state {
            padding: 14px 18px 20px;
        }

        @media (max-width: 900px) {
            .reviews-head,
            .reviews-toolbar {
                padding-right: 14px;
                padding-left: 14px;
            }

            .reviews-toolbar {
                justify-content: stretch;
            }

            .reviews-toolbar .form-control {
                width: 100%;
            }
        }
    </style>
@endpush

<div class="section reviews-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-star"></i>
                مدیریت نظرات
            </h2>
        </div>
    </div>

    <div class="reviews-table-card">
        <div class="reviews-head">
            <h3>
                <i class="fa fa-star"></i>
                مدیریت نظرات
            </h3>
        </div>

        <div class="reviews-toolbar">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="form-control"
                placeholder="جستجو کاربر، خدمت یا امتیاز"
            >
        </div>

        @if($list->count() > 0)
            <table class="data-table" style="margin: 0; border-right: none; border-left: none; border-bottom: none;">
                <thead>
                <tr>
                    <th>کاربر</th>
                    <th>خدمت</th>
                    <th>نوع</th>
                    <th>امتیاز</th>
                    <th>نظر</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $item)
                    <tr>
                        <td class="reviews-user">{{ $item->row_user_name }}</td>
                        <td class="reviews-service">{{ $item->row_service_name }}</td>
                        <td>{{ $item->row_type_label }}</td>
                        <td>
                            <span class="reviews-score">
                                <i class="fa fa-star"></i>
                                {{ $item->point }}
                            </span>
                        </td>
                        <td class="reviews-text">{{ $item->row_review_text }}</td>
                        <td>
                            <span class="status-chip {{ $item->row_status['class'] }}">
                                {{ $item->row_status['label'] }}
                            </span>
                        </td>
                        <td>
                            <div class="reviews-actions">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-warning"
                                    wire:click="setForm('edit','{{ $item->id }}')"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger reviews-remove-btn"
                                    data-id="{{ $item->id }}"
                                    data-title="{{ $item->row_service_name }}"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="card" style="border: none; border-top: 1px solid var(--admin-border); border-radius: 0;">
                <div class="card-body">
                    {{ $list->links('vendor.pagination.default') }}
                </div>
            </div>
        @else
            <div class="admin-empty-state reviews-empty-state">
                <h4>هنوز نظری ثبت نشده است</h4>
                <p>پس از ثبت نظر توسط کاربران، فهرست این بخش تکمیل می‌شود.</p>
            </div>
        @endif
    </div>

    <div class="modal fade {{ $form != 'empty' ? 'show' : '' }}" id="exampleModal" tabindex="-1"
         aria-labelledby="exampleModalLabel" aria-hidden="true"
         style="{{ $form != 'empty' ? 'display: block;' : '' }}">
        <div class="modal-dialog">
            <form wire:submit="{{ $form }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        ویرایش امتیاز
                    </h5>
                    <span wire:click="setForm('empty')" type="button" class="close"
                          data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </span>
                </div>
                <div class="modal-body">
                    <div style="margin: 4px">
                        <label>امتیاز:
                            <input type="number" wire:model="point" min="0" max="5" class="form-control">
                        </label>
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
        $(document).on("click", ".reviews-remove-btn", function () {
            let id = $(this).attr("data-id")
            let title = $(this).attr("data-title")
            Swal.fire({
                icon: "warning",
                title: 'هشدار',
                text: `از حذف کردن ${title} اطمینان دارید؟`,
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
