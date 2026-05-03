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
        }
        
        .tickets-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px 0 40px;
        }
        
        /* لینک بازگشت */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            text-decoration: none;
            font-size: 13px;
            margin-bottom: 20px;
            padding: 8px 16px;
            background: white;
            border-radius: 40px;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }
        
        .back-link:hover {
            border-color: var(--primary);
            color: var(--accent);
        }
        
        /* هدر تیکت */
        .ticket-header {
            background: white;
            border-radius: 24px;
            border: 1px solid var(--border);
            padding: 20px 24px;
            margin-bottom: 24px;
        }
        
        .ticket-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--secondary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .ticket-title i {
            color: var(--primary);
        }
        
        .ticket-badge {
            display: inline-block;
            background: var(--gray-bg);
            color: var(--gray-text);
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 12px;
        }
        
        .ticket-badge i {
            margin-left: 4px;
        }
        
        /* لیست پیام‌ها - حالت چت */
        .messages-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 32px;
        }
        
        .message-item {
            display: flex;
            gap: 12px;
            max-width: 80%;
        }
        
        .message-item.me {
            flex-direction: row-reverse;
            align-self: flex-end;
        }
        
        /* آواتار */
        .message-avatar {
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            background: var(--gray-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .message-avatar i {
            font-size: 22px;
            color: var(--primary);
        }
        
        .message-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* حباب پیام - اندازه یکسان */
        .message-bubble {
            flex: 0 1 auto;
            max-width: calc(100% - 54px);
            background: white;
            border-radius: 20px;
            padding: 12px 18px;
            border: 1px solid var(--border);
        }
        
        .message-item.me .message-bubble {
            background: var(--secondary);
            border-color: var(--secondary);
        }
        
        /* فرستنده */
        .message-sender {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 6px;
        }
        
        .message-item.me .message-sender {
            color: rgba(255,255,255,0.7);
        }
        
        /* متن پیام - با شکستن خودکار */
        .message-text {
            font-size: 14px;
            line-height: 1.6;
            color: var(--gray-text);
            word-wrap: break-word;
            white-space: normal;
        }
        
        .message-item.me .message-text {
            color: white;
        }
        
        /* زمان */
        .message-time {
            font-size: 10px;
            color: var(--gray-text);
            direction: ltr;
            display: inline-block;
            margin-top: 6px;
        }
        
        .message-item.me .message-time {
            color: rgba(255,255,255,0.5);
        }
        
        /* فرم پاسخ */
        .reply-card {
            background: white;
            border-radius: 24px;
            border: 1px solid var(--border);
            overflow: hidden;
            margin-top: 24px;
        }
        
        .reply-header {
            background: var(--secondary);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .reply-header i {
            color: var(--primary);
            font-size: 18px;
        }
        
        .reply-header h3 {
            color: white;
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .reply-body {
            padding: 24px;
        }
        
        .reply-textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            transition: all 0.2s;
        }
        
        .reply-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102,204,255,0.1);
        }
        
        .btn-send {
            width: 100%;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 16px;
        }
        
        .btn-send:hover {
            background: #D97706;
            transform: translateY(-2px);
        }
        
        .btn-send:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .error-text {
            color: var(--danger);
            font-size: 12px;
            margin-top: 6px;
            display: block;
        }
        
        /* خالی بودن */
        .empty-messages {
            text-align: center;
            padding: 48px 20px;
            background: white;
            border-radius: 24px;
            border: 1px solid var(--border);
            color: var(--gray-text);
        }
        
        .empty-messages i {
            font-size: 48px;
            color: var(--border);
            margin-bottom: 12px;
            display: block;
        }
        
        /* پاسخ داده شده */
        .ticket-status {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
        }
        
        .status-badge {
            background: #FEF3C7;
            color: #D97706;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 12px;
        }
        
        .status-badge.resolved {
            background: #E8F5E9;
            color: #2E7D32;
        }
        
        @media (max-width: 768px) {
            .message-item {
                max-width: 90%;
            }
            
            .message-bubble {
                max-width: calc(100% - 46px);
            }
            
            .message-avatar {
                width: 36px;
                height: 36px;
            }
            
            .message-avatar i {
                font-size: 18px;
            }
            
            .message-bubble {
                padding: 10px 14px;
            }
            
            .reply-body {
                padding: 20px;
            }
        }
    </style>

    @php
        $messages = \App\Models\TicketChat::where("ticket_id", $ticket->id)
            ->orderBy("id", "asc")
            ->get();
        $isResolved = ($ticket->status == 1);
    @endphp

    <div class="tickets-container">
        
        {{-- لینک بازگشت --}}
        <a href="{{ url('/dashboard') }}" class="back-link">
            <i class="fa fa-arrow-right"></i> بازگشت به داشبورد
        </a>

        {{-- هدر تیکت --}}
        <div class="ticket-header">
            <div class="ticket-title">
                <i class="fa fa-ticket"></i>
                {{ $ticket->title }}
                <span class="ticket-badge">
                    <i class="fa fa-hashtag"></i> تیکت #{{ $ticket->id }}
                </span>
            </div>
            <div class="ticket-status">
                <span class="status-badge {{ $isResolved ? 'resolved' : '' }}">
                    <i class="fa {{ $isResolved ? 'fa-check-circle' : 'fa-clock-o' }}"></i>
                    {{ $isResolved ? 'پاسخ داده شده' : 'درحال بررسی' }}
                </span>
            </div>
        </div>

        {{-- لیست پیام‌ها --}}
        @if($messages->count() > 0)
            <div class="messages-list">
                @foreach($messages as $item)
                    @php
                        $isMe = ($item->user_id == auth()->id());
                        $gregorianDate = new \DateTime($item["created_at"]);
                        $jalaliDate = \Morilog\Jalali\Jalalian::fromDateTime($gregorianDate);
                    @endphp
                    
                    <div class="message-item {{ $isMe ? 'me' : '' }}">
                        <div class="message-avatar">
                            @if($isMe)
                                @if(auth()->user()->profile_image)
                                    <img src="{{ asset('storage/user/' . auth()->user()->profile_image) }}" alt="من">
                                @else
                                    <i class="fa fa-user-circle"></i>
                                @endif
                            @else
                                <i class="fa fa-headset"></i>
                            @endif
                        </div>
                        <div class="message-bubble">
                            <div class="message-sender">
                                {{ $isMe ? (auth()->user()->name ?? 'من') : 'پشتیبانی اینجا' }}
                            </div>
                            <div class="message-text">
                                {{ $item->message }}
                            </div>
                            <div class="message-time">
                                <i class="fa fa-clock-o"></i>
                                {{ $jalaliDate->format('H:i') }} - {{ $jalaliDate->format('%Y/%m/%d') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-messages">
                <i class="fa fa-comment-o"></i>
                <p>هنوز پیامی در این تیکت وجود ندارد</p>
            </div>
        @endif

        {{-- فرم پاسخ --}}
        <form wire:submit.prevent="save" class="reply-card">
            <div class="reply-header">
                <i class="fa fa-reply"></i>
                <h3>پاسخ به تیکت</h3>
            </div>
            <div class="reply-body">
                <textarea wire:model="message" rows="4" class="reply-textarea" placeholder="پیام خود را بنویسید..."></textarea>
                @error('message')
                    <span class="error-text">{{ $message }}</span>
                @enderror
                
                <button type="submit" class="btn-send" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="fa fa-paper-plane"></i> ارسال پیام</span>
                    <span wire:loading><i class="fa fa-spinner fa-spin"></i> در حال ارسال...</span>
                </button>
            </div>
        </form>
    </div>

    @script
    <script>
        // اسکرول خودکار به پایین صفحه برای دیدن آخرین پیام
        window.addEventListener('load', function() {
            const messagesList = document.querySelector('.messages-list');
            if (messagesList) {
                messagesList.scrollIntoView({ behavior: 'smooth', block: 'end' });
            }
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Livewire.on("create", event => {
            Swal.fire({
                icon: "success",
                title: 'ثبت موفقیت آمیز',
                text: 'پاسخ شما با موفقیت ثبت شد.',
                confirmButtonText: "بستن",
                confirmButtonColor: '#0A2B4E',
            });
        });
    </script>
    @endscript
</div>