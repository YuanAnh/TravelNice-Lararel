@extends('layouts.app')
@section('title', 'Đăng ký — TravelNice')

@push('styles')
<style>
.auth-wrap {
    min-height: calc(100vh - 64px);
    background: linear-gradient(135deg, #0066CC 0%, #0099FF 100%);
    display: flex; align-items: center; justify-content: center;
    padding: 40px 16px; position: relative; overflow: hidden;
}
.auth-wrap::before {
    content: ''; position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.auth-card {
    background: #fff; border-radius: 16px; padding: 40px;
    width: 100%; max-width: 480px;
    box-shadow: 0 20px 60px rgba(0,0,0,.2); position: relative;
}
.auth-logo { text-align: center; margin-bottom: 24px; }
.auth-logo .logo-text { font-size: 26px; font-weight: 800; color: var(--tn-blue); }
.auth-logo .logo-text span { color: var(--tn-orange); }
.auth-logo p { font-size: 13px; color: var(--tn-muted); margin-top: 4px; }
.auth-label { font-size: 13px; font-weight: 600; color: var(--tn-text); margin-bottom: 5px; display: block; }
.auth-input {
    height: 46px; border: 1.5px solid var(--tn-border);
    border-radius: 8px; font-size: 14px; padding: 0 14px;
    width: 100%; transition: border-color .15s, box-shadow .15s;
}
.auth-input:focus { border-color: var(--tn-blue); box-shadow: 0 0 0 3px rgba(0,102,204,.1); outline: none; }
.auth-input.is-invalid { border-color: #DC2626; }
.auth-error { font-size: 12px; color: #DC2626; margin-top: 4px; }
.auth-btn {
    width: 100%; height: 46px; background: var(--tn-orange); color: #fff;
    border: none; border-radius: 8px; font-size: 15px; font-weight: 600;
    cursor: pointer; transition: filter .15s;
}
.auth-btn:hover { filter: brightness(1.08); }
.input-wrap { margin-bottom: 14px; position: relative; }
.input-wrap .toggle-pw {
    position: absolute; right: 12px; bottom: 12px;
    background: none; border: none; color: var(--tn-muted); cursor: pointer; font-size: 16px;
}
.auth-link { color: var(--tn-blue); text-decoration: none; font-size: 13px; font-weight: 500; }
.auth-link:hover { text-decoration: underline; }
.pw-strength { height: 4px; border-radius: 2px; margin-top: 6px; transition: all .3s; }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-text"><i class="bi bi-compass"></i> Travel<span>Nice</span></div>
            <p>Tạo tài khoản để đặt tour dễ dàng hơn</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger mb-3" style="font-size:13px;border-radius:8px">
            <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="input-wrap">
                <label class="auth-label">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" name="name" class="auth-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       value="{{ old('name') }}" placeholder="Nguyễn Văn A" autofocus required>
                @error('name')<div class="auth-error">{{ $message }}</div>@enderror
            </div>

            <div class="input-wrap">
                <label class="auth-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="auth-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email') }}" placeholder="email@example.com" required>
                @error('email')<div class="auth-error">{{ $message }}</div>@enderror
            </div>

            <div class="input-wrap">
                <label class="auth-label">Số điện thoại</label>
                <input type="tel" name="phone" class="auth-input {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                       value="{{ old('phone') }}" placeholder="0912 345 678">
                @error('phone')<div class="auth-error">{{ $message }}</div>@enderror
            </div>

            <div class="input-wrap">
                <label class="auth-label">Mật khẩu <span class="text-danger">*</span></label>
                <input type="password" name="password" id="pwInput"
                       class="auth-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                       placeholder="Tối thiểu 8 ký tự" required autocomplete="new-password"
                       oninput="checkStrength(this.value)">
                <button type="button" class="toggle-pw" onclick="togglePw('pwInput','pwIcon')">
                    <i class="bi bi-eye" id="pwIcon"></i>
                </button>
                <div class="pw-strength" id="pwStrength" style="background:var(--tn-border);width:0%"></div>
                @error('password')<div class="auth-error">{{ $message }}</div>@enderror
            </div>

            <div class="input-wrap">
                <label class="auth-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" id="pwConfirm"
                       class="auth-input"
                       placeholder="Nhập lại mật khẩu" required autocomplete="new-password">
                <button type="button" class="toggle-pw" onclick="togglePw('pwConfirm','pwIcon2')">
                    <i class="bi bi-eye" id="pwIcon2"></i>
                </button>
            </div>

            <div class="d-flex align-items-start gap-2 mb-3" style="font-size:13px">
                <input type="checkbox" id="agree" required style="margin-top:2px;cursor:pointer">
                <label for="agree" style="cursor:pointer">
                    Tôi đồng ý với <a href="#" class="auth-link">Điều khoản dịch vụ</a>
                    và <a href="#" class="auth-link">Chính sách bảo mật</a> của TravelNice
                </label>
            </div>

            <button type="submit" class="auth-btn">
                <i class="bi bi-person-check me-1"></i> Tạo tài khoản
            </button>
        </form>

        <p class="text-center mt-3 mb-0" style="font-size:13px;color:var(--tn-muted)">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="auth-link fw-600">Đăng nhập ngay</a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

function checkStrength(val) {
    const bar = document.getElementById('pwStrength');
    let score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['#DC2626','#F59E0B','#3B82F6','#16A34A'];
    const widths = ['25%','50%','75%','100%'];
    bar.style.width     = val.length ? widths[score - 1] || '10%' : '0%';
    bar.style.background = val.length ? colors[score - 1] || '#DC2626' : 'var(--tn-border)';
}
</script>
@endpush