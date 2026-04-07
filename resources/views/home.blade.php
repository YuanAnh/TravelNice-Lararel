@extends('layouts.app')

@section('title', 'TravelNice — Đặt Tour Du Lịch Giá Tốt Nhất')

@section('content')

{{-- ===== HERO / SEARCH ===== --}}
<section class="tn-hero">
    <div class="container">
        <div class="text-center mb-4">
            <h1 class="mb-2">Khám phá thế giới cùng TravelNice</h1>
            <p>Hàng nghìn tour du lịch giá tốt — đặt ngay, thanh toán sau</p>
        </div>

        {{-- Search tabs --}}
        <ul class="nav tn-search-tabs" id="searchTab">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-tour">
                    <i class="bi bi-map"></i> Tour du lịch
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-flight">
                    <i class="bi bi-airplane"></i> Vé máy bay
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-hotel">
                    <i class="bi bi-building"></i> Khách sạn
                </a>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Tour search --}}
            <div class="tab-pane fade show active" id="tab-tour">
                <div class="tn-search-box">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label>Điểm đến</label>
                            <div class="input-icon">
                                <i class="bi bi-geo-alt-fill"></i>
                                <input type="text" class="form-control" placeholder="Bạn muốn đi đâu?">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Khởi hành từ</label>
                            <select class="form-select">
                                <option>Vui lòng chọn</option>
                                <option>Hà Nội</option>
                                <option>TP. Hồ Chí Minh</option>
                                <option>Đà Nẵng</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Ngày khởi hành</label>
                            <div class="input-icon">
                                <i class="bi bi-calendar3"></i>
                                <input type="date" class="form-control" style="padding-left:40px">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="tn-btn-search btn">
                                <i class="bi bi-search me-1"></i> Tìm ngay
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flight search (placeholder) --}}
            <div class="tab-pane fade" id="tab-flight">
                <div class="tn-search-box">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label>Điểm đi</label>
                            <div class="input-icon">
                                <i class="bi bi-airplane-fill"></i>
                                <input type="text" class="form-control" placeholder="Thành phố / Sân bay">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Điểm đến</label>
                            <div class="input-icon">
                                <i class="bi bi-geo-alt-fill"></i>
                                <input type="text" class="form-control" placeholder="Thành phố / Sân bay">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>Ngày đi</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Hành khách</label>
                            <select class="form-select">
                                <option>1 người lớn</option>
                                <option>2 người lớn</option>
                                <option>3 người lớn</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="tn-btn-search btn">
                                <i class="bi bi-search me-1"></i> Tìm ngay
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hotel search (placeholder) --}}
            <div class="tab-pane fade" id="tab-hotel">
                <div class="tn-search-box">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label>Điểm đến / Khách sạn</label>
                            <div class="input-icon">
                                <i class="bi bi-building"></i>
                                <input type="text" class="form-control" placeholder="Bạn muốn ở đâu?">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>Nhận phòng</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Trả phòng</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Phòng & Khách</label>
                            <select class="form-select">
                                <option>1 phòng, 2 khách</option>
                                <option>2 phòng, 4 khách</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="tn-btn-search btn">
                                <i class="bi bi-search me-1"></i> Tìm ngay
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Wave bottom --}}
    <div style="margin-top:32px;line-height:0">
        <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#fff"/>
        </svg>
    </div>
</section>

{{-- ===== PROMO BANNERS ===== --}}
<section class="py-4">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <div class="tn-section-title">Khuyến mại <span>HOT</span> tại TravelNice</div>
                <div class="tn-section-subtitle">Ưu đãi có hạn — đặt ngay kẻo lỡ!</div>
            </div>
            <a href="#" class="btn btn-sm btn-outline-primary rounded-pill">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="rounded-3 overflow-hidden" style="background:linear-gradient(135deg,#0066CC,#00AAFF);padding:20px;color:#fff;position:relative">
                    <div style="position:absolute;right:0;top:0;bottom:0;width:40%;background:rgba(255,255,255,.08);clip-path:polygon(20% 0,100% 0,100% 100%,0% 100%)"></div>
                    <div class="fw-700 fs-5 mb-1">Giảm đến 30%</div>
                    <div class="fw-600 mb-1">Tour Châu Âu hè 2025</div>
                    <div style="font-size:13px;opacity:.85">Áp dụng đến 30/06/2025</div>
                    <a href="#" class="btn btn-sm mt-3" style="background:#FF6B00;color:#fff;border-radius:20px">Đặt ngay</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="rounded-3 overflow-hidden" style="background:linear-gradient(135deg,#FF6B00,#FF9900);padding:20px;color:#fff">
                    <div class="fw-700 fs-6 mb-1">Combo Đà Nẵng</div>
                    <div style="font-size:13px;opacity:.85">3N2Đ chỉ từ</div>
                    <div class="fw-700 fs-4">3.990.000đ</div>
                    <a href="#" class="btn btn-sm mt-2" style="background:rgba(255,255,255,.2);color:#fff;border-radius:20px">Xem ngay</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="rounded-3 overflow-hidden" style="background:linear-gradient(135deg,#1D9E75,#00CC88);padding:20px;color:#fff">
                    <div class="fw-700 fs-6 mb-1">Tour Phú Quốc</div>
                    <div style="font-size:13px;opacity:.85">4N3Đ chỉ từ</div>
                    <div class="fw-700 fs-4">5.490.000đ</div>
                    <a href="#" class="btn btn-sm mt-2" style="background:rgba(255,255,255,.2);color:#fff;border-radius:20px">Xem ngay</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== TOUR CARDS ===== --}}
<section class="py-4">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <div class="tn-section-title">Tour <span>nổi bật</span></div>
                <div class="tn-section-subtitle">Được yêu thích nhất tuần qua</div>
            </div>
            <a href="#" class="btn btn-sm btn-outline-primary rounded-pill">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            @forelse($featuredTours ?? [] as $tour)
            <div class="col-sm-6 col-lg-3">
                <div class="tn-tour-card">
                    <div class="card-img-wrap">
                        <img src="{{ $tour->thumbnail ?? 'https://placehold.co/400x240/0066CC/white?text=Tour' }}" alt="{{ $tour->name }}">
                        <span class="badge-hot">HOT</span>
                        <button class="btn-wishlist"><i class="bi bi-heart"></i></button>
                    </div>
                    <div class="card-body">
                        <div class="tour-name">{{ $tour->name }}</div>
                        <div class="tour-meta">
                            <span><i class="bi bi-clock"></i> {{ $tour->duration ?? '5N4Đ' }}</span>
                            <span><i class="bi bi-geo-alt"></i> {{ $tour->destination->name ?? 'Việt Nam' }}</span>
                        </div>
                        <div class="d-flex align-items-end justify-content-between">
                            <div>
                                <div class="tour-price-old">12.000.000đ</div>
                                <div class="tour-price">{{ number_format($tour->price ?? 9990000) }}đ <small>/người</small></div>
                            </div>
                            <a href="#" class="btn btn-book">Đặt ngay</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            {{-- Placeholder cards khi chưa có data --}}
            @foreach([
                ['name'=>'Tour Nhật Bản Mùa Hoa Anh Đào', 'price'=>'28.990.000', 'dest'=>'Nhật Bản', 'dur'=>'7N6Đ'],
                ['name'=>'Khám Phá Đà Nẵng – Hội An – Bà Nà', 'price'=>'4.590.000', 'dest'=>'Đà Nẵng', 'dur'=>'3N2Đ'],
                ['name'=>'Tour Phú Quốc Nghỉ Dưỡng 5 Sao', 'price'=>'5.990.000', 'dest'=>'Phú Quốc', 'dur'=>'4N3Đ'],
                ['name'=>'Hành Trình Châu Âu 10 Quốc Gia', 'price'=>'69.900.000', 'dest'=>'Châu Âu', 'dur'=>'14N13Đ'],
            ] as $i => $t)
            <div class="col-sm-6 col-lg-3">
                <div class="tn-tour-card">
                    <div class="card-img-wrap">
                        <img src="https://placehold.co/400x240/{{ ['0066CC','FF6B00','1D9E75','9333EA'][$i] }}/white?text={{ urlencode($t['dest']) }}" alt="{{ $t['name'] }}">
                        @if($i == 0)<span class="badge-hot">HOT</span>@endif
                        @if($i == 3)<span class="badge-discount">-15%</span>@endif
                        <button class="btn-wishlist"><i class="bi bi-heart"></i></button>
                    </div>
                    <div class="card-body">
                        <div class="tour-name">{{ $t['name'] }}</div>
                        <div class="tour-meta">
                            <span><i class="bi bi-clock"></i> {{ $t['dur'] }}</span>
                            <span><i class="bi bi-geo-alt"></i> {{ $t['dest'] }}</span>
                        </div>
                        <div class="tour-rating mb-2">
                            <span class="stars">★★★★★</span> 4.8 (120 đánh giá)
                        </div>
                        <div class="d-flex align-items-end justify-content-between">
                            <div>
                                <div class="tour-price">{{ $t['price'] }}đ <small>/người</small></div>
                            </div>
                            <a href="#" class="btn btn-book">Đặt ngay</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</section>

{{-- ===== DESTINATIONS ===== --}}
<section class="py-4" style="background:#F5F6F8">
    <div class="container">
        <div class="tn-section-title mb-3">Điểm đến <span>phổ biến</span></div>
        <div class="row g-0">
            <div class="col-md-3">
                <div class="fw-600 text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Du lịch nước ngoài</div>
                <div class="row">
                    @foreach(['Trung Quốc','Hàn Quốc','Nhật Bản','Singapore','Thái Lan','Đài Loan'] as $dest)
                    <div class="col-6"><a href="#" class="tn-dest-link">{{ $dest }}</a></div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-1 d-none d-md-block" style="border-left:1px solid #E5E7EB"></div>
            <div class="col-md-3">
                <div class="fw-600 text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Du lịch trong nước</div>
                <div class="row">
                    @foreach(['Đà Nẵng','Nha Trang','Phú Quốc','Hạ Long','Sapa','Hội An'] as $dest)
                    <div class="col-6"><a href="#" class="tn-dest-link">{{ $dest }}</a></div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-1 d-none d-md-block" style="border-left:1px solid #E5E7EB"></div>
            <div class="col-md-4">
                <div class="fw-600 text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Tại sao chọn TravelNice?</div>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex gap-3 align-items-center">
                        <i class="bi bi-shield-check fs-4 text-primary"></i>
                        <div><div class="fw-600" style="font-size:13px">Thanh toán an toàn</div><div style="font-size:12px;color:#6B7280">Bảo mật 100%</div></div>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <i class="bi bi-headset fs-4 text-success"></i>
                        <div><div class="fw-600" style="font-size:13px">Hỗ trợ 24/7</div><div style="font-size:12px;color:#6B7280">Tư vấn tận tâm</div></div>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <i class="bi bi-tag fs-4 text-warning"></i>
                        <div><div class="fw-600" style="font-size:13px">Giá tốt nhất</div><div style="font-size:12px;color:#6B7280">Cam kết hoàn tiền</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection