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
        
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px 0 40px;
        }
        
        .profile-card {
            background: white;
            border-radius: 28px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--secondary) 0%, #1A4A6E 100%);
            padding: 32px;
            text-align: center;
            position: relative;
        }
        
        .avatar-wrapper {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        
        .avatar {
            width: 120px;
            height: 120px;
            background: var(--gray-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            transition: all 0.2s;
        }
        
        .avatar:hover {
            transform: scale(1.02);
        }
        
        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .avatar i {
            font-size: 60px;
            color: var(--primary);
        }
        
        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s;
        }
        
        .avatar-wrapper:hover .avatar-overlay {
            opacity: 1;
        }
        
        .avatar-overlay i {
            font-size: 30px;
            color: white;
        }
        
        .profile-name {
            font-size: 22px;
            font-weight: 800;
            color: white;
            margin-top: 16px;
            margin-bottom: 4px;
        }
        
        .profile-phone {
            font-size: 14px;
            color: rgba(255,255,255,0.8);
        }
        
        .profile-body {
            padding: 32px;
        }
        
        .form-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-group.full-width {
            grid-column: span 2;
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 6px;
        }
        
        .form-label i {
            color: var(--primary);
            margin-left: 4px;
        }
        
        .form-control-custom {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
            background: white;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102,204,255,0.1);
        }
        
        .form-control-custom:disabled {
            background: var(--gray-bg);
            color: var(--gray-text);
        }
        
        .phone-display {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--gray-bg);
            border-radius: 12px;
            padding: 12px 16px;
        }
        
        .phone-display span {
            flex: 1;
            font-size: 14px;
            color: var(--secondary);
            direction: ltr;
        }
        
        .phone-badge {
            background: var(--border);
            color: var(--gray-text);
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 11px;
        }
        
        .error-text {
            color: var(--danger);
            font-size: 11px;
            margin-top: 4px;
            display: block;
        }
        
        .btn-submit {
            width: 100%;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 24px;
        }
        
        .btn-submit:hover {
            background: #D97706;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .profile-body {
                padding: 24px;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full-width {
                grid-column: span 1;
            }
            .avatar {
                width: 100px;
                height: 100px;
            }
            .profile-name {
                font-size: 18px;
            }
        }
    </style>

    @php
        $user = auth()->user();
    @endphp

    <div class="profile-container">
        <div class="profile-card">
            {{-- هدر پروفایل --}}
            <div class="profile-header">
                <div class="avatar-wrapper" onclick="document.getElementById('avatarInput').click()">
                    <div class="avatar">
                        @if($tempAvatar)
                            <img src="{{ $tempAvatar }}" alt="آواتار">
                        @elseif($user->profile_image && $user->profile_image != '')
                            <img src="{{ asset('storage/user/' . $user->profile_image) }}" alt="آواتار">
                        @else
                            <i class="fa fa-user-circle"></i>
                        @endif
                    </div>
                    <div class="avatar-overlay">
                        <i class="fa fa-camera"></i>
                    </div>
                </div>
                <input type="file" id="avatarInput" wire:model="image" accept="image/jpeg,image/png,image/webp,image/gif" style="display: none">
                
                <div class="profile-name">
                    {{ $user->name ?? 'کاربر' }} {{ $user->family ?? '' }}
                </div>
                <div class="profile-phone">
                    <i class="fa fa-phone"></i> {{ $user->phone }}
                </div>
            </div>

            {{-- فرم اطلاعات --}}
            <form wire:submit="save" class="profile-body">
                <h2 class="form-title">
                    <i class="fa fa-user"></i> اطلاعات شخصی
                </h2>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label"><i class="fa fa-user"></i> نام</label>
                        <input type="text" wire:model="name" class="form-control-custom" placeholder="نام خود را وارد کنید">
                        @error('name') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa fa-user-friends"></i> نام خانوادگی</label>
                        <input type="text" wire:model="family" class="form-control-custom" placeholder="نام خانوادگی خود را وارد کنید">
                        @error('family') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa fa-id-card"></i> کد ملی</label>
                        <input type="text" wire:model="nationalCode" class="form-control-custom" placeholder="کد ملی خود را وارد کنید" maxlength="10">
                        @error('nationalCode') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa fa-calendar"></i> تاریخ تولد</label>
                        <input type="text" wire:model="birthDay" id="birthday" class="form-control-custom" placeholder="سال/ماه/روز" maxlength="10">
                        @error('birthDay') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label"><i class="fa fa-phone"></i> شماره موبایل</label>
                        <div class="phone-display">
                            <span dir="ltr">{{ $user->phone }}</span>
                            <span class="phone-badge">غیرقابل ویرایش</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="fa fa-save"></i> ذخیره اطلاعات</span>
                    <span wire:loading><i class="fa fa-spinner fa-spin"></i> در حال ذخیره...</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        // تاریخ تولد
        const birthdayInput = document.getElementById('birthday');
        if (birthdayInput) {
            birthdayInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                let formatted = '';
                
                if (value.length > 0) {
                    formatted = value.slice(0, 4);
                }
                if (value.length > 4) {
                    formatted += '/' + value.slice(4, 6);
                }
                if (value.length > 6) {
                    formatted += '/' + value.slice(6, 8);
                }
                
                this.value = formatted;
            });
        }
        
        // نمایش پیام با SweetAlert
        @if(session('message'))
            Swal.fire({
                icon: "success",
                title: "{{ session('message') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
</div>