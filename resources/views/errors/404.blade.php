{{-- resources/views/errors/404.blade.php --}}
@extends('app') {{-- یا هر layout دیگر --}}
@section('title', 'صفحه پیدا نشد')

@section('content')
    <div style="text-align: center; padding: 50px;">
        <h1>404</h1>
        <p>اوه! صفحه‌ای که دنبالش هستی وجود نداره.</p>
        <br>
        <br>
        <img src="{{asset("storage/".getConfigs("page404Icon"))}}" style="max-width: 300px;min-width: 200px">
        <br>
        <br>
        <br>
        <a class="btn btn-primary" href="{{ url('/') }}">بازگشت به صفحه اصلی</a>
    </div>
@endsection
