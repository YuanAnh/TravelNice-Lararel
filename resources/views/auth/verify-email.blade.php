@extends('layouts.app')
@section('title', 'Xác thực email — TravelNice')

@push('styles')
<style>
.auth-wrap { min-height:calc(100vh - 64px); background:linear-gradient(135deg,#0066CC,#0099FF); display:flex; align-items:center; justify-content:center; padding:40px 16px; }
.auth-card { background:#fff; border-radius:16px; padding:40px; width:100%; max-width:440px; box-shadow:0 20px 60px rgba(0,0,0,.2); text-align:center; }
.auth-logo .logo-text { font-size:26px; font-weight:800; color:var(--tn-blue); }
.auth-logo .logo-text span { color:var(--tn-orange); }
.auth-btn { width:100%; height:46px; background:var(--tn-blue); color:#fff; border:none; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; transition:filter .15s; }
.auth-btn:hover { filter:brightness(1.08); }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo mb-4">
            <div class="logo-text"><i class="bi bi-compass"></i> Travel<span>Nice</span></div>
        </div>

        <div style="width:72px;height:72px;background:#E1F5EE;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
            <i class="bi bi-envelope-check" style="font-size:32px;color:#1D9E75"></i>
        </div>

        <h5 class="fw-700 mb-2">Xác thực email của bạn</h5>
        <p style="font-size:13px;color:var(--tn-muted);margin-bottom:20px">
            Chúng tôi đã gửi link xác thực đến email của bạn. Vui lòng kiểm tra hộp thư và nhấn vào link để kích hoạt tài khoản.
        </p>

        @if(session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-3" style="font-size:13px;border-radius:8px">
            <i class="bi bi-check-circle me-1"></i> Email xác thực đã được gửi lại!
        </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="auth-btn mb-3">
                <i class="bi bi-arrow-clockwise me-1"></i> Gửi lại email xác thực
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none;border:none;color:var(--tn-muted);font-size:13px;cursor:pointer;text-decoration:underline">
                Đăng xuất
            </button>
        </form>
    </div>
</div>
@endsection