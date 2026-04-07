@extends('layouts.app')

@section('title', $tour->title . ' — TravelNice')

@push('styles')
<style>
    .tour-gallery { position: relative; }
    .tour-gallery .main-img { border-radius: 12px; overflow: hidden; height: 380px; }
    .tour-gallery .main-img img { width: 100%; height: 100%; object-fit: cover; }
    .tour-gallery .thumb-list { display: flex; gap: 8px; margin-top: 8px; }
    .tour-gallery .thumb-list img { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid transparent; transition: border-color .15s; }
    .tour-gallery .thumb-list img:hover, .tour-gallery .thumb-list img.active { border-color: var(--tn-blue); }

    .tour-sticky { position: sticky; top: 80px; }
    .tour-price-box { background: #fff; border: 1px solid var(--tn-border); border-radius: 12px; padding: 20px; }
    .tour-price-box .price-old { font-size: 13px; color: var(--tn-muted); text-decoration: line-through; }
    .tour-price-box .price-new { font-size: 28px; font-weight: 700; color: var(--tn-orange); }

    .tn-tabs .nav-link { color: var(--tn-muted); font-size: 14px; font-weight: 500; border: none; border-bottom: 2px solid transparent; border-radius: 0; padding: 10px 16px; }
    .tn-tabs .nav-link.active { color: var(--tn-blue); border-bottom-color: var(--tn-blue); background: none; }

    .schedule-item { border-left: 2px solid var(--tn-border); padding-left: 16px; position: relative; margin-bottom: 16px; }
    .schedule-item::before { content: ''; position: absolute; left: -7px; top: 4px; width: 12px; height: 12px; background: var(--tn-blue); border-radius: 50%; }
    .schedule-item .day-badge { font-size: 12px; font-weight: 700; color: var(--tn-blue); margin-bottom: 4px; }
    .schedule-item .day-title { font-size: 14px; font-weight: 600; margin-bottom: 4px; }
    .schedule-item .day-desc { font-size: 13px; color: var(--tn-muted); line-height: 1.6; }

    .slot-item { border: 1.5px solid var(--tn-border); border-radius: 8px; padding: 10px 14px; cursor: pointer; transition: border-color .15s; }
    .slot-item:hover, .slot-item.selected { border-color: var(--tn-blue); background: #EEF5FF; }
    .slot-item .slot-date { font-size: 14px; font-weight: 600; }
    .slot-item .slot-info { font-size: 12px; color: var(--tn-muted); }
    .slot-item .slot-avail { font-size: 12px; font-weight: 600; color: #16A34A; }

    .review-item { border-bottom: 1px solid var(--tn-border); padding: 16px 0; }
    .review-item:last-child { border-bottom: none; }
    .review-stars { color: #F59E0B; }

    .tn-breadcrumb { background: #F5F6F8; padding: 10px 0; }
    .tn-breadcrumb .breadcrumb { margin: 0; font-size: 13px; }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="tn-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">{{ $tour->category->name ?? 'Tour du lịch' }}</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">{{ $tour->destination->name ?? 'Điểm đến' }}</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($tour->title, 40) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">

        {{-- LEFT: Gallery + Info --}}
        <div class="col-lg-8">

            {{-- Gallery --}}
            <div class="tour-gallery mb-4">
                <div class="main-img">
                    <img id="mainImg"
                        src="{{ $tour->thumbnail ?? 'https://placehold.co/800x400/0066CC/white?text=Tour' }}"
                        alt="{{ $tour->title }}">
                </div>
                @if($tour->images->count())
                <div class="thumb-list">
                    @foreach($tour->images->take(5) as $img)
                    <img src="{{ asset('storage/'.$img->image_path) }}"
                         onclick="document.getElementById('mainImg').src=this.src; document.querySelectorAll('.thumb-list img').forEach(i=>i.classList.remove('active')); this.classList.add('active')"
                         class="{{ $loop->first ? 'active' : '' }}">
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Title --}}
            <h1 class="fw-700 mb-2" style="font-size:22px;line-height:1.4">{{ $tour->title }}</h1>

            <div class="d-flex flex-wrap gap-3 mb-3" style="font-size:13px;color:var(--tn-muted)">
                <span><i class="bi bi-clock text-primary me-1"></i> {{ $tour->duration_days }} ngày {{ $tour->duration_days - 1 }} đêm</span>
                <span><i class="bi bi-geo-alt text-primary me-1"></i> {{ $tour->destination->name ?? '—' }}</span>
                <span><i class="bi bi-tag text-primary me-1"></i> {{ $tour->category->name ?? '—' }}</span>
                @if($tour->avg_rating)
                <span><i class="bi bi-star-fill text-warning me-1"></i> {{ $tour->avg_rating }}/5 ({{ $tour->reviews->count() }} đánh giá)</span>
                @endif
            </div>

            {{-- Tabs --}}
            <ul class="nav tn-tabs border-bottom mb-4" id="tourTab">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-intro">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-schedule">Lịch trình</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-price">Bảng giá</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-review">Đánh giá</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-faq">Hỏi đáp</a></li>
            </ul>

            <div class="tab-content">

                {{-- Giới thiệu --}}
                <div class="tab-pane fade show active" id="tab-intro">
                    <div style="font-size:14px;line-height:1.8;color:#374151">
                        {!! nl2br(e($tour->description)) !!}
                    </div>
                </div>

                {{-- Lịch trình --}}
                <div class="tab-pane fade" id="tab-schedule">
                    @forelse($tour->schedules as $schedule)
                    <div class="schedule-item">
                        <div class="day-badge">Ngày {{ $schedule->day_number }}</div>
                        <div class="day-title">{{ $schedule->title }}</div>
                        <div class="day-desc">{{ $schedule->description }}</div>
                    </div>
                    @empty
                    <p class="text-muted">Chưa có lịch trình chi tiết.</p>
                    @endforelse
                </div>

                {{-- Bảng giá --}}
                <div class="tab-pane fade" id="tab-price">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Đối tượng</th>
                                    <th>Giá</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="bi bi-person me-1"></i> Người lớn</td>
                                    <td class="fw-600 text-danger">{{ number_format($tour->price_adult, 0, ',', '.') }}đ</td>
                                    <td>Từ 12 tuổi trở lên</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-person-fill me-1"></i> Trẻ em</td>
                                    <td class="fw-600 text-danger">{{ number_format($tour->price_child, 0, ',', '.') }}đ</td>
                                    <td>Từ 5–11 tuổi</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-emoji-smile me-1"></i> Em bé</td>
                                    <td class="fw-600 text-success">Miễn phí</td>
                                    <td>Dưới 5 tuổi</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @if($tour->cancel_policy)
                    <div class="alert alert-warning mt-3" style="font-size:13px">
                        <i class="bi bi-info-circle me-2"></i><strong>Chính sách huỷ:</strong> {{ $tour->cancel_policy }}
                    </div>
                    @endif
                </div>

                {{-- Đánh giá --}}
                <div class="tab-pane fade" id="tab-review">
                    @forelse($tour->reviews as $review)
                    <div class="review-item">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:14px;font-weight:600">
                                {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-600" style="font-size:14px">{{ $review->user->name ?? 'Khách hàng' }}</div>
                                <div class="review-stars">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                            </div>
                            <div class="ms-auto" style="font-size:12px;color:var(--tn-muted)">{{ $review->created_at->diffForHumans() }}</div>
                        </div>
                        <p style="font-size:13px;color:#374151;margin:0">{{ $review->comment }}</p>
                    </div>
                    @empty
                    <p class="text-muted">Chưa có đánh giá nào.</p>
                    @endforelse
                </div>

                {{-- Hỏi đáp --}}
                <div class="tab-pane fade" id="tab-faq">
                    <div class="accordion" id="faqAccordion">
                        @foreach([
                            ['Visa có bao gồm trong giá tour không?', 'Chi phí visa chưa bao gồm trong giá tour. TravelNice hỗ trợ làm visa với phí dịch vụ riêng.'],
                            ['Tôi có thể huỷ tour sau khi đặt không?', 'Bạn có thể huỷ tour theo chính sách huỷ của từng tour. Vui lòng xem mục Bảng giá.'],
                            ['Phương tiện di chuyển trong tour là gì?', 'Xe du lịch đời mới, máy lạnh toàn trình. Một số điểm đi bộ hoặc xe điện.'],
                        ] as $i => $faq)
                        <div class="accordion-item border-0 mb-2" style="border-radius:8px!important;border:1px solid var(--tn-border)!important">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} fw-500" style="font-size:14px;border-radius:8px" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">
                                    {{ $faq[0] }}
                                </button>
                            </h2>
                            <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size:13px;color:var(--tn-muted)">{{ $faq[1] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- RIGHT: Sticky booking box --}}
        <div class="col-lg-4">
            <div class="tour-sticky">
                <div class="tour-price-box">
                    <div class="price-old">Giá gốc: {{ number_format($tour->price_adult * 1.15, 0, ',', '.') }}đ</div>
                    <div class="price-new">{{ number_format($tour->price_adult, 0, ',', '.') }}đ</div>
                    <div style="font-size:12px;color:var(--tn-muted)">/người lớn · đã bao gồm thuế</div>

                    <hr>

                    {{-- Slot picker --}}
                    @if($tour->slots->count())
                    <div class="mb-3">
                        <label class="fw-600 mb-2" style="font-size:13px">Chọn ngày khởi hành</label>
                        <div class="d-flex flex-column gap-2">
                            @foreach($tour->availableSlots()->take(3) as $slot)
                            <div class="slot-item {{ $loop->first ? 'selected' : '' }}" onclick="selectSlot(this, {{ $slot->id }})">
                                <div class="slot-date">{{ \Carbon\Carbon::parse($slot->departure_date)->format('d/m/Y') }}</div>
                                <div class="d-flex justify-content-between">
                                    <span class="slot-info">Khởi hành: {{ $slot->departure_from ?? 'Hà Nội' }}</span>
                                    <span class="slot-avail">Còn {{ $slot->available_slots }} chỗ</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <button class="btn w-100 fw-600 py-2 mb-2" style="background:var(--tn-orange);color:#fff;border-radius:8px;font-size:15px"
                        data-bs-toggle="modal" data-bs-target="#bookingModal">
                        <i class="bi bi-calendar-check me-1"></i> Gửi yêu cầu đặt tour
                    </button>
                    <button class="btn w-100 btn-outline-primary py-2" style="border-radius:8px;font-size:14px">
                        <i class="bi bi-heart me-1"></i> Thêm vào yêu thích
                    </button>

                    <hr>
                    <div class="d-flex flex-column gap-2" style="font-size:13px">
                        <div class="d-flex gap-2"><i class="bi bi-shield-check text-success"></i> Thanh toán an toàn, bảo mật</div>
                        <div class="d-flex gap-2"><i class="bi bi-arrow-counterclockwise text-primary"></i> Hoàn tiền theo chính sách</div>
                        <div class="d-flex gap-2"><i class="bi bi-headset text-warning"></i> Hỗ trợ 24/7: 024 3555 2008</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ===== BOOKING MODAL ===== --}}
@include('tours.partials.booking-modal', ['tour' => $tour])

@endsection

@push('scripts')
<script>
function selectSlot(el, slotId) {
    document.querySelectorAll('.slot-item').forEach(i => i.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('selectedSlot').value = slotId;
}
</script>
@endpush