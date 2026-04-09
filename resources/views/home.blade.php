@extends('layouts.app')
@section('title', 'TravelNice — Đặt Tour Du Lịch Giá Tốt Nhất')
@section('content')

{{-- HERO --}}
<section class="tn-hero">
    <div class="container">
        <div class="text-center mb-4">
            <h1 class="mb-2">Khám phá thế giới cùng TravelNice</h1>
            <p>Hàng nghìn tour du lịch giá tốt — đặt ngay, thanh toán sau</p>
        </div>
        <ul class="nav tn-search-tabs">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-tour"><i class="bi bi-map"></i> Tour du lịch</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-flight"><i class="bi bi-airplane"></i> Vé máy bay</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-hotel"><i class="bi bi-building"></i> Khách sạn</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-tour">
                <form action="{{ route('tours.index') }}" method="GET">
                    <div class="tn-search-box">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label>Điểm đến / Tên tour</label>
                                <div class="input-icon">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <input type="text" name="q" class="form-control" placeholder="Bạn muốn đi đâu?">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label>Danh mục</label>
                                <select name="category[]" class="form-select">
                                    <option value="">Tất cả danh mục</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Thời gian</label>
                                <select name="duration[]" class="form-select">
                                    <option value="">Tất cả</option>
                                    <option value="1-3">1–3 ngày</option>
                                    <option value="4-6">4–6 ngày</option>
                                    <option value="7-10">7–10 ngày</option>
                                    <option value="11+">Trên 10 ngày</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="tn-btn-search btn"><i class="bi bi-search me-1"></i> Tìm ngay</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="tab-flight">
                <div class="tn-search-box text-center py-4" style="color:#6B7280;font-size:14px">
                    <i class="bi bi-airplane fs-2 text-primary d-block mb-2"></i> Tính năng đặt vé máy bay sắp ra mắt!
                </div>
            </div>
            <div class="tab-pane fade" id="tab-hotel">
                <div class="tn-search-box text-center py-4" style="color:#6B7280;font-size:14px">
                    <i class="bi bi-building fs-2 text-primary d-block mb-2"></i> Tính năng đặt khách sạn sắp ra mắt!
                </div>
            </div>
        </div>
    </div>
    <div style="margin-top:32px;line-height:0;pointer-events:none">
    <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" style="pointer-events:none">
        <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#fff"/>
    </svg>
    </div>
</section>

{{-- PROMO --}}
<section class="py-4">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <div class="tn-section-title">Khuyến mại <span>HOT</span> tại TravelNice</div>
                <div class="tn-section-subtitle">Ưu đãi có hạn — đặt ngay kẻo lỡ!</div>
            </div>
            <a href="{{ route('tours.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="rounded-3 overflow-hidden" style="background:linear-gradient(135deg,#0066CC,#00AAFF);padding:20px;color:#fff;position:relative">
                    <div style="position:absolute;right:0;top:0;bottom:0;width:40%;background:rgba(255,255,255,.08);clip-path:polygon(20% 0,100% 0,100% 100%,0% 100%)"></div>
                    <div class="fw-700 fs-5 mb-1">Giảm đến 30%</div>
                    <div class="fw-600 mb-1">Tour Châu Âu hè 2025</div>
                    <div style="font-size:13px;opacity:.85">Ưu đãi đặc biệt mùa hè</div>
                    <a href="{{ route('tours.index', ['q'=>'Châu Âu']) }}" class="btn btn-sm mt-3" style="background:#FF6B00;color:#fff;border-radius:20px">Đặt ngay</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="rounded-3 overflow-hidden" style="background:linear-gradient(135deg,#FF6B00,#FF9900);padding:20px;color:#fff">
                    <div class="fw-700 fs-6 mb-1">Combo Đà Nẵng</div>
                    <div style="font-size:13px;opacity:.85">3N2Đ chỉ từ</div>
                    <div class="fw-700 fs-4">3.990.000đ</div>
                    <a href="{{ route('tours.index', ['q'=>'Đà Nẵng']) }}" class="btn btn-sm mt-2" style="background:rgba(255,255,255,.2);color:#fff;border-radius:20px">Xem ngay</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="rounded-3 overflow-hidden" style="background:linear-gradient(135deg,#1D9E75,#00CC88);padding:20px;color:#fff">
                    <div class="fw-700 fs-6 mb-1">Tour Phú Quốc</div>
                    <div style="font-size:13px;opacity:.85">4N3Đ chỉ từ</div>
                    <div class="fw-700 fs-4">5.490.000đ</div>
                    <a href="{{ route('tours.index', ['q'=>'Phú Quốc']) }}" class="btn btn-sm mt-2" style="background:rgba(255,255,255,.2);color:#fff;border-radius:20px">Xem ngay</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FEATURED TOURS --}}
<section class="py-4">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <div class="tn-section-title">Tour <span>nổi bật</span></div>
                <div class="tn-section-subtitle">Được yêu thích nhất tuần qua</div>
            </div>
            <a href="{{ route('tours.index', ['sort'=>'popular']) }}" class="btn btn-sm btn-outline-primary rounded-pill">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            @forelse($featuredTours as $tour)
            <div class="col-sm-6 col-lg-3">
                <div class="tn-tour-card">
                    <div class="card-img-wrap">
                        <img src="{{ $tour->thumbnail ? asset('storage/'.$tour->thumbnail) : 'https://placehold.co/400x240/0066CC/white?text='.urlencode($tour->destination->name??'Tour') }}" alt="{{ $tour->title }}">
                        @if($loop->index < 2)<span class="badge-hot">HOT</span>@endif
                        <button class="btn-wishlist" onclick="toggleWishlist(this, {{ $tour->id }})">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="tour-name">{{ $tour->title }}</div>
                        <div class="tour-meta">
                            <span><i class="bi bi-clock"></i> {{ $tour->duration_days }}N{{ $tour->duration_days-1 }}Đ</span>
                            <span><i class="bi bi-geo-alt"></i> {{ $tour->destination->name ?? 'Việt Nam' }}</span>
                        </div>
                        @if($tour->avg_rating > 0)
                        <div class="tour-rating mb-1">
                            <span class="stars">★★★★★</span> {{ $tour->avg_rating }}
                        </div>
                        @endif
                        <div class="d-flex align-items-end justify-content-between">
                            <div>
                                <div class="tour-price-old">{{ number_format($tour->price_adult*1.15,0,',','.') }}đ</div>
                                <div class="tour-price">{{ number_format($tour->price_adult,0,',','.') }}đ <small>/người</small></div>
                            </div>
                            <a href="{{ route('tours.show', $tour->slug) }}" class="btn btn-book">Đặt ngay</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-4 text-muted">
                <i class="bi bi-map" style="font-size:36px"></i>
                <div class="mt-2">Chưa có tour nào. <a href="{{ route('admin.tours.create') }}">Thêm tour mới</a></div>
            </div>
            @endforelse
        </div>
    </div>
</section>

{{-- DESTINATIONS --}}
<section class="py-4" style="background:#F5F6F8">
    <div class="container">
        <div class="tn-section-title mb-3">Điểm đến <span>phổ biến</span></div>
        <div class="row g-0">
            <div class="col-md-3">
                <div class="fw-600 text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Du lịch nước ngoài</div>
                <div class="row">
                    @foreach(['Trung Quốc','Hàn Quốc','Nhật Bản','Singapore','Thái Lan','Đài Loan'] as $dest)
                    <div class="col-6"><a href="{{ route('tours.index', ['q'=>$dest]) }}" class="tn-dest-link">{{ $dest }}</a></div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-1 d-none d-md-block" style="border-left:1px solid #E5E7EB"></div>
            <div class="col-md-3">
                <div class="fw-600 text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Du lịch trong nước</div>
                <div class="row">
                    @foreach(['Đà Nẵng','Nha Trang','Phú Quốc','Hạ Long','Sapa','Hội An'] as $dest)
                    <div class="col-6"><a href="{{ route('tours.index', ['q'=>$dest]) }}" class="tn-dest-link">{{ $dest }}</a></div>
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

@push('scripts')
<script>
function toggleWishlist(btn, tourId) {
    @auth
    fetch('/wishlist/' + tourId, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}
    }).then(r => r.json()).then(data => {
        const icon = btn.querySelector('i');
        icon.className = data.wishlisted ? 'bi bi-heart-fill text-danger' : 'bi bi-heart';
    }).catch(() => {});
    @else
    window.location = '{{ route("login") }}';
    @endauth
}
</script>
@endpush