<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — TravelNice</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --admin-sidebar: 240px;
            --admin-blue: #0066CC;
            --admin-dark: #1A1A2E;
            --admin-darker: #12122A;
            --admin-hover: rgba(255,255,255,.07);
            --admin-active: rgba(0,102,204,.25);
            --admin-border: #E5E7EB;
            --admin-orange: #FF6B00;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Be Vietnam Pro', sans-serif; background: #F5F6F8; margin: 0; }

        /* ===== SIDEBAR ===== */
        .admin-sidebar {
            width: var(--admin-sidebar);
            background: var(--admin-dark);
            position: fixed; top: 0; left: 0; bottom: 0;
            display: flex; flex-direction: column;
            z-index: 100; overflow-y: auto;
        }
        .sidebar-logo {
            padding: 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-logo .logo-text { font-size: 18px; font-weight: 800; color: #fff; }
        .sidebar-logo .logo-text span { color: var(--admin-orange); }
        .sidebar-logo .admin-badge { font-size: 10px; background: var(--admin-blue); color: #fff; padding: 2px 6px; border-radius: 4px; font-weight: 600; }
        .sidebar-section { padding: 16px 8px 4px; font-size: 10px; font-weight: 700; color: rgba(255,255,255,.3); text-transform: uppercase; letter-spacing: 1px; }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 16px; margin: 1px 8px;
            border-radius: 8px; color: rgba(255,255,255,.65);
            text-decoration: none; font-size: 13px; font-weight: 500;
            transition: all .15s;
        }
        .sidebar-link:hover { background: var(--admin-hover); color: #fff; }
        .sidebar-link.active { background: var(--admin-active); color: #fff; border-left: 2px solid var(--admin-blue); }
        .sidebar-link i { font-size: 16px; width: 18px; }
        .sidebar-link .badge { margin-left: auto; font-size: 10px; }
        .sidebar-footer { margin-top: auto; padding: 12px 8px; border-top: 1px solid rgba(255,255,255,.08); }

        /* ===== TOPBAR ===== */
        .admin-topbar {
            margin-left: var(--admin-sidebar);
            height: 56px; background: #fff;
            border-bottom: 1px solid var(--admin-border);
            display: flex; align-items: center;
            padding: 0 24px; gap: 12px;
            position: sticky; top: 0; z-index: 99;
        }
        .topbar-title { font-size: 16px; font-weight: 700; color: #1A1A2E; }
        .topbar-breadcrumb { font-size: 12px; color: #9CA3AF; }
        .topbar-actions { margin-left: auto; display: flex; align-items: center; gap: 8px; }
        .admin-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--admin-blue); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; cursor: pointer;
        }

        /* ===== CONTENT ===== */
        .admin-content { margin-left: var(--admin-sidebar); padding: 24px; min-height: 100vh; }

        /* ===== STAT CARDS ===== */
        .stat-card { background: #fff; border-radius: 12px; padding: 20px; border: 1px solid var(--admin-border); }
        .stat-card .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .stat-card .stat-num { font-size: 24px; font-weight: 800; margin: 8px 0 2px; }
        .stat-card .stat-label { font-size: 13px; color: #6B7280; }
        .stat-card .stat-change { font-size: 12px; margin-top: 4px; }

        /* ===== DATA TABLE ===== */
        .admin-table-wrap { background: #fff; border-radius: 12px; border: 1px solid var(--admin-border); overflow: hidden; }
        .admin-table-header { padding: 16px 20px; border-bottom: 1px solid var(--admin-border); display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .admin-table-title { font-size: 15px; font-weight: 700; }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { background: #F9FAFB; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #6B7280; padding: 10px 16px; text-align: left; border-bottom: 1px solid var(--admin-border); white-space: nowrap; }
        .admin-table td { padding: 12px 16px; border-bottom: 1px solid #F3F4F6; font-size: 13px; vertical-align: middle; }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover td { background: #F9FAFB; }
        .admin-table .tour-thumb { width: 56px; height: 42px; object-fit: cover; border-radius: 6px; }
        .admin-table .tour-name { font-weight: 600; color: #1A1A2E; max-width: 200px; }
        .admin-table .tour-name small { display: block; font-weight: 400; color: #6B7280; font-size: 12px; }

        /* Status badges */
        .badge-active   { background: #D1FAE5; color: #065F46; font-size: 11px; padding: 3px 8px; border-radius: 20px; font-weight: 600; }
        .badge-inactive { background: #FEE2E2; color: #991B1B; font-size: 11px; padding: 3px 8px; border-radius: 20px; font-weight: 600; }
        .badge-draft    { background: #FEF3C7; color: #92400E; font-size: 11px; padding: 3px 8px; border-radius: 20px; font-weight: 600; }

        /* Action buttons */
        .btn-action { width: 30px; height: 30px; border-radius: 6px; border: none; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; cursor: pointer; transition: all .15s; }
        .btn-action-edit   { background: #EEF5FF; color: var(--admin-blue); }
        .btn-action-delete { background: #FEE2E2; color: #DC2626; }
        .btn-action-view   { background: #F0FDF4; color: #16A34A; }
        .btn-action:hover  { filter: brightness(.9); }

        /* Form */
        .admin-form-card { background: #fff; border-radius: 12px; border: 1px solid var(--admin-border); padding: 24px; margin-bottom: 20px; }
        .admin-form-card h6 { font-size: 13px; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--admin-border); }
        .form-label { font-size: 13px; font-weight: 600; margin-bottom: 5px; }
        .form-control, .form-select { font-size: 13px; border: 1.5px solid var(--admin-border); border-radius: 8px; }
        .form-control:focus, .form-select:focus { border-color: var(--admin-blue); box-shadow: 0 0 0 3px rgba(0,102,204,.1); }
        .form-text { font-size: 12px; color: #9CA3AF; }

        /* Search */
        .admin-search { height: 36px; border: 1.5px solid var(--admin-border); border-radius: 8px; font-size: 13px; padding: 0 12px 0 36px; width: 220px; }
        .admin-search:focus { border-color: var(--admin-blue); outline: none; box-shadow: 0 0 0 3px rgba(0,102,204,.1); }
        .search-wrap { position: relative; }
        .search-wrap i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9CA3AF; font-size: 14px; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ===== SIDEBAR ===== --}}
<div class="admin-sidebar">
    <div class="sidebar-logo">
        <i class="bi bi-compass fs-5 text-white"></i>
        <div>
            <div class="logo-text">Travel<span>Nice</span></div>
            <span class="admin-badge">ADMIN</span>
        </div>
    </div>

    <div class="sidebar-section">Tổng quan</div>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid"></i> Dashboard
    </a>

    <div class="sidebar-section">Quản lý Tour</div>
    <a href="{{ route('admin.tours.index') }}" class="sidebar-link {{ request()->routeIs('admin.tours.*') ? 'active' : '' }}">
        <i class="bi bi-map"></i> Danh sách Tour
    </a>
    <a href="{{ route('admin.tours.create') }}" class="sidebar-link {{ request()->routeIs('admin.tours.create') ? 'active' : '' }}">
        <i class="bi bi-plus-circle"></i> Thêm Tour mới
    </a>

    <div class="sidebar-section">Vận hành</div>
    <a href="{{ route('admin.bookings.index') }}" class="sidebar-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
        <i class="bi bi-calendar-check"></i> Đặt Tour
    </a>
    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Người dùng
    </a>
    <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
        <i class="bi bi-star"></i> Đánh giá
        @php $pendingReviews = \App\Models\Review::where('is_approved', 0)->count(); @endphp
        @if($pendingReviews > 0)
        <span class="badge ms-auto" style="background:#DC2626;font-size:10px;padding:2px 6px;border-radius:10px">{{ $pendingReviews }}</span>
        @endif
    </a>

    <div class="sidebar-section">Nội dung</div>
    <a href="{{ route('admin.banners.index') }}" class="sidebar-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
        <i class="bi bi-images"></i> Banner trang chủ
    </a>

    <div class="sidebar-section">Hệ thống</div>
    <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart"></i> Báo cáo & Export
    </a>
    <a href="{{ route('admin.logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
        <i class="bi bi-journal-text"></i> Log hoạt động
    </a>

    <div class="sidebar-footer">
        <a href="{{ url('/') }}" class="sidebar-link" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i> Xem website
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-100 text-start border-0" style="color:rgba(255,100,100,.8);background:none">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
            </button>
        </form>
    </div>
</div>

{{-- ===== TOPBAR ===== --}}
<div class="admin-topbar">
    <div>
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <div class="topbar-breadcrumb">@yield('breadcrumb', 'Admin / Dashboard')</div>
    </div>
    <div class="topbar-actions">
        @if(session('success'))
        <div class="alert alert-success py-1 px-3 mb-0 d-flex align-items-center gap-2" style="font-size:13px;border-radius:8px">
            <i class="bi bi-check-circle"></i>{{ session('success') }}
        </div>
        @endif

        {{-- Notification Bell --}}
        @php $paidCount = \App\Models\Booking::where('status','paid')->count(); @endphp
        <div class="dropdown">
            <button class="btn position-relative p-2" data-bs-toggle="dropdown" style="background:none;border:none">
                <i class="bi bi-bell" style="font-size:20px;color:#6B7280"></i>
                @if($paidCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                      style="background:#DC2626;font-size:10px;padding:3px 5px">
                    {{ $paidCount }}
                </span>
                @endif
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="border-radius:12px;min-width:280px;box-shadow:0 8px 24px rgba(0,0,0,.12);border:none">
                <li class="px-3 py-2 border-bottom">
                    <div style="font-size:13px;font-weight:700">Thông báo</div>
                </li>
                @if($paidCount > 0)
                <li>
                    <a class="dropdown-item py-3" href="{{ route('admin.bookings.index', ['status'=>'paid']) }}"
                       style="white-space:normal">
                        <div class="d-flex gap-3 align-items-center">
                            <div style="width:36px;height:36px;border-radius:50%;background:#DBEAFE;color:#2563EB;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="bi bi-credit-card-fill"></i>
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:600">{{ $paidCount }} đơn chờ xác nhận</div>
                                <div style="font-size:12px;color:#9CA3AF">Khách đã thanh toán, cần xác nhận</div>
                            </div>
                        </div>
                    </a>
                </li>
                @else
                <li class="text-center py-4" style="font-size:13px;color:#9CA3AF">
                    <i class="bi bi-check-circle d-block mb-2 fs-4 text-success"></i>
                    Không có thông báo mới
                </li>
                @endif
                <li class="border-top">
                    <a class="dropdown-item text-center py-2" href="{{ route('admin.bookings.index') }}"
                       style="font-size:13px;color:#0066CC">
                        Xem tất cả đơn đặt →
                    </a>
                </li>
            </ul>
        </div>

        <div class="dropdown">
            <div class="admin-avatar" data-bs-toggle="dropdown">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <ul class="dropdown-menu dropdown-menu-end" style="font-size:13px;border-radius:10px;min-width:160px">
                <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i>Hồ sơ</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- ===== MAIN CONTENT ===== --}}
<div class="admin-content">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>