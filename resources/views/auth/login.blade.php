@extends('layouts.app')
@section('title', 'Đăng nhập — TravelNice')

@push('styles')
<style>
.auth-wrap {
    min-height: calc(100vh - 64px);
    background: linear-gradient(135deg, #0066CC 0%, #0099FF 100%);
    display: flex; align-items: center; justify-content: center;
    padding: 40px 16px;
    position: relative; overflow: hidden;
}
.auth-wrap::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.auth-card {
    background: #fff;
    border-radius: 16px;
    padding: 40px;
    width: 100%; max-width: 440px;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
    position: relative;
}
.auth-logo { text-align: center; margin-bottom: 28px; }
.auth-logo .logo-text { font-size: 26px; font-weight: 800; color: var(--tn-blue); letter-spacing: -0.5px; }
.auth-logo .logo-text span { color: var(--tn-orange); }
.auth-logo p { font-size: 13px; color: var(--tn-muted); margin-top: 4px; }
.auth-title { font-size: 20px; font-weight: 700; color: var(--tn-text); margin-bottom: 20px; }
.auth-label { font-size: 13px; font-weight: 600; color: var(--tn-text); margin-bottom: 5px; }
.auth-input {
    height: 46px; border: 1.5px solid var(--tn-border);
    border-radius: 8px; font-size: 14px; padding: 0 14px;
    width: 100%; transition: border-color .15s, box-shadow .15s;
}
.auth-input:focus {
    border-color: var(--tn-blue);
    box-shadow: 0 0 0 3px rgba(0,102,204,.1);
    outline: none;
}
.auth-input.is-invalid { border-color: #DC2626; }
.auth-error { font-size: 12px; color: #DC2626; margin-top: 4px; }
.auth-btn {
    width: 100%; height: 46px;
    background: var(--tn-blue); color: #fff;
    border: none; border-radius: 8px;
    font-size: 15px; font-weight: 600;
    cursor: pointer; transition: filter .15s;
}
.auth-btn:hover { filter: brightness(1.08); }
.auth-btn-orange { background: var(--tn-orange); }
.auth-divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
.auth-divider hr { flex: 1; border-color: var(--tn-border); }
.auth-divider span { font-size: 12px; color: var(--tn-muted); white-space: nowrap; }
.auth-link { color: var(--tn-blue); text-decoration: none; font-size: 13px; font-weight: 500; }
.auth-link:hover { text-decoration: underline; }
.input-wrap { position: relative; margin-bottom: 16px; }
.input-wrap .toggle-pw {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    background: none; border: none; color: var(--tn-muted); cursor: pointer; font-size: 16px;
}
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-text"><i class="bi bi-compass"></i> Travel<span>Nice</span></div>
            <p>Đăng nhập để tiếp tục khám phá</p>
        </div>

        @if(session('status'))
        <div class="alert alert-success mb-3" style="font-size:13px;border-radius:8px">{{ session('status') }}</div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger mb-3" style="font-size:13px;border-radius:8px">
            <i class="bi bi-exclamation-circle me-1"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-wrap">
                <label class="auth-label">Email</label>
                <input type="email" name="email" class="auth-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email') }}" placeholder="email@example.com" autofocus required>
            </div>

            <div class="input-wrap">
                <label class="auth-label">Mật khẩu</label>
                <input type="password" name="password" id="pwInput"
                       class="auth-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                       placeholder="••••••••" required autocomplete="current-password">
                <button type="button" class="toggle-pw" onclick="togglePw()">
                    <i class="bi bi-eye" id="pwIcon"></i>
                </button>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-20" style="margin-bottom:20px">
                <label class="d-flex align-items-center gap-2" style="font-size:13px;cursor:pointer">
                    <input type="checkbox" name="remember" style="cursor:pointer">
                    Ghi nhớ đăng nhập
                </label>
                @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">Quên mật khẩu?</a>
                @endif
            </div>

            <button type="submit" class="auth-btn">
                <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
            </button>
        </form>

        <div class="auth-divider"><hr><span>Chưa có tài khoản?</span><hr></div>

        <a href="{{ route('register') }}" class="auth-btn auth-btn-orange d-flex align-items-center justify-content-center gap-2 text-decoration-none" style="border-radius:8px;font-size:15px;font-weight:600">
            <i class="bi bi-person-plus"></i> Đăng ký miễn phí
        </a>

        <p class="text-center mt-3 mb-0" style="font-size:12px;color:var(--tn-muted)">
            Bằng cách đăng nhập, bạn đồng ý với
            <a href="#" class="auth-link">Điều khoản dịch vụ</a> của TravelNice
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePw() {
    const input = document.getElementById('pwInput');
    const icon  = document.getElementById('pwIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endpush