@extends('layouts.app')

@section('title', 'Danh sách Tour Du Lịch — TravelNice')

@push('styles')
<style>
    .filter-sidebar { position: sticky; top: 80px; }
    .filter-card { background: #fff; border: 1px solid var(--tn-border); border-radius: 12px; padding: 16px; margin-bottom: 16px; }
    .filter-card h6 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--tn-muted); margin-bottom: 12px; }
    .filter-check { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; cursor: pointer; }
    .filter-check input { cursor: pointer; }
    .filter-check label { font-size: 13px; cursor: pointer; flex: 1; }
    .filter-check .count { font-size: 11px; color: var(--tn-muted); background: var(--tn-gray); padding: 1px 6px; border-radius: 10px; }

    .price-range { display: flex; gap: 8px; align-items: center; }
    .price-range input { width: 100%; height: 4px; cursor: pointer; accent-color: var(--tn-blue); }

    .sort-bar { background: #fff; border: 1px solid var(--tn-border); border-radius: 10px; padding: 10px 16px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .sort-bar .sort-label { font-size: 13px; color: var(--tn-muted); }
    .sort-btn { font-size: 13px; padding: 4px 12px; border-radius: 20px; border: 1px solid var(--tn-border); background: #fff; cursor: pointer; transition: all .15s; color: var(--tn-text); text-decoration: none; }
    .sort-btn:hover, .sort-btn.active { background: var(--tn-blue); color: #fff; border-color: var(--tn-blue); }
    .result-count { margin-left: auto; font-size: 13px; color: var(--tn-muted); }

    .tour-list-card { display: flex; gap: 0; border: 1px solid var(--tn-border); border-radius: 12px; overflow: hidden; background: #fff; transition: box-shadow .2s, transform .2s; margin-bottom: 16px; }
    .tour-list-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.1); transform: translateY(-2px); }
    .tour-list-card .card-img { width: 240px; flex-shrink: 0; position: relative; overflow: hidden; }
    .tour-list-card .card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
    .tour-list-card:hover .card-img img { transform: scale(1.05); }
    .tour-list-card .badge-hot { position: absolute; top: 10px; left: 10px; background: var(--tn-orange); color: #fff; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px; }
    .tour-list-card .card-body { padding: 16px; flex: 1; display: flex; flex-direction: column; }
    .tour-list-card .tour-title { font-size: 16px; font-weight: 700; color: var(--tn-text); margin-bottom: 6px; line-height: 1.4; }
    .tour-list-card .tour-title:hover { color: var(--tn-blue); }
    .tour-list-card .tour-meta { font-size: 12px; color: var(--tn-muted); display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 8px; }
    .tour-list-card .tour-meta i { color: var(--tn-blue); }
    .tour-list-card .tour-desc { font-size: 13px; color: #6B7280; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; flex: 1; }
    .tour-list-card .card-footer-row { display: flex; align-items: flex-end; justify-content: space-between; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--tn-border); }
    .tour-list-card .price-box .price-old { font-size: 12px; color: var(--tn-muted); text-decoration: line-through; }
    .tour-list-card .price-box .price-new { font-size: 20px; font-weight: 700; color: var(--tn-orange); line-height: 1.2; }
    .tour-list-card .price-box small { font-size: 11px; color: var(--tn-muted); }

    @media (max-width: 576px) {
        .tour-list-card { flex-direction: column; }
        .tour-list-card .card-img { width: 100%; height: 180px; }
    }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="tn-breadcrumb">
    <div class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active">Tour du lịch</li>
        </ol>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">

        {{-- ===== SIDEBAR FILTER ===== --}}
        <div class="col-lg-3">
            <form method="GET" action="{{ route('tours.index') }}" id="filterForm">
                {{-- Giữ lại search & sort khi filter --}}
                <input type="hidden" name="q" value="{{ request('q') }}">
                <input type="hidden" name="sort" value="{{ request('sort', 'popular') }}">

                {{-- Danh mục --}}
                <div class="filter-card">
                    <h6>Danh mục</h6>
                    @foreach($categories as $cat)
                    <div class="filter-check">
                        <input type="checkbox" name="category[]" value="{{ $cat->id }}"
                               id="cat_{{ $cat->id }}" onchange="document.getElementById('filterForm').submit()"
                               {{ in_array($cat->id, (array)request('category')) ? 'checked' : '' }}>
                        <label for="cat_{{ $cat->id }}">{{ $cat->name }}</label>
                        <span class="count">{{ $cat->tours_count ?? 0 }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Điểm đến --}}
                <div class="filter-card">
                    <h6>Điểm đến</h6>
                    @foreach($destinations as $dest)
                    <div class="filter-check">
                        <input type="checkbox" name="destination[]" value="{{ $dest->id }}"
                               id="dest_{{ $dest->id }}" onchange="document.getElementById('filterForm').submit()"
                               {{ in_array($dest->id, (array)request('destination')) ? 'checked' : '' }}>
                        <label for="dest_{{ $dest->id }}">{{ $dest->name }}</label>
                    </div>
                    @endforeach
                </div>

                {{-- Thời gian --}}
                <div class="filter-card">
                    <h6>Thời gian</h6>
                    @foreach([
                        ['1-3', '1–3 ngày'],
                        ['4-6', '4–6 ngày'],
                        ['7-10', '7–10 ngày'],
                        ['11+', 'Trên 10 ngày'],
                    ] as $d)
                    <div class="filter-check">
                        <input type="checkbox" name="duration[]" value="{{ $d[0] }}"
                               id="dur_{{ $d[0] }}" onchange="document.getElementById('filterForm').submit()"
                               {{ in_array($d[0], (array)request('duration')) ? 'checked' : '' }}>
                        <label for="dur_{{ $d[0] }}">{{ $d[1] }}</label>
                    </div>
                    @endforeach
                </div>

                {{-- Khoảng giá --}}
                <div class="filter-card">
                    <h6>Khoảng giá (triệu đồng)</h6>
                    <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                        <span id="priceMin">{{ request('price_min', 0) / 1000000 }} tr</span>
                        <span id="priceMax">{{ request('price_max', 100000000) / 1000000 }} tr</span>
                    </div>
                    <input type="range" name="price_max" min="0" max="100000000" step="1000000"
                           value="{{ request('price_max', 100000000) }}"
                           oninput="document.getElementById('priceMax').textContent = (this.value/1000000) + ' tr'"
                           onchange="document.getElementById('filterForm').submit()"
                           style="width:100%">
                    <input type="hidden" name="price_min" value="{{ request('price_min', 0) }}">
                </div>

                {{-- Reset --}}
                @if(request()->hasAny(['category', 'destination', 'duration', 'price_max', 'q']))
                <a href="{{ route('tours.index') }}" class="btn btn-sm btn-outline-danger w-100 rounded-pill">
                    <i class="bi bi-x-circle me-1"></i> Xóa bộ lọc
                </a>
                @endif
            </form>
        </div>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="col-lg-9">

            {{-- Search bar --}}
            <form method="GET" action="{{ route('tours.index') }}" class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-primary"></i></span>
                    <input type="text" name="q" class="form-control border-start-0 ps-0"
                           placeholder="Tìm kiếm tour theo tên, điểm đến..."
                           value="{{ request('q') }}">
                    <button class="btn btn-primary px-4" type="submit">Tìm</button>
                </div>
            </form>

            {{-- Sort bar --}}
            <div class="sort-bar mb-3">
                <span class="sort-label">Sắp xếp:</span>
                @foreach([
                    ['popular', 'Phổ biến'],
                    ['price_asc', 'Giá thấp → cao'],
                    ['price_desc', 'Giá cao → thấp'],
                    ['newest', 'Mới nhất'],
                    ['rating', 'Đánh giá cao'],
                ] as $s)
                <a href="{{ request()->fullUrlWithQuery(['sort' => $s[0]]) }}"
                   class="sort-btn {{ request('sort', 'popular') === $s[0] ? 'active' : '' }}">
                    {{ $s[1] }}
                </a>
                @endforeach
                <span class="result-count">{{ $tours->total() }} kết quả</span>
            </div>

            {{-- Tour list --}}
            @forelse($tours as $tour)
            <div class="tour-list-card">
                <div class="card-img">
                    <img src="{{ $tour->thumbnail ? asset('storage/'.$tour->thumbnail) : 'https://placehold.co/240x180/0066CC/white?text=Tour' }}"
                        alt="{{ $tour->title }}">
                    @if($loop->index < 3)<span class="badge-hot">HOT</span>@endif
                </div>
                <div class="card-body">
                    <a href="{{ route('tours.show', $tour->slug) }}" class="tour-title text-decoration-none d-block">
                        {{ $tour->title }}
                    </a>
                    <div class="tour-meta">
                        <span><i class="bi bi-clock"></i> {{ $tour->duration_days }} ngày {{ $tour->duration_days - 1 }} đêm</span>
                        <span><i class="bi bi-geo-alt"></i> {{ $tour->destination->name ?? '—' }}</span>
                        <span><i class="bi bi-tag"></i> {{ $tour->category->name ?? '—' }}</span>
                        @if($tour->avg_rating)
                        <span><i class="bi bi-star-fill text-warning"></i> {{ $tour->avg_rating }}</span>
                        @endif
                    </div>
                    <div class="tour-desc">{{ Str::limit($tour->description, 120) }}</div>

                    {{-- Slot gần nhất --}}
                    @php $nextSlot = $tour->slots->where('status','open')->sortBy('departure_date')->first(); @endphp
                    @if($nextSlot)
                    <div style="font-size:12px;color:var(--tn-muted);margin-top:6px">
                        <i class="bi bi-calendar3 text-primary me-1"></i>
                        Khởi hành gần nhất: <strong>{{ $nextSlot->departure_date->format('d/m/Y') }}</strong>
                        — Còn <strong>{{ $nextSlot->remainingSlots() }}</strong> chỗ
                    </div>
                    @endif

                    <div class="card-footer-row">
                        <div class="price-box">
                            <div class="price-old">{{ number_format($tour->price_adult * 1.15, 0, ',', '.') }}đ</div>
                            <div class="price-new">{{ number_format($tour->price_adult, 0, ',', '.') }}đ <small>/người</small></div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary" title="Yêu thích">
                                <i class="bi bi-heart"></i>
                            </button>
                            <a href="{{ route('tours.show', $tour->slug) }}" class="btn btn-sm btn-primary px-3">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-search" style="font-size:48px;color:var(--tn-border)"></i>
                <div class="mt-3 fw-600" style="font-size:16px">Không tìm thấy tour nào</div>
                <div class="text-muted mt-1" style="font-size:13px">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</div>
                <a href="{{ route('tours.index') }}" class="btn btn-primary mt-3 rounded-pill">Xem tất cả tour</a>
            </div>
            @endforelse

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $tours->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>

@endsection