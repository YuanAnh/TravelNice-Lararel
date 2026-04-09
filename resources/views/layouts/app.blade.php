<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TravelNice — Đặt Tour Du Lịch')</title>

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --tn-blue:    #0066CC;
            --tn-blue-dk: #0052A5;
            --tn-orange:  #FF6B00;
            --tn-gray:    #F5F6F8;
            --tn-text:    #1A1A2E;
            --tn-muted:   #6B7280;
            --tn-border:  #E5E7EB;
            --tn-nav-h:   64px;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            color: var(--tn-text);
            background: #fff;
            padding-top: var(--tn-nav-h);
        }

        /* ===== NAVBAR ===== */
        .tn-navbar {
            height: var(--tn-nav-h);
            background: var(--tn-blue);
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
        }
        .tn-navbar .navbar-brand img { height: 36px; }
        .tn-navbar .nav-link {
            color: rgba(255,255,255,.9) !important;
            font-size: 14px; font-weight: 500;
            padding: 6px 12px !important;
            border-radius: 6px;
            transition: background .15s;
            display: flex; align-items: center; gap: 5px;
        }
        .tn-navbar .nav-link:hover { background: rgba(255,255,255,.15); color: #fff !important; }
        .tn-navbar .nav-link i { font-size: 16px; }
        .tn-navbar .nav-actions { display: flex; align-items: center; gap: 6px; }
        .tn-navbar .btn-login {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            color: #fff; font-size: 13px; font-weight: 500;
            padding: 5px 14px; border-radius: 20px;
            transition: background .15s;
        }
        .tn-navbar .btn-login:hover { background: rgba(255,255,255,.28); color: #fff; }
        .tn-navbar .btn-register {
            background: var(--tn-orange);
            border: none; color: #fff;
            font-size: 13px; font-weight: 600;
            padding: 5px 14px; border-radius: 20px;
            transition: filter .15s;
        }
        .tn-navbar .btn-register:hover { filter: brightness(1.1); }

        /* Dropdown */
        .tn-navbar .dropdown-menu {
            border: none; border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
            padding: 6px;
            min-width: 200px;
        }
        .tn-navbar .dropdown-item {
            font-size: 13px; border-radius: 6px; padding: 8px 12px;
            display: flex; align-items: center; gap: 8px;
        }
        .tn-navbar .dropdown-item:hover { background: var(--tn-gray); }
        .tn-navbar .dropdown-divider { margin: 4px 0; }

        /* ===== SEARCH HERO (dùng trong trang chủ) ===== */
        .tn-hero {
            background: linear-gradient(135deg, var(--tn-blue) 0%, #0099FF 100%);
            padding: 48px 0 0;
            position: relative; overflow: hidden;
        }
        .tn-hero::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        .tn-hero h1 {
            color: #fff; font-size: 32px; font-weight: 700;
            text-shadow: 0 2px 8px rgba(0,0,0,.2);
        }
        .tn-hero p { color: rgba(255,255,255,.85); font-size: 15px; }

        /* Search tabs */
        .tn-search-tabs { border: none; gap: 4px; }
        .tn-search-tabs .nav-link {
            color: rgba(255,255,255,.75) !important;
            font-size: 14px; font-weight: 500;
            border: none; border-radius: 8px 8px 0 0;
            padding: 10px 20px;
            background: rgba(255,255,255,.12);
            transition: all .15s;
        }
        .tn-search-tabs .nav-link.active {
            color: var(--tn-blue) !important;
            background: #fff !important;
        }
        .tn-search-tabs .nav-link i { margin-right: 6px; }

        /* Search box */
        .tn-search-box {
            background: #fff;
            border-radius: 0 12px 12px 12px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,.15);
        }
        .tn-search-box .form-control, .tn-search-box .form-select {
            border: 1.5px solid var(--tn-border);
            border-radius: 8px; font-size: 14px;
            height: 48px; padding: 0 14px;
        }
        .tn-search-box .form-control:focus, .tn-search-box .form-select:focus {
            border-color: var(--tn-blue); box-shadow: 0 0 0 3px rgba(0,102,204,.1);
        }
        .tn-search-box label { font-size: 12px; font-weight: 600; color: var(--tn-muted); margin-bottom: 4px; }
        .tn-search-box .input-icon { position: relative; }
        .tn-search-box .input-icon i {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: var(--tn-blue); font-size: 18px; z-index: 1;
        }
        .tn-search-box .input-icon .form-control { padding-left: 40px; }
        .tn-btn-search {
            background: var(--tn-orange); color: #fff; border: none;
            height: 48px; border-radius: 8px; font-weight: 600; font-size: 15px;
            width: 100%; transition: filter .15s;
        }
        .tn-btn-search:hover { filter: brightness(1.08); color: #fff; }

        /* ===== SECTION TITLES ===== */
        .tn-section-title {
            font-size: 22px; font-weight: 700; color: var(--tn-text);
        }
        .tn-section-title span { color: var(--tn-orange); }
        .tn-section-subtitle { font-size: 14px; color: var(--tn-muted); margin-top: 4px; }

        /* ===== TOUR CARD ===== */
        .tn-tour-card {
            border: 1px solid var(--tn-border);
            border-radius: 12px; overflow: hidden;
            transition: box-shadow .2s, transform .2s;
            background: #fff;
        }
        .tn-tour-card:hover {
            box-shadow: 0 8px 28px rgba(0,0,0,.12);
            transform: translateY(-3px);
        }
        .tn-tour-card .card-img-wrap { position: relative; overflow: hidden; height: 180px; }
        .tn-tour-card .card-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
        .tn-tour-card:hover .card-img-wrap img { transform: scale(1.05); }
        .tn-tour-card .badge-hot {
            position: absolute; top: 10px; left: 10px;
            background: var(--tn-orange); color: #fff;
            font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px;
        }
        .tn-tour-card .badge-discount {
            position: absolute; top: 10px; right: 10px;
            background: #E53E3E; color: #fff;
            font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px;
        }
        .tn-tour-card .btn-wishlist {
            position: absolute; bottom: 10px; right: 10px;
            background: rgba(255,255,255,.9); border: none;
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: var(--tn-muted); transition: all .15s;
        }
        .tn-tour-card .btn-wishlist:hover { background: #fff; color: #E53E3E; }
        .tn-tour-card .card-body { padding: 14px; }
        .tn-tour-card .tour-name {
            font-size: 14px; font-weight: 600; color: var(--tn-text);
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
            margin-bottom: 6px; line-height: 1.4;
        }
        .tn-tour-card .tour-meta { font-size: 12px; color: var(--tn-muted); display: flex; gap: 12px; margin-bottom: 8px; }
        .tn-tour-card .tour-meta i { color: var(--tn-blue); }
        .tn-tour-card .tour-price-old { font-size: 12px; color: var(--tn-muted); text-decoration: line-through; }
        .tn-tour-card .tour-price { font-size: 18px; font-weight: 700; color: var(--tn-orange); }
        .tn-tour-card .tour-price small { font-size: 11px; font-weight: 400; color: var(--tn-muted); }
        .tn-tour-card .tour-rating { font-size: 12px; color: var(--tn-muted); }
        .tn-tour-card .tour-rating .stars { color: #F59E0B; }
        .tn-tour-card .btn-book {
            background: var(--tn-blue); color: #fff; border: none;
            border-radius: 6px; font-size: 13px; font-weight: 600;
            padding: 7px 16px; transition: background .15s;
        }
        .tn-tour-card .btn-book:hover { background: var(--tn-blue-dk); color: #fff; }

        /* ===== CATEGORY PILLS ===== */
        .tn-dest-link {
            display: block; padding: 8px 0;
            font-size: 13px; color: var(--tn-text);
            text-decoration: none; transition: color .15s;
        }
        .tn-dest-link:hover { color: var(--tn-blue); }

        /* ===== ALERT / FLASH ===== */
        .tn-flash {
            position: fixed; top: 80px; right: 20px; z-index: 9999;
            min-width: 280px; border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
        }

        /* ===== FOOTER ===== */
        .tn-footer {
            background: var(--tn-blue);
            color: rgba(255,255,255,.85);
            padding: 48px 0 24px;
            margin-top: 60px;
        }
        .tn-footer h6 { color: #fff; font-weight: 700; font-size: 13px; letter-spacing: .5px; text-transform: uppercase; margin-bottom: 14px; }
        .tn-footer a { color: rgba(255,255,255,.75); font-size: 13px; text-decoration: none; display: block; margin-bottom: 6px; }
        .tn-footer a:hover { color: #fff; }
        .tn-footer .footer-logo { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .tn-footer .footer-logo span { color: var(--tn-orange); }
        .tn-footer .footer-desc { font-size: 13px; color: rgba(255,255,255,.7); line-height: 1.6; margin-top: 8px; }
        .tn-footer .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.15);
            margin-top: 32px; padding-top: 20px;
            font-size: 12px; color: rgba(255,255,255,.55);
        }
        .tn-footer .social-btn {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,.15); color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 16px; text-decoration: none; transition: background .15s;
        }
        .tn-footer .social-btn:hover { background: rgba(255,255,255,.3); color: #fff; }

        /* ===== BREADCRUMB ===== */
        .tn-breadcrumb { background: var(--tn-gray); padding: 10px 0; }
        .tn-breadcrumb .breadcrumb { margin: 0; font-size: 13px; }

        /* ===== PAGE CONTENT ===== */
        .tn-page { min-height: calc(100vh - var(--tn-nav-h) - 60px); }

        /* ===== CHAT WIDGET ===== */
        .tn-chat-widget {
            position: fixed; bottom: 24px; right: 24px; z-index: 999;
        }
        .tn-chat-btn {
            width: 54px; height: 54px; border-radius: 50%;
            background: var(--tn-blue); color: #fff; border: none;
            box-shadow: 0 4px 16px rgba(0,102,204,.4);
            font-size: 22px; display: flex; align-items: center; justify-content: center;
            transition: transform .2s;
        }
        .tn-chat-btn:hover { transform: scale(1.1); }
        .tn-chat-bubble {
            position: absolute; bottom: 62px; right: 0;
            background: #fff; border-radius: 12px 12px 0 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
            padding: 10px 14px; font-size: 13px; font-weight: 500;
            white-space: nowrap; color: var(--tn-text);
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ===== NAVBAR ===== --}}
<nav class="tn-navbar navbar navbar-expand-lg">
    <div class="container">
        {{-- Logo --}}
        <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-800 fs-5 text-decoration-none" href="{{ url('/') }}">
            <i class="bi bi-compass fs-4"></i>
            <span>Travel<span style="color:#FF6B00">Nice</span></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" style="color:#fff">
            <i class="bi bi-list fs-4"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto gap-1 ms-3">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-map"></i> Tour du lịch
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('tours.index', ['duration[]' => '7-10']) }}"><i class="bi bi-globe-asia-australia text-primary"></i> Du lịch nước ngoài</a></li>
                        <li><a class="dropdown-item" href="{{ route('tours.index', ['duration[]' => '1-3']) }}"><i class="bi bi-geo-alt text-success"></i> Du lịch trong nước</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('tours.index', ['sort' => 'popular']) }}"><i class="bi bi-fire text-danger"></i> Tour HOT giảm giá</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-airplane"></i> Vé máy bay</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-building"></i> Khách sạn</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-gift"></i> Combo khuyến mại</a>
                </li>
            </ul>

            <div class="nav-actions">
                @auth
                    <div class="dropdown">
                        <button class="btn btn-login dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            {{ Str::limit(auth()->user()->name, 12) }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person"></i> Hồ sơ của tôi</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}#bookings"><i class="bi bi-calendar-check"></i> Đơn đặt tour</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}#wishlist"><i class="bi bi-heart"></i> Yêu thích</a></li>
                            @if(auth()->user()->hasRole('admin'))
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-primary" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-check"></i> Quản trị</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right"></i> Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-login"><i class="bi bi-person"></i> Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn btn-register">Đăng ký</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
@if(session('success'))
<div class="tn-flash alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="tn-flash alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Main content --}}
<main class="tn-page">
    @yield('content')
</main>

{{-- ===== FOOTER ===== --}}
<footer class="tn-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-logo"><i class="bi bi-compass"></i> Travel<span>Nice</span></div>
                <p class="footer-desc">Nền tảng đặt tour du lịch uy tín hàng đầu Việt Nam. Hàng nghìn tour trong nước và quốc tế với giá tốt nhất.</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="social-btn"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-btn"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="social-btn"><i class="bi bi-tiktok"></i></a>
                    <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Tour du lịch</h6>
                <a href="#">Du lịch nước ngoài</a>
                <a href="#">Du lịch trong nước</a>
                <a href="#">Vé máy bay</a>
                <a href="#">Khách sạn</a>
                <a href="#">Combo du lịch</a>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Hỗ trợ</h6>
                <a href="#">Về chúng tôi</a>
                <a href="#">Liên hệ</a>
                <a href="#">Chính sách</a>
                <a href="#">Điều khoản</a>
                <a href="#">Hướng dẫn đặt tour</a>
            </div>
            <div class="col-lg-4">
                <h6>Liên hệ</h6>
                <p class="footer-desc mb-2"><i class="bi bi-geo-alt me-2"></i>Đường Phan Tây Nhạc, Xuân Phương, Hà Nội</p>
                <p class="footer-desc mb-2"><i class="bi bi-telephone me-2"></i>097 8023 211</p>
                <p class="footer-desc mb-2"><i class="bi bi-envelope me-2"></i>support@travelnice.vn</p>
                <p class="footer-desc"><i class="bi bi-clock me-2"></i>8:00 – 22:00 (Thứ 2 – Chủ nhật)</p>
            </div>
        </div>
        <div class="footer-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>© {{ date('Y') }} TravelNice. Bảo lưu mọi quyền.</span>
            <span>Được xây dựng bởi sinh viên ĐHCNĐA</span>
        </div>
    </div>
</footer>

{{-- Chat widget --}}
<div class="tn-chat-widget">
    <div class="tn-chat-bubble">💬 Tư vấn AI miễn phí</div>
    <button class="tn-chat-btn mt-2">
        <i class="bi bi-chat-dots-fill"></i>
    </button>
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Auto-hide flash --}}
<script>
    setTimeout(() => {
        document.querySelectorAll('.tn-flash').forEach(el => {
            const alert = bootstrap.Alert.getOrCreateInstance(el);
            alert.close();
        });
    }, 4000);
</script>

@stack('scripts')
</body>
</html>