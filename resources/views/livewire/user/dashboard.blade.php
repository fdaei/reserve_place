<div>
    <style>
        :root {
            --primary: #66ccff;
            --secondary: #0A2B4E;
            --accent: #F59E0B;
            --gray-bg: #F8FAFC;
            --gray-text: #475569;
            --border: #E2E8F0;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
        }
        
        .dashboard-container {
            padding: 20px 0 40px;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }
        
        .dashboard-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--secondary);
            margin: 0;
        }
        
        .logout-btn {
            background: transparent;
            border: 1px solid var(--danger);
            color: var(--danger);
            padding: 8px 20px;
            border-radius: 40px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .logout-btn:hover {
            background: var(--danger);
            color: white;
        }
        
        /* کارت کیف پول - شبیه کارت بانکی */
        .wallet-card {
            background: linear-gradient(135deg, #1E3A5F 0%, #0F2B45 100%);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
        }
        
        .wallet-card::before {
            content: "💳";
            position: absolute;
            bottom: -20px;
            right: -20px;
            font-size: 120px;
            opacity: 0.05;
            pointer-events: none;
        }
        
        .card-chip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        
        .chip-icon {
            width: 50px;
            height: 40px;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            border-radius: 10px;
            position: relative;
        }
        
        .chip-icon::before {
            content: "";
            position: absolute;
            top: 8px;
            left: 8px;
            width: 20px;
            height: 16px;
            background: #B8860B;
            border-radius: 3px;
        }
        
        .card-type {
            color: rgba(255,255,255,0.5);
            font-size: 12px;
            letter-spacing: 2px;
        }
        
        .card-balance {
            margin-bottom: 24px;
        }
        
        .balance-label {
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            margin-bottom: 4px;
        }
        
        .balance-amount {
            font-size: 32px;
            font-weight: 800;
            color: white;
        }
        
        .balance-amount small {
            font-size: 14px;
            font-weight: normal;
        }
        
        .card-number {
font-family: 'Courier New', monospace;
font-size: 22px;
letter-spacing: 3px;
    letter-spacing: 2px;
    color: white;
    margin-bottom: 20px;
    text-align: center;
    direction: ltr;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}
        
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        
        .card-expiry {
            color: rgba(255,255,255,0.5);
            font-size: 12px;
        }
        
        .wallet-actions {
            display: flex;
            gap: 10px;
        }
        
        .wallet-btn-small {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 6px 16px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .wallet-btn-small:hover {
            background: rgba(255,255,255,0.25);
        }
        
        /* منوی اصلی */
        .main-menu {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }
        
        .menu-card {
            background: white;
            border-radius: 20px;
            padding: 20px 16px;
            text-align: center;
            text-decoration: none;
            border: 1px solid var(--border);
            transition: all 0.3s;
        }
        
        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -12px rgba(0,0,0,0.1);
            border-color: var(--primary);
        }
        
        .menu-icon {
            width: 55px;
            height: 55px;
            background: var(--gray-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }
        
        .menu-icon i {
            font-size: 26px;
            color: var(--primary);
        }
        
        .menu-card h4 {
            font-size: 15px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 6px;
        }
        
        .menu-card p {
            font-size: 11px;
            color: var(--gray-text);
        }
        
        /* پیام خوانده نشده */
        .unread-alert {
            background: #FEF3C7;
            border-radius: 16px;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }
        
        .read-messages-btn {
            background: var(--warning);
            color: #1E293B;
            border: none;
            padding: 6px 16px;
            border-radius: 40px;
            font-size: 12px;
            cursor: pointer;
        }
        
        /* تب‌ها */
        .tabs-container {
            background: white;
            border-radius: 20px;
            border: 1px solid var(--border);
            overflow: hidden;
        }
        
        .tabs-header {
            background: var(--gray-bg);
            padding: 12px 16px;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            scrollbar-width: thin;
            -webkit-overflow-scrolling: touch;
        }
        
        .tabs-header::-webkit-scrollbar {
            height: 3px;
        }
        
        .tabs-header::-webkit-scrollbar-track {
            background: var(--border);
            border-radius: 10px;
        }
        
        .tabs-header::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }
        
        .tab-btn {
            background: transparent;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-text);
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 40px;
            display: inline-block;
        }
        
        .tab-btn.active {
            background: var(--secondary);
            color: white;
        }
        
        .tab-content {
            padding: 24px;
        }
        
        .tab-pane {
            display: none;
        }
        
        .tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* لیست اقامتگاه‌ها */
        .properties-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .property-item {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            transition: all 0.3s;
        }
        
        .property-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -12px rgba(0,0,0,0.1);
        }
        
        .property-image {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }
        
        .property-info {
            padding: 16px;
        }
        
        .property-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 8px;
        }
        
        .property-stats {
            display: flex;
            gap: 12px;
            font-size: 12px;
            color: var(--gray-text);
            margin-bottom: 12px;
        }
        
        .property-stats i {
            color: var(--primary);
        }
        
        .property-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-sm {
            padding: 6px 14px;
            font-size: 12px;
            border-radius: 40px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            border: none;
        }
        
        .btn-outline {
            background: transparent;
            color: var(--secondary);
            border: 1px solid var(--border);
        }
        
        .btn-primary {
            background: var(--secondary);
            color: white;
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        /* درخواست‌ها و رزروها */
        .request-item, .reservation-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            padding: 16px;
            background: var(--gray-bg);
            border-radius: 16px;
            margin-bottom: 12px;
        }
        
        .request-residence, .reservation-residence {
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 6px;
        }
        
        .request-details, .reservation-details {
            display: flex;
            gap: 16px;
            font-size: 12px;
            color: var(--gray-text);
            flex-wrap: wrap;
        }
        
        .request-details i, .reservation-details i {
            color: var(--primary);
        }
        
        .request-status, .reservation-status {
            font-size: 12px;
            margin-top: 6px;
        }
        
        .request-status.pending, .reservation-status.pending {
            color: var(--warning);
        }
        
        .request-status.confirmed, .reservation-status.confirmed {
            color: var(--success);
        }
        
        .request-status.cancelled, .reservation-status.cancelled {
            color: var(--danger);
        }
        
        .request-amount, .reservation-amount {
            font-size: 18px;
            font-weight: 800;
            color: var(--secondary);
        }
        
        /* تراکنش‌ها */
        .transactions-list {
            margin-top: 16px;
        }
        
        .transaction-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid var(--border);
        }
        
        .transaction-icon {
            width: 45px;
            height: 45px;
            background: var(--gray-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .transaction-icon i {
            font-size: 20px;
        }
        
        .transaction-info {
            flex: 1;
        }
        
        .transaction-title {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 4px;
        }
        
        .transaction-date {
            font-size: 11px;
            color: var(--gray-text);
        }
        
        .transaction-amount {
            font-weight: 700;
            font-size: 15px;
        }
        
        .text-success { color: var(--success); }
        .text-danger { color: var(--danger); }
        
        /* تیکت‌ها */
        .tickets-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tickets-table th,
        .tickets-table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid var(--border);
        }
        
        .tickets-table th {
            background: var(--gray-bg);
            font-weight: 600;
            color: var(--secondary);
        }
        
        .status-warning { color: var(--warning); }
        .status-success { color: var(--success); }
        
        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: var(--gray-text);
        }
        
        .empty-state i {
            font-size: 48px;
            color: var(--border);
            margin-bottom: 12px;
            display: block;
        }
        
        /* راهنمای موبایل */
        .mobile-hint {
            display: none;
            text-align: center;
            font-size: 11px;
            color: var(--gray-text);
            padding: 8px;
            background: var(--gray-bg);
            border-radius: 40px;
            margin-bottom: 16px;
        }
        
        @media (max-width: 768px) {
            .main-menu {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .properties-list {
                grid-template-columns: 1fr;
            }
            
            .tickets-table {
                display: block;
                overflow-x: auto;
            }
            
            .mobile-hint {
                display: block;
            }
            
            .card-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    @php
        $user = auth()->user();
        $isHost = $user->residences->count() > 0;
        $ticketIds = \App\Models\Ticket::where('user_id', $user->id)->pluck('id')->toArray();
        $unreadCount = \App\Models\TicketChat::whereIn("ticket_id", $ticketIds)
            ->where("seen", 0)
            ->where("user_id", "!=", $user->id)
            ->count();
        
        // TODO: بعداً توسط برنامه‌نویس تکمیل شود
        $reservationRequests = collect();
        $myReservations = collect();
        $transactions = collect();
        
        $areas = \App\Models\SupportAreaTickets::all()->keyBy("id");
    @endphp

    <div class="dashboard-container">
        
        @if(session('message'))
            <script>Swal.fire({ icon: "success", title: "{{ session('message') }}", timer: 3000, showConfirmButton: false });</script>
            @php session()->forget('message'); @endphp
        @endif

        {{-- هدر --}}
        <div class="dashboard-header">
            <h2 class="dashboard-title"><i class="fa fa-dashboard" style="color: var(--primary);"></i> داشبورد</h2>
            <button class="logout-btn" wire:click="logout"><i class="fa fa-sign-out"></i> خروج</button>
        </div>

{{-- کارت بانکی زیبا --}}
<div class="wallet-card">
    <div class="card-chip">
        <div class="chip-icon"></div>
        <span class="card-type">INJAA CARD</span>
    </div>
    <div class="card-balance">
        <div class="balance-label">موجودی کیف پول</div>
        <div class="balance-amount">{{ number_format($user->wallet ?? 0) }} <small>تومان</small></div>
    </div>
    <div class="card-number">
        @php
            $phone = $user->phone ?? '09123456789';
            $phone = preg_replace('/[^0-9]/', '', $phone);
            $part1 = substr($phone, 0, 4);
            $part2 = substr($phone, 4, 4);
            $part3 = substr($phone, 8, 4);
        @endphp
        {{ $part1 }} {{ $part2 }} {{ $part3 }}
    </div>
    <div class="card-footer">
        <div class="card-expiry">اعتبار: نامحدود</div>
        <div class="wallet-actions">
            <button class="wallet-btn-small" wire:click="requestWithdraw"><i class="fa fa-arrow-down"></i> برداشت</button>
        </div>
    </div>
</div>

        {{-- منوی اصلی --}}
        <div class="main-menu">
            <a href="{{ url('add-residence') }}" class="menu-card">
                <div class="menu-icon"><i class="fa fa-home"></i></div>
                <h4>ثبت اقامتگاه</h4>
                <p>ثبت و مدیریت اقامتگاه جدید</p>
            </a>
            <a href="{{ url('profile') }}" class="menu-card">
                <div class="menu-icon"><i class="fa fa-user"></i></div>
                <h4>پروفایل من</h4>
                <p>ویرایش اطلاعات شخصی</p>
            </a>
        </div>

        {{-- پیام خوانده نشده --}}
        @if($unreadCount > 0)
            <div class="unread-alert">
                <div><i class="fa fa-bell"></i> شما {{ $unreadCount }} پیام خوانده نشده دارید</div>
                <button class="read-messages-btn" id="readMessagesBtn"><i class="fa fa-envelope"></i> مشاهده</button>
            </div>
        @endif

        {{-- راهنمای اسکرول موبایل --}}
        <div class="mobile-hint">
            <i class="fa fa-arrow-right"></i> برای دیدن بخش‌های بیشتر به راست اسکرول کنید <i class="fa fa-arrow-left"></i>
        </div>

        {{-- تب‌ها --}}
        <div class="tabs-container">
            <div class="tabs-header" id="tabsHeader">
                <button class="tab-btn" data-tab="residences">🏠 اقامتگاه‌ها</button>
                @if($isHost)
                    <button class="tab-btn" data-tab="requests">📋 درخواست‌ها</button>
                @endif
                <button class="tab-btn" data-tab="reservations">🎫 رزروها</button>
                <button class="tab-btn" data-tab="wallet">💰 تراکنش‌ها</button>
                <button class="tab-btn" data-tab="tickets">💬 پیام‌ها</button>
            </div>

            <div class="tab-content">
                
                {{-- اقامتگاه‌های من --}}
                <div class="tab-pane" id="tab-residences">
                    @if($user->residences->count() > 0)
                        <div class="properties-list">
                            @foreach($user->residences as $item)
                                <div class="property-item">
                                    <img class="property-image" src="{{ asset('storage/residences/' . $item->image) }}" alt="{{ $item->title }}">
                                    <div class="property-info">
                                        <h3 class="property-title">{{ $item->title }}</h3>
                                        <div class="property-stats">
                                            <span><i class="fa fa-eye"></i> {{ $item->view }} بازدید</span>
                                            <span><i class="fa fa-map-marker"></i> {{ $item->city->name ?? '' }}</span>
                                        </div>
                                        <div class="property-actions">
                                            <a href="{{ url('detail/' . $item->id) }}" class="btn-sm btn-outline"><i class="fa fa-eye"></i> مشاهده</a>
                                            <a href="{{ url('edit-residence/' . $item->id) }}" class="btn-sm btn-primary"><i class="fa fa-edit"></i> ویرایش</a>
                                            <button wire:click="removeResidence({{ $item->id }})" wire:confirm="از حذف این اقامتگاه اطمینان دارید؟" class="btn-sm btn-danger"><i class="fa fa-trash"></i> حذف</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fa fa-home"></i>
                            <p>هنوز اقامتگاهی ثبت نکرده‌اید</p>
                            <a href="{{ url('add-residence') }}" style="color: var(--primary);">ثبت اولین اقامتگاه</a>
                        </div>
                    @endif
                </div>

                {{-- درخواست‌های رزرو (فقط میزبان) --}}
                @if($isHost)
                <div class="tab-pane" id="tab-requests">
                    <h4 style="margin-bottom: 16px; color: var(--secondary);"><i class="fa fa-calendar-check-o"></i> درخواست‌های رزرو</h4>
                    @if($reservationRequests->count() > 0)
                        @foreach($reservationRequests as $request)
                            <div class="request-item">
                                <div class="request-info">
                                    <div class="request-residence">{{ $request->residence->title ?? '' }}</div>
                                    <div class="request-details">
                                        <span><i class="fa fa-user"></i> {{ $request->user->name ?? 'کاربر' }}</span>
                                        <span><i class="fa fa-calendar"></i> {{ $request->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="request-status pending"><i class="fa fa-clock-o"></i> در انتظار تأیید</div>
                                </div>
                                <div class="request-amount">{{ number_format($request->amount) }} تومان</div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state"><i class="fa fa-calendar"></i><p>هیچ درخواستی وجود ندارد</p></div>
                    @endif
                </div>
                @endif

                {{-- رزروهای من --}}
                <div class="tab-pane" id="tab-reservations">
                    <h4 style="margin-bottom: 16px; color: var(--secondary);"><i class="fa fa-ticket"></i> رزروهای من</h4>
                    @if($myReservations->count() > 0)
                        @foreach($myReservations as $reservation)
                            <div class="reservation-item">
                                <div class="reservation-info">
                                    <div class="reservation-residence">{{ $reservation->residence->title ?? '' }}</div>
                                    <div class="reservation-details">
                                        <span><i class="fa fa-map-marker"></i> {{ $reservation->residence->city->name ?? '' }}</span>
                                        <span><i class="fa fa-calendar"></i> {{ $reservation->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="reservation-status {{ $reservation->status }}">
                                        @if($reservation->status == 'pending')
                                            <i class="fa fa-clock-o"></i> در انتظار تأیید
                                        @elseif($reservation->status == 'confirmed')
                                            <i class="fa fa-check-circle"></i> تأیید شده
                                        @else
                                            <i class="fa fa-times-circle"></i> لغو شده
                                        @endif
                                    </div>
                                </div>
                                <div class="reservation-amount">{{ number_format($reservation->amount) }} تومان</div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state"><i class="fa fa-ticket"></i><p>هیچ رزروی ندارید</p></div>
                    @endif
                </div>

                {{-- تراکنش‌ها --}}
                <div class="tab-pane" id="tab-wallet">
                    <h4 style="margin-bottom: 16px; color: var(--secondary);"><i class="fa fa-history"></i> تاریخچه تراکنش‌ها</h4>
                    <div class="transactions-list">
                        @if($transactions->count() > 0)
                            @foreach($transactions as $transaction)
                                <div class="transaction-item">
                                    <div class="transaction-icon"><i class="fa {{ $transaction->type == 'deposit' ? 'fa-arrow-down text-success' : 'fa-arrow-up text-danger' }}"></i></div>
                                    <div class="transaction-info">
                                        <div class="transaction-title">{{ $transaction->type == 'deposit' ? 'واریز' : 'برداشت' }} - {{ $transaction->residence->title ?? '' }}</div>
                                        <div class="transaction-date">{{ $transaction->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="transaction-amount {{ $transaction->type == 'deposit' ? 'text-success' : 'text-danger' }}">{{ $transaction->type == 'deposit' ? '+' : '-' }} {{ number_format($transaction->amount) }} تومان</div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state"><i class="fa fa-credit-card"></i><p>هیچ تراکنشی یافت نشد</p></div>
                        @endif
                    </div>
                </div>

                {{-- پیام‌ها --}}
                <div class="tab-pane" id="tab-tickets">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
                        <h4 style="margin: 0; color: var(--secondary);"><i class="fa fa-envelope"></i> پیام‌های من</h4>
                        <a href="{{ url('contact') }}" class="wallet-btn-small" style="background: var(--accent); color: white;"><i class="fa fa-plus"></i> تیکت جدید</a>
                    </div>
                    @if($user->tickets->count() > 0)
                        <table class="tickets-table">
                            <thead>
                                <tr><th>شناسه</th><th>عنوان</th><th>بخش</th><th>وضعیت</th><th>تاریخ</th><th></th></tr>
                            </thead>
                            <tbody>
                                @foreach($user->tickets as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ $areas[$item->area]->title ?? '' }}</td>
                                        <td><span class="{{ $item->status == 0 ? 'status-warning' : 'status-success' }}">{{ $item->status == 0 ? 'درحال بررسی' : 'پاسخ داده شده' }}</span></td>
                                        <td>{{ \Morilog\Jalali\Jalalian::fromDateTime($item->created_at)->format('%Y/%m/%d') }}</td>
                                        <td><a href="{{ url('ticket/' . $item->id) }}" class="btn-sm btn-primary">مشاهده</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state"><i class="fa fa-envelope"></i><p>هیچ پیامی ندارید</p><a href="{{ url('contact') }}" style="color: var(--primary);">ثبت اولین تیکت</a></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const tabId = this.getAttribute('data-tab');
                document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
                document.getElementById(`tab-${tabId}`).classList.add('active');
            });
        });
        
        document.getElementById('readMessagesBtn')?.addEventListener('click', function() {
            document.querySelector('.tab-btn[data-tab="tickets"]').click();
        });
        
        if (document.querySelector('.tab-btn')) {
            document.querySelector('.tab-btn').click();
        }
        
        const tabsHeader = document.getElementById('tabsHeader');
        if (tabsHeader) {
            const activeTab = tabsHeader.querySelector('.tab-btn.active');
            if (activeTab) {
                activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        }
    </script>
</div>