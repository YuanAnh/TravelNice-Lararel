@extends('layouts.app')
@section('title', 'Xác nhận mật khẩu — TravelNice')

@push('styles')
<style>
.auth-wrap { min-height:calc(100vh - 64px); background:linear-gradient(135deg,#0066CC,#0099FF); display:flex; align-items:center; justify-content:center; padding:40px 16px; }
.auth-card { background:#fff; border-radius:16px; padding:40px; width:100%; max-width:440px; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.auth-logo { text-align:center; margin-bottom:24px; }
.auth-logo .logo-text { font-size:26px; font-weight:800; color:var(--tn-blue); }
.auth-logo .logo-text span { color:var(--tn-orange); }
.auth-input { height:46px; border:1.5px solid var(--tn-border); border-radius:8px; font-size:14px; padding:0 14px; width:100%; transition:border-color .15s; }
.auth-input:focus { border-color:var(--tn-blue); box-shadow:0 0 0 3px rgba(0,102,204,.1); outline:none; }
.auth-btn { width:100%; height:46px; background:var(--tn-blue); color:#fff; border:none; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; transition:filter .15s; }
.auth-btn:hover { filter:brightness(1.08); }
.input-wrap { margin-bottom:16px; position:relative; }
.toggle-pw { position:absolute; right:12px; bottom:12px; background:none; border:none; color:var(--tn-muted); cursor:pointer; font-size:16px; }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-text"><i class="bi bi-compass"></i> Travel<span>Nice</span></div>
        </div>

        <div class="text-center mb-4">
            <div style="width:56px;height:56px;background:#FFF3CD;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                <i class="bi bi-shield-lock" style="font-size:24px;color:#F59E0B"></i>
            </div>
            <h5 class="fw-700 mb-1">Xác nhận mật khẩu</h5>
            <p style="font-size:13px;color:var(--tn-muted)">Đây là khu vực bảo mật. Vui lòng xác nhận mật khẩu trước khi tiếp tục.</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger mb-3" style="font-size:13px;border-radius:8px">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div class="input-wrap">
                <label style="font-size:13px;font-weight:600;display:block;margin-bottom:5px">Mật khẩu</label>
                <input type="password" name="password" id="pwInput" class="auth-input"
                       placeholder="Nhập mật khẩu hiện tại" required autocomplete="current-password">
                <button type="button" class="toggle-pw" onclick="togglePw()">
                    <i class="bi bi-eye" id="pwIcon"></i>
                </button>
            </div>
            <button type="submit" class="auth-btn">
                <i class="bi bi-shield-check me-1"></i> Xác nhận
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePw() {
    const el = document.getElementById('pwInput');
    const ic = document.getElementById('pwIcon');
    el.type = el.type === 'password' ? 'text' : 'password';
    ic.className = el.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
@endpush