@extends('layouts.app')
@section('title', 'Đặt lại mật khẩu — TravelNice')

@push('styles')
<style>
.auth-wrap { min-height:calc(100vh - 64px); background:linear-gradient(135deg,#0066CC,#0099FF); display:flex; align-items:center; justify-content:center; padding:40px 16px; }
.auth-card { background:#fff; border-radius:16px; padding:40px; width:100%; max-width:440px; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.auth-logo { text-align:center; margin-bottom:24px; }
.auth-logo .logo-text { font-size:26px; font-weight:800; color:var(--tn-blue); }
.auth-logo .logo-text span { color:var(--tn-orange); }
.auth-input { height:46px; border:1.5px solid var(--tn-border); border-radius:8px; font-size:14px; padding:0 14px 0 14px; width:100%; transition:border-color .15s; }
.auth-input:focus { border-color:var(--tn-blue); box-shadow:0 0 0 3px rgba(0,102,204,.1); outline:none; }
.auth-input.is-invalid { border-color:#DC2626; }
.auth-btn { width:100%; height:46px; background:var(--tn-blue); color:#fff; border:none; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; transition:filter .15s; }
.auth-btn:hover { filter:brightness(1.08); }
.input-wrap { margin-bottom:14px; position:relative; }
.toggle-pw { position:absolute; right:12px; bottom:12px; background:none; border:none; color:var(--tn-muted); cursor:pointer; font-size:16px; }
.auth-error { font-size:12px; color:#DC2626; margin-top:4px; }
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
                <i class="bi bi-lock" style="font-size:24px;color:var(--tn-blue)"></i>
            </div>
            <h5 class="fw-700 mb-1">Đặt lại mật khẩu</h5>
            <p style="font-size:13px;color:var(--tn-muted)">Nhập mật khẩu mới cho tài khoản của bạn</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger mb-3" style="font-size:13px;border-radius:8px">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="input-wrap">
                <label style="font-size:13px;font-weight:600;display:block;margin-bottom:5px">Email</label>
                <input type="email" name="email" class="auth-input {{ $errors->has('email') ? 'is-invalid':'' }}"
                       value="{{ old('email', $request->email) }}" required>
                @error('email')<div class="auth-error">{{ $message }}</div>@enderror
            </div>

            <div class="input-wrap">
                <label style="font-size:13px;font-weight:600;display:block;margin-bottom:5px">Mật khẩu mới</label>
                <input type="password" name="password" id="pw1"
                       class="auth-input {{ $errors->has('password') ? 'is-invalid':'' }}"
                       placeholder="Tối thiểu 8 ký tự" required autocomplete="new-password">
                <button type="button" class="toggle-pw" onclick="togglePw('pw1','ic1')"><i class="bi bi-eye" id="ic1"></i></button>
                @error('password')<div class="auth-error">{{ $message }}</div>@enderror
            </div>

            <div class="input-wrap">
                <label style="font-size:13px;font-weight:600;display:block;margin-bottom:5px">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" id="pw2"
                       class="auth-input" placeholder="Nhập lại mật khẩu" required autocomplete="new-password">
                <button type="button" class="toggle-pw" onclick="togglePw('pw2','ic2')"><i class="bi bi-eye" id="ic2"></i></button>
            </div>

            <button type="submit" class="auth-btn">
                <i class="bi bi-check-circle me-1"></i> Đặt lại mật khẩu
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePw(id, icId) {
    const el = document.getElementById(id);
    const ic = document.getElementById(icId);
    el.type = el.type === 'password' ? 'text' : 'password';
    ic.className = el.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
@endpush