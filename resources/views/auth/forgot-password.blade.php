@extends('layouts.app')
@section('title', 'Quên mật khẩu — TravelNice')

@push('styles')
<style>
.auth-wrap { min-height: calc(100vh - 64px); background: linear-gradient(135deg,#0066CC,#0099FF); display:flex; align-items:center; justify-content:center; padding:40px 16px; }
.auth-card { background:#fff; border-radius:16px; padding:40px; width:100%; max-width:440px; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.auth-logo { text-align:center; margin-bottom:24px; }
.auth-logo .logo-text { font-size:26px; font-weight:800; color:var(--tn-blue); }
.auth-logo .logo-text span { color:var(--tn-orange); }
.auth-input { height:46px; border:1.5px solid var(--tn-border); border-radius:8px; font-size:14px; padding:0 14px; width:100%; transition:border-color .15s; }
.auth-input:focus { border-color:var(--tn-blue); box-shadow:0 0 0 3px rgba(0,102,204,.1); outline:none; }
.auth-btn { width:100%; height:46px; background:var(--tn-blue); color:#fff; border:none; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; transition:filter .15s; }
.auth-btn:hover { filter:brightness(1.08); }
.auth-link { color:var(--tn-blue); text-decoration:none; font-size:13px; font-weight:500; }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-text"><i class="bi bi-compass"></i> Travel<span>Nice</span></div>
        </div>

        <div class="text-center mb-4">
            <div style="width:56px;height:56px;background:#EEF5FF;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                <i class="bi bi-envelope-open" style="font-size:24px;color:var(--tn-blue)"></i>
            </div>
            <h5 class="fw-700 mb-1">Quên mật khẩu?</h5>
            <p style="font-size:13px;color:var(--tn-muted)">Nhập email của bạn, chúng tôi sẽ gửi link đặt lại mật khẩu</p>
        </div>

        @if(session('status'))
        <div class="alert alert-success mb-3" style="font-size:13px;border-radius:8px">
            <i class="bi bi-check-circle me-1"></i>{{ session('status') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger mb-3" style="font-size:13px;border-radius:8px">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div style="margin-bottom:16px">
                <label style="font-size:13px;font-weight:600;display:block;margin-bottom:5px">Email</label>
                <input type="email" name="email" class="auth-input" value="{{ old('email') }}"
                       placeholder="email@example.com" autofocus required>
            </div>
            <button type="submit" class="auth-btn">
                <i class="bi bi-send me-1"></i> Gửi link đặt lại mật khẩu
            </button>
        </form>

        <p class="text-center mt-3 mb-0" style="font-size:13px">
            <a href="{{ route('login') }}" class="auth-link"><i class="bi bi-arrow-left me-1"></i> Quay lại đăng nhập</a>
        </p>
    </div>
</div>
@endsection