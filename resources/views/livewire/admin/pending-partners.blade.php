<div class="section listing-panel">
    <div class="listing-panel-head">
        <h2 class="listing-panel-title">
            <span class="listing-panel-icon">
                <i class="fa fa-clock-o"></i>
            </span>
            همسفرهای در انتظار تایید
        </h2>

        <span class="panel-counter">
            {{ convertEnglishToPersianNumbers($pendingCount) }} درخواست
        </span>
    </div>

    @if(session('admin_success'))
        <div class="admin-notice success">{{ session('admin_success') }}</div>
    @endif

    @if($list->count() > 0)
        <div class="listing-table-wrap">
            <table class="table responsive-table listing-table listing-table--pending">
                <thead>
                <tr>
                    <th>نام</th>
                    <th>کشور</th>
                    <th>تاریخ درخواست</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $item)
                    @php
                        $hostName = trim(($item->admin->name ?? '') . ' ' . ($item->admin->family ?? ''));
                        $jalaliDate = $item->created_at ? \Morilog\Jalali\Jalalian::fromDateTime($item->created_at) : null;
                    @endphp
                    <tr>
                        <td data-label="نام">
                            <strong class="listing-title">{{ $hostName !== '' ? $hostName : $item->title }}</strong>
                        </td>
                        <td data-label="کشور">
                            {{ $item->country->name ?? '-' }}
                        </td>
                        <td data-label="تاریخ درخواست">
                            @if($jalaliDate)
                                {{ convertEnglishToPersianNumbers($jalaliDate->format('%Y/%m/%d')) }}
                            @else
                                -
                            @endif
                        </td>
                        <td data-label="عملیات">
                            <div class="pending-actions">
                                <button type="button" class="pending-action-btn pending-action-btn--approve" wire:click="approve({{ $item->id }})">
                                    تایید
                                </button>
                                <button
                                    type="button"
                                    class="pending-action-btn pending-action-btn--reject"
                                    wire:click="reject({{ $item->id }})"
                                    wire:confirm="این درخواست همسفر رد و حذف می‌شود. ادامه می‌دهید؟"
                                >
                                    رد
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="listing-pagination">
            <div class="card">
                <div class="card-body">
                    {{ $list->links('vendor.pagination.default') }}
                </div>
            </div>
        </div>
    @else
        <div class="admin-empty-state">
            <h4>همسفر در انتظار بررسی وجود ندارد</h4>
            <p>همه درخواست‌های ثبت‌شده تعیین تکلیف شده‌اند.</p>
        </div>
    @endif
</div>
