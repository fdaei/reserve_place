@push('head')
    <style>
        .export-page {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .export-card {
            border-radius: 18px;
            border: 1px solid var(--admin-border);
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .export-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 18px 8px;
        }

        .export-head h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 800;
            color: var(--admin-text);
        }

        .export-head h3 i {
            color: var(--admin-primary);
        }

        .export-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            padding: 0 18px 18px;
        }

        .export-box {
            min-height: 164px;
            padding: 18px;
            border-radius: 14px;
            border: 1px solid #edf2f7;
            background: #f8fafc;
        }

        .export-box h4 {
            margin: 0 0 14px;
            color: #111827;
            font-size: 0.95rem;
            font-weight: 800;
        }

        .export-form {
            display: grid;
            gap: 8px;
        }

        .export-date-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .export-form .btn {
            justify-self: start;
        }

        @media (max-width: 900px) {
            .export-head,
            .export-grid {
                padding-right: 14px;
                padding-left: 14px;
            }

            .export-grid,
            .export-date-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="section export-page">
    <div class="admin-section-head">
        <div>
            <h2>
                <i class="fa fa-file-excel-o"></i>
                خروجی اکسل
            </h2>
        </div>
    </div>

    <div class="export-card">
        <div class="export-head">
            <h3>
                <i class="fa fa-file-excel-o"></i>
                خروجی اکسل
            </h3>
        </div>

        <div class="export-grid">
            <div class="export-box">
                <h4>گزارش مالی</h4>
                <form class="export-form" wire:submit="downloadFinancialExport">
                    <select wire:model="financialReport" class="form-control">
                        <option value="booking_requests">درخواست‌های رزرو</option>
                        <option value="bookings">رزروها</option>
                        <option value="withdraws">درخواست‌های برداشت</option>
                        <option value="commissions">کمیسیون‌ها</option>
                        <option value="wallet">کیف پول میزبان‌ها</option>
                    </select>

                    <div class="export-date-row">
                        <input type="text" wire:model="dateFrom" class="form-control" placeholder="از تاریخ">
                        <input type="text" wire:model="dateTo" class="form-control" placeholder="تا تاریخ">
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-download"></i>
                        دریافت اکسل
                    </button>
                </form>
            </div>

            <div class="export-box">
                <h4>گزارش کاربران</h4>
                <form class="export-form" wire:submit="downloadUsersExport">
                    <select wire:model="userReport" class="form-control">
                        <option value="users">لیست کاربران</option>
                        <option value="hosts">لیست میزبان‌ها</option>
                        <option value="employees">لیست کارمندان</option>
                    </select>

                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-download"></i>
                        دریافت اکسل
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
