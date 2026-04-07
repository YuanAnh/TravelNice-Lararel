@extends('layouts.app')
@section('title', 'Tài khoản của tôi — TravelNice')

@push('styles')
<style>
.profile-wrap { background: #F5F6F8; min-height: calc(100vh - 64px); padding: 32px 0; }

/* Sidebar */
.profile-sidebar { background: #fff; border-radius: 12px; border: 1px solid var(--tn-border); overflow: hidden; }
.profile-avatar-wrap { background: linear-gradient(135deg, #0066CC, #0099FF); padding: 28px; text-align: center; }
.profile-avatar { width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,.25); color: #fff; font-size: 32px; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; border: 3px solid rgba(255,255,255,.4); }
.profile-name { color: #fff; font-size: 16px; font-weight: 700; margin-bottom: 2px; }
.profile-email { color: rgba(255,255,255,.8); font-size: 12px; }
.profile-nav { padding: 8px; }
.profile-nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 8px; font-size: 14px; font-weight: 500; color: var(--tn-text); text-decoration: none; transition: all .15s; margin-bottom: 2px; cursor: pointer; border: none; background: none; width: 100%; }
.profile-nav-item:hover { background: #F5F6F8; color: var(--tn-blue); }
.profile-nav-item.active { background: #EEF5FF; color: var(--tn-blue); }
.profile-nav-item i { font-size: 18px; width: 20px; }
.profile-nav-item .badge-count { margin-left: auto; background: var(--tn-orange); color: #fff; font-size: 11px; padding: 1px 7px; border-radius: 10px; }

/* Content cards */
.profile-card { background: #fff; border-radius: 12px; border: 1px solid var(--tn-border); padding: 24px; margin-bottom: 16px; }
.profile-card-title { font-size: 16px; font-weight: 700; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--tn-border); display: flex; align-items: center; gap: 8px; }
.profile-card-title i { color: var(--tn-blue); }

/* Form */
.pf-label { font-size: 13px; font-weight: 600; color: var(--tn-text); margin-bottom: 5px; display: block; }
.pf-input { height: 44px; border: 1.5px solid var(--tn-border); border-radius: 8px; font-size: 14px; padding: 0 14px; width: 100%; transition: border-color .15s; }
.pf-input:focus { border-color: var(--tn-blue); box-shadow: 0 0 0 3px rgba(0,102,204,.1); outline: none; }
.pf-input:disabled { background: #F9FAFB; color: var(--tn-muted); }

/* Booking list */
.booking-item { border: 1px solid var(--tn-border); border-radius: 10px; padding: 16px; margin-bottom: 12px; transition: box-shadow .15s; }
.booking-item:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); }
.booking-code { font-size: 11px; font-weight: 700; color: var(--tn-muted); letter-spacing: .5px; }
.booking-tour-name { font-size: 15px; font-weight: 700; color: var(--tn-text); margin: 4px 0; }
.booking-meta { font-size: 12px; color: var(--tn-muted); display: flex; flex-wrap: wrap; gap: 12px; }
.booking-meta i { color: var(--tn-blue); }
.booking-price { font-size: 18px; font-weight: 700; color: var(--tn-orange); }
.booking-price small { font-size: 12px; color: var(--tn-muted); font-weight: 400; }

/* Status badges */
.status-badge { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
.status-pending   { background: #FEF3C7; color: #92400E; }
.status-paid      { background: #DBEAFE; color: #1E40AF; }
.status-confirmed { background: #D1FAE5; color: #065F46; }
.status-cancelled { background: #FEE2E2; color: #991B1B; }
.status-completed { background: #E0E7FF; color: #3730A3; }

/* Stats */
.stat-box { background: #F5F6F8; border-radius: 10px; padding: 16px; text-align: center; }
.stat-box .stat-num { font-size: 24px; font-weight: 800; color: var(--tn-blue); }
.stat-box .stat-label { font-size: 12px; color: var(--tn-muted); margin-top: 2px; }

/* Tab sections */
.tab-section { display: none; }
.tab-section.active { display: block; }
</style>
@endpush

@section('content')
<div class="profile-wrap">
    <div class="container">
        <div class="row g-4">

            {{-- ===== SIDEBAR ===== --}}
            <div class="col-lg-3">
                <div class="profile-sidebar">
                    <div class="profile-avatar-wrap">
                        <div class="profile-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="profile-name">{{ auth()->user()->name }}</div>
                        <div class="profile-email">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="profile-nav">
                        <button class="profile-nav-item active" onclick="showTab('tab-overview')">
                            <i class="bi bi-grid"></i> Tổng quan
                        </button>
                        <button class="profile-nav-item" onclick="showTab('tab-bookings')">
                            <i class="bi bi-calendar-check"></i> Đơn đặt tour
                            @if($pendingCount > 0)
                            <span class="badge-count">{{ $pendingCount }}</span>
                            @endif
                        </button>
                        <button class="profile-nav-item" onclick="showTab('tab-wishlist')">
                            <i class="bi bi-heart"></i> Yêu thích
                        </button>
                        <button class="profile-nav-item" onclick="showTab('tab-profile')">
                            <i class="bi bi-person"></i> Thông tin cá nhân
                        </button>
                        <button class="profile-nav-item" onclick="showTab('tab-password')">
                            <i class="bi bi-lock"></i> Đổi mật khẩu
                        </button>
                        <hr style="margin:8px 4px">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="profile-nav-item text-danger">
                                <i class="bi bi-box-arrow-right"></i> Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ===== MAIN CONTENT ===== --}}
            <div class="col-lg-9">

                {{-- Flash --}}
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px;font-size:14px">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                {{-- TAB: OVERVIEW --}}
                <div id="tab-overview" class="tab-section active">
                    <div class="profile-card">
                        <div class="profile-card-title"><i class="bi bi-grid"></i> Tổng quan tài khoản</div>
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-3">
                                <div class="stat-box">
                                    <div class="stat-num">{{ $totalBookings }}</div>
                                    <div class="stat-label">Tổng đơn đặt</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-box">
                                    <div class="stat-num" style="color:#16A34A">{{ $completedBookings }}</div>
                                    <div class="stat-label">Đã hoàn thành</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-box">
                                    <div class="stat-num" style="color:var(--tn-orange)">{{ $pendingCount }}</div>
                                    <div class="stat-label">Chờ xác nhận</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-box">
                                    <div class="stat-num" style="color:#9333EA">{{ $wishlistCount }}</div>
                                    <div class="stat-label">Tour yêu thích</div>
                                </div>
                            </div>
                        </div>

                        {{-- Recent bookings --}}
                        <div class="fw-600 mb-3" style="font-size:14px">Đơn đặt gần đây</div>
                        @forelse($recentBookings as $booking)
                        @include('profile.partials.booking-item', ['booking' => $booking])
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x" style="font-size:36px"></i>
                            <div class="mt-2">Bạn chưa có đơn đặt tour nào</div>
                            <a href="{{ route('tours.index') }}" class="btn btn-primary btn-sm mt-2 rounded-pill">Khám phá tour ngay</a>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- TAB: BOOKINGS --}}
                <div id="tab-bookings" class="tab-section">
                    <div class="profile-card">
                        <div class="profile-card-title"><i class="bi bi-calendar-check"></i> Lịch sử đặt tour</div>

                        {{-- Filter tabs --}}
                        <div class="d-flex gap-2 mb-4 flex-wrap">
                            @foreach([
                                ['all', 'Tất cả', $totalBookings],
                                ['pending', 'Chờ xác nhận', $pendingCount],
                                ['confirmed', 'Đã xác nhận', null],
                                ['completed', 'Hoàn thành', $completedBookings],
                                ['cancelled', 'Đã huỷ', null],
                            ] as $f)
                            <a href="{{ route('profile.index', ['status' => $f[0]]) }}"
                               class="btn btn-sm rounded-pill {{ request('status', 'all') === $f[0] ? 'btn-primary' : 'btn-outline-secondary' }}"
                               onclick="showTab('tab-bookings'); return true;">
                                {{ $f[1] }}
                                @if($f[2] !== null)<span class="ms-1">({{ $f[2] }})</span>@endif
                            </a>
                            @endforeach
                        </div>

                        @forelse($allBookings as $booking)
                        @include('profile.partials.booking-item', ['booking' => $booking])
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x" style="font-size:36px"></i>
                            <div class="mt-2">Không có đơn đặt nào</div>
                        </div>
                        @endforelse

                        <div class="mt-3">
                            {{ $allBookings->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>

                {{-- TAB: WISHLIST --}}
                <div id="tab-wishlist" class="tab-section">
                    <div class="profile-card">
                        <div class="profile-card-title"><i class="bi bi-heart"></i> Tour yêu thích</div>
                        @forelse($wishlists as $tour)
                        <div class="d-flex gap-3 align-items-center border rounded-3 p-3 mb-3">
                            <img src="{{ $tour->thumbnail ?? 'https://placehold.co/80x60/0066CC/white?text=Tour' }}"
                                 style="width:80px;height:60px;object-fit:cover;border-radius:8px">
                            <div class="flex-grow-1">
                                <a href="{{ route('tours.show', $tour->slug) }}" class="fw-600 text-decoration-none" style="font-size:14px">{{ $tour->title }}</a>
                                <div style="font-size:12px;color:var(--tn-muted)">{{ $tour->destination->name ?? '' }} · {{ $tour->duration_days }} ngày</div>
                                <div class="fw-700" style="color:var(--tn-orange);font-size:15px">{{ $tour->formattedPrice() }}</div>
                            </div>
                            <a href="{{ route('tours.show', $tour->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill">Xem tour</a>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-heart" style="font-size:36px"></i>
                            <div class="mt-2">Chưa có tour yêu thích</div>
                            <a href="{{ route('tours.index') }}" class="btn btn-primary btn-sm mt-2 rounded-pill">Khám phá tour</a>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- TAB: PROFILE --}}
                <div id="tab-profile" class="tab-section">
                    <div class="profile-card">
                        <div class="profile-card-title"><i class="bi bi-person"></i> Thông tin cá nhân</div>
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf @method('PATCH')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="pf-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="pf-input" value="{{ old('name', auth()->user()->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="pf-label">Email</label>
                                    <input type="email" class="pf-input" value="{{ auth()->user()->email }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="pf-label">Số điện thoại</label>
                                    <input type="tel" name="phone" class="pf-input" value="{{ old('phone', auth()->user()->phone) }}" placeholder="0912 345 678">
                                </div>
                                <div class="col-md-6">
                                    <label class="pf-label">Trạng thái tài khoản</label>
                                    <input type="text" class="pf-input" value="{{ auth()->user()->status === 'active' ? 'Đang hoạt động' : 'Bị khóa' }}" disabled>
                                </div>
                                <div class="col-12">
                                    <label class="pf-label">Địa chỉ</label>
                                    <input type="text" name="address" class="pf-input" value="{{ old('address', auth()->user()->address) }}" placeholder="Số nhà, đường, quận, thành phố">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary px-4 rounded-pill">
                                        <i class="bi bi-check2 me-1"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TAB: PASSWORD --}}
                <div id="tab-password" class="tab-section">
                    <div class="profile-card">
                        <div class="profile-card-title"><i class="bi bi-lock"></i> Đổi mật khẩu</div>
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf @method('PUT')
                            <div class="row g-3" style="max-width:440px">
                                <div class="col-12">
                                    <label class="pf-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                    <input type="password" name="current_password" class="pf-input {{ $errors->updatePassword->has('current_password') ? 'is-invalid':'' }}" required>
                                    @error('current_password', 'updatePassword')
                                    <div style="font-size:12px;color:#DC2626;margin-top:4px">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="pf-label">Mật khẩu mới <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="pf-input {{ $errors->updatePassword->has('password') ? 'is-invalid':'' }}" required>
                                    @error('password', 'updatePassword')
                                    <div style="font-size:12px;color:#DC2626;margin-top:4px">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="pf-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="pf-input" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary px-4 rounded-pill">
                                        <i class="bi bi-lock-fill me-1"></i> Đổi mật khẩu
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showTab(tabId) {
    document.querySelectorAll('.tab-section').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.profile-nav-item').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    event.currentTarget.classList.add('active');
}

// Auto-open tab from URL hash
const hash = window.location.hash;
if (hash === '#bookings') showTabById('tab-bookings');
if (hash === '#profile')  showTabById('tab-profile');

function showTabById(id) {
    document.querySelectorAll('.tab-section').forEach(el => el.classList.remove('active'));
    document.getElementById(id)?.classList.add('active');
}

// Auto-open bookings tab if status filter active
@if(request('status'))
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.tab-section').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-bookings').classList.add('active');
});
@endif
</script>
@endpush