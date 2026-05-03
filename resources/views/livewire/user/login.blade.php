<div id="login-page">
    <style>
        :root {
            --primary: #66ccff;
            --secondary: #0A2B4E;
            --accent: #F59E0B;
            --gray-bg: #F8FAFC;
            --gray-text: #475569;
            --border: #E2E8F0;
        }
        
        .login-container {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        .login-card {
            max-width: 480px;
            width: 100%;
            background: white;
            border-radius: 32px;
            border: 1px solid var(--border);
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 20px 35px -12px rgba(0,0,0,0.08);
        }
        
        .login-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--secondary) 0%, #1A4A6E 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        
        .login-icon i {
            font-size: 40px;
            color: var(--primary);
        }
        
        .login-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 12px;
        }
        
        .login-subtitle {
            color: var(--gray-text);
            font-size: 14px;
            margin-bottom: 28px;
        }
        
        .phone-input {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid var(--border);
            border-radius: 60px;
            font-size: 16px;
            text-align: center;
            font-family: inherit;
            transition: all 0.2s;
            direction: ltr;
        }
        
        .phone-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 204, 255, 0.1);
        }
        
        .error-text {
            color: #EF4444;
            font-size: 12px;
            margin-top: 8px;
            display: block;
        }
        
        .terms-text {
            font-size: 12px;
            color: var(--gray-text);
            margin: 20px 0;
        }
        
        .terms-text a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .btn-continue {
            width: 100%;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 60px;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-continue:hover {
            background: #D97706;
            transform: translateY(-1px);
        }
        
        .btn-continue:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .phone-display {
            background: var(--gray-bg);
            border-radius: 60px;
            padding: 12px 20px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }
        
        .phone-number {
            font-size: 16px;
            font-weight: 600;
            color: var(--secondary);
            direction: ltr;
            display: inline-block;
        }
        
        .edit-phone {
            font-size: 12px;
            color: var(--primary);
            cursor: pointer;
            text-decoration: none;
        }
        
        .otp-container {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 24px 0;
            direction: ltr;
        }
        
        .otp-input {
            width: 55px;
            height: 55px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--gray-bg);
            transition: all 0.2s;
            -moz-appearance: textfield;
            appearance: textfield;
        }
        
        .otp-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 204, 255, 0.1);
        }
        
        .otp-input::-webkit-outer-spin-button,
        .otp-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        .timer-btn {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 60px;
            padding: 8px 20px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }
        
        .timer-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .resend-text {
            font-size: 12px;
            color: var(--gray-text);
        }
        
        .btn-verify {
            width: 100%;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 60px;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 16px;
        }
        
        .btn-verify:hover {
            background: #D97706;
        }
        
        @media (max-width: 480px) {
            .login-card {
                padding: 28px 20px;
            }
            .login-title {
                font-size: 22px;
            }
            .otp-input {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }
            .otp-container {
                gap: 8px;
            }
        }
    </style>

    <div class="login-container">
        <div class="login-card">
            {{-- مرحله 1: وارد کردن شماره موبایل --}}
            <div class="{{ $page == 1 ? '' : 'd-none' }}" id="step-phone">
                <div class="login-icon">
                    <i class="fa fa-home"></i>
                </div>
                <h1 class="login-title">ورود یا ثبت نام</h1>
                <p class="login-subtitle">برای ورود به اینجا، شماره همراه خود را وارد کنید</p>
                
                <input type="tel" 
                       wire:model="phone" 
                       placeholder="۰۹۱۲۳۴۵۶۷۸۹" 
                       class="phone-input"
                       maxlength="11"
                       dir="ltr"
                       id="phoneInput">
                
                @error('phone')
                    <span class="error-text">{{ $message }}</span>
                @enderror
                
                <p class="terms-text">
                    ورود و ثبت نام در اینجا به منزله پذیرفتن 
                    <a href="{{ url('/p/terms') }}">قوانین و مقررات</a> 
                    میباشد
                </p>
                
                <button class="btn-continue" wire:click="login" wire:loading.attr="disabled">
                    <i class="fa fa-spin fa-spinner" wire:loading wire:target="login"></i>
                    ادامه
                </button>
            </div>

            {{-- مرحله 2: تایید کد ارسال شده --}}
            <div class="{{ $page == 2 ? '' : 'd-none' }}" id="step-verify">
                <div class="login-icon">
                    <i class="fa fa-mobile"></i>
                </div>
                <h1 class="login-title">تایید شماره موبایل</h1>
                <p class="login-subtitle">کد ۴ رقمی ارسال شده را وارد کنید</p>
                
                <div class="phone-display">
                    <span class="phone-number" dir="ltr">{{ $phone }}</span>
                    <a href="#" class="edit-phone" wire:click="back">
                        <i class="fa fa-pencil"></i> ویرایش
                    </a>
                </div>
                
                <div class="otp-container">
                    <input type="text" 
                           maxlength="1" 
                           class="otp-input otp-digit" 
                           id="digit1"
                           autocomplete="one-time-code"
                           inputmode="numeric"
                           pattern="\d*"
                           oninput="otpInputHandler(this, 1)">
                    <input type="text" 
                           maxlength="1" 
                           class="otp-input otp-digit" 
                           id="digit2"
                           autocomplete="one-time-code"
                           inputmode="numeric"
                           pattern="\d*"
                           oninput="otpInputHandler(this, 2)">
                    <input type="text" 
                           maxlength="1" 
                           class="otp-input otp-digit" 
                           id="digit3"
                           autocomplete="one-time-code"
                           inputmode="numeric"
                           pattern="\d*"
                           oninput="otpInputHandler(this, 3)">
                    <input type="text" 
                           maxlength="1" 
                           class="otp-input otp-digit" 
                           id="digit4"
                           autocomplete="one-time-code"
                           inputmode="numeric"
                           pattern="\d*"
                           oninput="otpInputHandler(this, 4)">
                </div>
                
                @error('code')
                    <span class="error-text">{{ $message }}</span>
                @enderror
                
                <button class="timer-btn" id="timerBtn" disabled>
                    <i class="fa fa-refresh" id="timerIcon"></i>
                    <span id="timerText">۰۲:۰۰</span>
                </button>
                <p class="resend-text" id="resendText" style="display: none;">
                    کد دریافت نکردید؟ <a href="#" wire:click="resendCode">ارسال مجدد</a>
                </p>
                
                <button class="btn-verify" id="verifyBtn" wire:click="verify_code" wire:loading.attr="disabled">
                    <i class="fa fa-spin fa-spinner" wire:loading wire:target="verify_code"></i>
                    ورود به حساب کاربری
                </button>
            </div>
        </div>
    </div>

    @script
    <script>
        let timerInterval = null;
        let seconds = 120;
        
        // تابع مدیریت ورودی OTP
        window.otpInputHandler = function(input, index) {
            input.value = input.value.replace(/[^0-9]/g, '');
            
            @this.set('code' + index, input.value);
            
            if (input.value.length === 1) {
                if (index === 1) document.getElementById('digit2').focus();
                else if (index === 2) document.getElementById('digit3').focus();
                else if (index === 3) document.getElementById('digit4').focus();
                else if (index === 4) {
                    const digit1 = document.getElementById('digit1').value;
                    const digit2 = document.getElementById('digit2').value;
                    const digit3 = document.getElementById('digit3').value;
                    const digit4 = document.getElementById('digit4').value;
                    
                    if (digit1 && digit2 && digit3 && digit4) {
                        @this.call('verify_code');
                    }
                }
            }
        };
        
        // تایمر
        $wire.on('start-timer', () => {
            seconds = 120;
            const timerBtn = document.getElementById('timerBtn');
            const timerText = document.getElementById('timerText');
            const timerIcon = document.getElementById('timerIcon');
            const resendText = document.getElementById('resendText');
            
            timerBtn.disabled = true;
            timerIcon.classList.remove('active');
            resendText.style.display = 'none';
            
            if (timerInterval) clearInterval(timerInterval);
            
            timerInterval = setInterval(() => {
                seconds--;
                let minutes = Math.floor(seconds / 60);
                let secs = seconds % 60;
                let timeStr = `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                timerText.textContent = timeStr;
                
                if (seconds <= 0) {
                    clearInterval(timerInterval);
                    timerBtn.disabled = false;
                    timerIcon.classList.add('active');
                    resendText.style.display = 'block';
                    timerText.textContent = 'ارسال مجدد';
                }
            }, 1000);
        });
        
        // فوکوس روی فیلد اول
        $wire.on('focus-first-digit', () => {
            setTimeout(() => {
                const digit1 = document.getElementById('digit1');
                if (digit1) {
                    digit1.value = '';
                    digit1.focus();
                }
                for (let i = 2; i <= 4; i++) {
                    const digit = document.getElementById('digit' + i);
                    if (digit) digit.value = '';
                }
                @this.set('code1', '');
                @this.set('code2', '');
                @this.set('code3', '');
                @this.set('code4', '');
            }, 150);
        });
        
        // دکمه Backspace
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace') {
                const activeElement = document.activeElement;
                if (activeElement && activeElement.classList && activeElement.classList.contains('otp-digit')) {
                    if (activeElement.value === '') {
                        if (activeElement.id === 'digit2') document.getElementById('digit1').focus();
                        else if (activeElement.id === 'digit3') document.getElementById('digit2').focus();
                        else if (activeElement.id === 'digit4') document.getElementById('digit3').focus();
                    }
                }
            }
        });
        
        // ========== پشتیبانی از AutoFill پیامک در وب ویو و مرورگر ==========
        
        // روش 1: WebView SMS Retriever API (برای اپلیکیشن اندروید)
        if (window.SMSRetriever) {
            window.SMSRetriever.startWatch((sms) => {
                const codeMatch = sms.match(/\b(\d{4})\b/);
                if (codeMatch) {
                    const code = codeMatch[1];
                    const digits = code.split('');
                    for (let i = 0; i < digits.length && i < 4; i++) {
                        const digitInput = document.getElementById('digit' + (i + 1));
                        if (digitInput) {
                            digitInput.value = digits[i];
                            @this.set('code' + (i + 1), digits[i]);
                        }
                    }
                    if (digits.length === 4) {
                        setTimeout(() => {
                            @this.call('verify_code');
                        }, 100);
                    }
                }
            });
        }
        
        // روش 2: OTP Credential API (برای مرورگرهای مدرن)
        if ('OTPCredential' in window) {
            navigator.credentials.get({
                otp: { transport: ['sms'] }
            }).then(otp => {
                if (otp && otp.code) {
                    const code = otp.code;
                    const digits = code.split('');
                    for (let i = 0; i < digits.length && i < 4; i++) {
                        const digitInput = document.getElementById('digit' + (i + 1));
                        if (digitInput) {
                            digitInput.value = digits[i];
                            @this.set('code' + (i + 1), digits[i]);
                        }
                    }
                    if (digits.length === 4) {
                        setTimeout(() => {
                            @this.call('verify_code');
                        }, 100);
                    }
                }
            }).catch(err => console.log('OTP Credential error:', err));
        }
        
        // روش 3: مشاهده تغییرات DOM برای فوکوس خودکار
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                const stepVerify = document.getElementById('step-verify');
                if (stepVerify && !stepVerify.classList.contains('d-none')) {
                    setTimeout(() => {
                        const digit1 = document.getElementById('digit1');
                        if (digit1) digit1.focus();
                    }, 200);
                    observer.disconnect();
                }
            });
        });
        
        const stepVerify = document.getElementById('step-verify');
        if (stepVerify) {
            observer.observe(stepVerify, { attributes: true });
        }
        
        if (stepVerify && !stepVerify.classList.contains('d-none')) {
            setTimeout(() => {
                const digit1 = document.getElementById('digit1');
                if (digit1) digit1.focus();
            }, 200);
        }
    </script>
    @endscript
</div>