@if(session('admin_success'))
    <div class="admin-notice success">{{ session('admin_success') }}</div>
@endif

@if(session('admin_warning'))
    <div class="admin-notice warning">{{ session('admin_warning') }}</div>
@endif

@if($errors->any())
    <div class="admin-notice warning">
        <strong>لطفا خطاهای فرم را بررسی کنید.</strong>
        <ul class="admin-error-list">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
