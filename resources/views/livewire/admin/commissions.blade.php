@push('head')
    <style>
        .commissions-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .commissions-card {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .commissions-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 18px 8px;
        }

        .commissions-head h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 800;
            color: var(--admin-text);
        }

        .commissions-head h3 i {
            color: var(--admin-primary);
        }

        .commissions-filters {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto;
            gap: 8px;
            padding: 0 18px 14px;
        }

        .commissions-empty-state {
            padding: 14px 18px 20px;
        }

        @media (max-width: 900px) {
            .commissions-head,
            .commissions-filters {
                padding-right: 14px;
                padding-left: 14px;
            }

            .commissions-filters {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section commissions-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-percent"></i>
                کمیسیون‌ها
            </h2>
        </div>
    </div>

    <div class="commissions-card">
        <div class="commissions-head">
            <h3>
                <i class="fa fa-percent"></i>
                کمیسیون‌ها
            </h3>
        </div>

        <div class="commissions-filters">
            <input type="text" wire:model.live.debounce.300ms="dateFrom" class="form-control" placeholder="از تاریخ">
            <input type="text" wire:model.live.debounce.300ms="dateTo" class="form-control" placeholder="تا تاریخ">
            <button type="button" class="btn btn-primary" wire:click="$refresh">فیلتر</button>
        </div>

        @if($rows->count() > 0)
            <table class="data-table" style="margin: 0; border-right: none; border-left: none; border-bottom: none;">
                <thead>
                <tr>
                    <th>کد رزرو</th>
                    <th>خدمت</th>
                    <th>میزبان</th>
                    <th>مبلغ پایه</th>
                    <th>درصد کمیسیون</th>
                    <th>مبلغ کمیسیون</th>
                    <th>تاریخ</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td>{{ $row['code'] }}</td>
                        <td>{{ $row['service'] }}</td>
                        <td>{{ $row['host'] }}</td>
                        <td>{{ number_format($row['base_amount']) }}</td>
                        <td>{{ $row['commission_percent'] }}٪</td>
                        <td>{{ number_format($row['commission_amount']) }}</td>
                        <td>{{ $row['date'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="admin-empty-state commissions-empty-state">
                <h4>کمیسیونی یافت نشد</h4>
                <p>بازه تاریخ را تغییر دهید یا ابتدا رزرو/سرویس قیمت‌دار ثبت شود.</p>
            </div>
        @endif
    </div>
</div>
