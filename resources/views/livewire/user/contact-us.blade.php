<div>
    <style>
        :root {
            --primary: #66ccff;
            --secondary: #0A2B4E;
            --accent: #F59E0B;
            --gray-bg: #F8FAFC;
            --gray-text: #475569;
            --border: #E2E8F0;
        }
        
        .page-header {
            margin: 20px 0 30px;
            text-align: center;
        }
        
        .page-header h2 {
            font-size: 28px;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 8px;
        }
        
        .page-header p {
            color: var(--gray-text);
            font-size: 14px;
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .form-card {
            background: white;
            border-radius: 24px;
            border: 1px solid var(--border);
            overflow: hidden;
        }
        
        .form-header {
            background: var(--secondary);
            padding: 16px 24px;
            border-bottom: 3px solid var(--primary);
        }
        
        .form-header h3 {
            color: white;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }
        
        .form-header i {
            color: var(--primary);
            margin-left: 8px;
        }
        
        .form-body {
            padding: 24px;
        }
        
        .info-card {
            background: white;
            border-radius: 24px;
            border: 1px solid var(--border);
            overflow: hidden;
        }
        
        .info-header {
            background: var(--secondary);
            padding: 16px 24px;
            border-bottom: 3px solid var(--primary);
        }
        
        .info-header h3 {
            color: white;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }
        
        .info-body {
            padding: 24px;
        }
        
        .contact-info-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
        }
        
        .contact-info-item:last-child {
            border-bottom: none;
        }
        
        .contact-info-item i {
            width: 40px;
            height: 40px;
            background: var(--gray-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 18px;
        }
        
        .contact-info-item span {
            color: var(--gray-text);
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--secondary);
            font-size: 13px;
        }
        
        .form-group label i {
            color: var(--primary);
            margin-left: 6px;
        }
        
        .form-control-custom {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.2s;
            font-family: inherit;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 204, 255, 0.1);
        }
        
        .form-control-custom:disabled {
            background: var(--gray-bg);
            color: var(--gray-text);
        }
        
        select.form-control-custom {
            cursor: pointer;
        }
        
        textarea.form-control-custom {
            resize: vertical;
            min-height: 140px;
        }
        
        .alert-box {
            background: #FEF3C7;
            border-right: 4px solid #F59E0B;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            text-align: center;
        }
        
        .alert-box p {
            margin-bottom: 12px;
            color: #92400E;
        }
        
        .login-link {
            display: inline-block;
            background: var(--accent);
            color: white;
            padding: 10px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .login-link:hover {
            background: #D97706;
        }
        
        .btn-submit {
            width: 100%;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 12px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-submit:hover:not(:disabled) {
            background: #D97706;
            transform: translateY(-1px);
        }
        
        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .error-text {
            color: #EF4444;
            font-size: 12px;
            margin-top: 6px;
            display: block;
        }
        
        @media (max-width: 768px) {
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .page-header h2 {
                font-size: 22px;
            }
            
            .form-body, .info-body {
                padding: 16px;
            }
        }
    </style>

    {{-- عنوان صفحه --}}
    <div class="page-header">
        <h2>تماس با ما</h2>
        <p>سوال، پیشنهاد یا مشکلی دارید؟ با ما در میان بگذارید</p>
    </div>

    <div class="contact-grid">
        {{-- فرم تماس --}}
        <div class="form-card">
            <div class="form-header">
                <h3>
                    <i class="fa fa-envelope"></i>
                    ارسال پیام
                </h3>
            </div>
            <div class="form-body">
                @if(!auth()->check())
                    <div class="alert-box">
                        <p>برای ارسال پیام باید وارد حساب خود شوید</p>
                        <a href="{{ url('login') }}" class="login-link">
                            <i class="fa fa-sign-in"></i> ورود یا ثبت نام
                        </a>
                    </div>
                @endif
                
                <form wire:submit.prevent="save">
                    <div class="form-group">
                        <label><i class="fa fa-user"></i> نام شما</label>
                        <input type="text" value="{{ $name }}" disabled class="form-control-custom">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fa fa-phone"></i> شماره تماس</label>
                        <input type="text" value="{{ $phone }}" disabled class="form-control-custom">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fa fa-tag"></i> عنوان پیام</label>
                        <input type="text" wire:model="title" class="form-control-custom" placeholder="مثال: مشکل در رزرو">
                        @error('title')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fa fa-folder"></i> موضوع</label>
                        <select wire:model="area" class="form-control-custom">
                            @foreach(\App\Models\SupportAreaTickets::where("status",true)->get() as $item)
                                <option value="{{ $item->id }}">{{ $item->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fa fa-comment"></i> متن پیام</label>
                        <textarea wire:model="message" class="form-control-custom" placeholder="توضیحات خود را وارد کنید..."></textarea>
                        @error('message')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn-submit" {{ !auth()->check() ? 'disabled' : '' }}>
                        <i class="fa fa-paper-plane"></i> ارسال پیام
                    </button>
                </form>
            </div>
        </div>

        {{-- اطلاعات تماس --}}
        <div class="info-card">
            <div class="info-header">
                <h3>
                    <i class="fa fa-phone"></i>
                    اطلاعات تماس
                </h3>
            </div>
            <div class="info-body">
                <div class="contact-info-item">
                    <i class="fa fa-map-marker"></i>
                    <span>{{ getConfigs('address') }}</span>
                </div>
                <div class="contact-info-item">
                    <i class="fa fa-phone"></i>
                    <span>{{ getConfigs('phone1') }}</span>
                </div>
                @if(getConfigs('phone2'))
                <div class="contact-info-item">
                    <i class="fa fa-phone"></i>
                    <span>{{ getConfigs('phone2') }}</span>
                </div>
                @endif
                <div class="contact-info-item">
                    <i class="fa fa-envelope"></i>
                    <span>{{ getConfigs('email') }}</span>
                </div>
            </div>
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

        Livewire.on("create", event => {
            Swal.fire({
                icon: "success",
                title: 'ثبت موفقیت آمیز',
                text: 'درخواست شما با موفقیت ثبت شد و میتوانید پاسخ آن را از طریق پروفایل خود پیگیری کنید.',
                confirmButtonText: "بستن",
                confirmButtonColor: '#0A2B4E',
            })
        })
    </script>
    @endscript
</div>