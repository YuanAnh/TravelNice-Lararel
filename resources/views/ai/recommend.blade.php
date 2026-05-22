@extends('layouts.app')
@section('title', 'Gợi ý tour AI — TravelNice')

@push('styles')
<style>
/* ── Hero ──────────────────────────────────────────────── */
.ai-hero {
    background: linear-gradient(135deg, #7C3AED 0%, #4F46E5 60%, #2563EB 100%);
    padding: 52px 0 44px;
    color: #fff;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.ai-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.ai-hero h1 { font-size: 30px; font-weight: 800; margin-bottom: 8px; position: relative; }
.ai-hero p  { font-size: 15px; opacity: .85; position: relative; }

/* ── Pref card ─────────────────────────────────────────── */
.pref-card {
    background: #fff;
    border-radius: 20px;
    padding: 28px 32px;
    box-shadow: 0 8px 32px rgba(0,0,0,.10);
    max-width: 700px;
    margin: -36px auto 28px;
    position: relative;
    z-index: 2;
}
.pref-label { font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .4px; }
.pref-label i { color: #7C3AED; margin-right: 5px; }
.pref-input { border: 1.5px solid #E5E7EB; border-radius: 10px; font-size: 14px; transition: border-color .2s, box-shadow .2s; }
.pref-input:focus { border-color: #7C3AED; box-shadow: 0 0 0 3px rgba(124,58,237,.12); outline: none; }

.btn-ai {
    background: linear-gradient(135deg, #7C3AED, #4F46E5);
    color: #fff; border: none; border-radius: 12px;
    font-size: 15px; font-weight: 700; padding: 13px 32px;
    width: 100%; transition: opacity .15s, transform .15s;
}
.btn-ai:hover { opacity: .92; color: #fff; transform: translateY(-1px); }
.btn-ai:disabled { opacity: .7; transform: none; }

/* ── Behavior insight panel ────────────────────────────── */
.behavior-panel {
    background: linear-gradient(135deg, #F5F3FF, #EDE9FE);
    border: 1px solid #DDD6FE;
    border-radius: 14px;
    padding: 16px 20px;
    max-width: 700px;
    margin: 0 auto 20px;
}
.behavior-panel .panel-title {
    font-size: 12px; font-weight: 800; color: #7C3AED;
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px;
}
.behavior-tag {
    display: inline-flex; align-items: center; gap: 4px;
    background: #fff; color: #4C1D95;
    border: 1px solid #C4B5FD;
    border-radius: 20px; font-size: 12px; font-weight: 600;
    padding: 3px 10px; margin: 3px 3px 3px 0;
}
.engagement-badge {
    display: inline-block;
    padding: 3px 12px; border-radius: 20px;
    font-size: 11px; font-weight: 700; letter-spacing: .3px;
}
.engagement-loyal     { background: #FEF3C7; color: #92400E; }
.engagement-converter { background: #D1FAE5; color: #065F46; }
.engagement-explorer  { background: #DBEAFE; color: #1E40AF; }
.engagement-new       { background: #F3F4F6; color: #6B7280; }

/* ── Result card ───────────────────────────────────────── */
.ai-result-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #E5E7EB;
    overflow: hidden;
    margin-bottom: 14px;
    transition: box-shadow .2s, transform .2s;
}
.ai-result-card:hover { box-shadow: 0 10px 32px rgba(0,0,0,.10); transform: translateY(-2px); }

.rank-badge {
    position: absolute; top: 10px; left: 10px;
    background: linear-gradient(135deg,#7C3AED,#4F46E5);
    color: #fff; font-size: 10px; font-weight: 800;
    padding: 3px 8px; border-radius: 20px; z-index: 1;
}

/* Match score bar */
.match-score-wrap { padding: 10px 16px 0; }
.match-score-label { font-size: 11px; font-weight: 700; color: #6B7280; margin-bottom: 4px; display: flex; justify-content: space-between; }
.match-score-label span { color: #7C3AED; font-size: 13px; }
.match-bar { height: 6px; background: #E5E7EB; border-radius: 99px; overflow: hidden; }
.match-bar-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #7C3AED, #4F46E5); transition: width 1s ease; }

/* AI reason */
.ai-reason {
    background: linear-gradient(135deg, #F5F3FF, #EDE9FE);
    border-left: 3px solid #7C3AED;
    padding: 10px 16px; font-size: 13px; color: #4C1D95;
    margin: 8px 16px 16px; border-radius: 0 10px 10px 0;
}

/* ── Skeleton loading ──────────────────────────────────── */
.skeleton { background: linear-gradient(90deg, #F3F4F6 25%, #E5E7EB 50%, #F3F4F6 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 8px; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

.skeleton-card {
    background: #fff; border-radius: 16px; border: 1px solid #E5E7EB;
    overflow: hidden; margin-bottom: 14px; padding: 0;
    display: none; /* shown via JS */
}
.skeleton-card .sk-img   { width: 180px; height: 130px; flex-shrink: 0; }
.skeleton-card .sk-title { height: 18px; width: 65%; margin-bottom: 10px; }
.skeleton-card .sk-meta  { height: 13px; width: 40%; margin-bottom: 8px; }
.skeleton-card .sk-price { height: 20px; width: 30%; }

/* ── Loading dots ──────────────────────────────────────── */
.loading-dots span {
    display: inline-block; width: 7px; height: 7px;
    border-radius: 50%; background: #fff; margin: 0 2px;
    animation: dot-bounce .8s infinite;
}
.loading-dots span:nth-child(2) { animation-delay: .15s; }
.loading-dots span:nth-child(3) { animation-delay: .30s; }
@keyframes dot-bounce { 0%,80%,100%{transform:scale(0.6);opacity:.5} 40%{transform:scale(1);opacity:1} }

/* ── Empty state ───────────────────────────────────────── */
.empty-state { text-align: center; padding: 60px 0; color: #9CA3AF; }
.empty-state i { font-size: 52px; opacity: .5; }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="ai-hero">
    <div class="container">
        <h1><i class="bi bi-stars me-2"></i>Gợi ý Tour bằng AI</h1>
        <p>AI phân tích sở thích & hành vi của bạn — gợi ý tour phù hợp nhất!</p>
    </div>
</div>

<div class="container">

    {{-- Behavior Insight Panel (chỉ hiện nếu đã đăng nhập và có dữ liệu) --}}
    @auth
    @if(!empty($behaviorProfile) && $behaviorProfile['total_events'] > 0)
    <div class="behavior-panel">
        <div class="panel-title"><i class="bi bi-graph-up me-1"></i>Hồ sơ sở thích của bạn</div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Engagement badge --}}
            @php
                $engMap = [
                    'loyal'     => ['⭐ Khách thân thiết', 'loyal'],
                    'converter' => ['✅ Đã từng đặt tour', 'converter'],
                    'explorer'  => ['🔍 Người khám phá', 'explorer'],
                    'new'       => ['👋 Thành viên mới', 'new'],
                ];
                [$engLabel, $engClass] = $engMap[$behaviorProfile['engagement']] ?? ['Mới', 'new'];
            @endphp
            <span class="engagement-badge engagement-{{ $engClass }}">{{ $engLabel }}</span>

            {{-- Điểm đến yêu thích --}}
            @foreach($behaviorProfile['top_destinations'] as $dest)
                <span class="behavior-tag"><i class="bi bi-geo-alt-fill" style="color:#7C3AED"></i>{{ ucwords($dest) }}</span>
            @endforeach

            {{-- Loại tour yêu thích --}}
            @foreach($behaviorProfile['top_categories'] as $cat)
                <span class="behavior-tag"><i class="bi bi-tag-fill" style="color:#7C3AED"></i>{{ ucwords($cat) }}</span>
            @endforeach

            {{-- Ngân sách trung bình --}}
            @if($behaviorProfile['avg_price'])
                <span class="behavior-tag"><i class="bi bi-cash-coin" style="color:#7C3AED"></i>
                    ~{{ number_format($behaviorProfile['avg_price'], 0, ',', '.') }}đ/tour
                </span>
            @endif

            <span style="font-size:11px;color:#7C3AED;margin-left:auto">
                {{ $behaviorProfile['total_events'] }} hoạt động · 30 ngày qua
            </span>
        </div>
        <div style="font-size:12px;color:#6D28D9;margin-top:8px">
            <i class="bi bi-lightning-fill me-1"></i>
            AI đã điền sẵn một số tiêu chí dựa trên sở thích của bạn. Bạn có thể thay đổi bên dưới.
        </div>
    </div>
    @endif
    @endauth

    {{-- Form preferences --}}
    <div class="pref-card">
        @if(isset($error))
        <div class="alert alert-danger mb-3" style="font-size:13px;border-radius:8px">
            <i class="bi bi-exclamation-circle me-2"></i>{{ $error }}
        </div>
        @endif

        <form method="POST" action="{{ route('ai.recommend') }}" id="recommendForm">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="pref-label"><i class="bi bi-cash-coin"></i>Ngân sách</label>
                    <select name="budget" class="form-select pref-input">
                        <option value="">Không giới hạn</option>
                        <option value="dưới 5 triệu"
                            @if(!empty($behaviorProfile['avg_price']) && $behaviorProfile['avg_price'] < 5000000) selected @endif>
                            Dưới 5 triệu đồng
                        </option>
                        <option value="5-15 triệu"
                            @if(!empty($behaviorProfile['avg_price']) && $behaviorProfile['avg_price'] >= 5000000 && $behaviorProfile['avg_price'] < 15000000) selected @endif>
                            5 – 15 triệu đồng
                        </option>
                        <option value="15-30 triệu"
                            @if(!empty($behaviorProfile['avg_price']) && $behaviorProfile['avg_price'] >= 15000000 && $behaviorProfile['avg_price'] < 30000000) selected @endif>
                            15 – 30 triệu đồng
                        </option>
                        <option value="trên 30 triệu"
                            @if(!empty($behaviorProfile['avg_price']) && $behaviorProfile['avg_price'] >= 30000000) selected @endif>
                            Trên 30 triệu đồng
                        </option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="pref-label"><i class="bi bi-clock"></i>Thời gian đi</label>
                    <select name="duration" class="form-select pref-input">
                        <option value="">Linh hoạt</option>
                        @php
                            $avgD = $behaviorProfile['avg_duration'] ?? null;
                        @endphp
                        <option value="1-3"    @if($avgD && $avgD <= 3)                          selected @endif>1 – 3 ngày</option>
                        <option value="4-6"    @if($avgD && $avgD >= 4 && $avgD <= 6)            selected @endif>4 – 6 ngày</option>
                        <option value="7-10"   @if($avgD && $avgD >= 7 && $avgD <= 10)           selected @endif>7 – 10 ngày</option>
                        <option value="trên 10" @if($avgD && $avgD > 10)                         selected @endif>Trên 10 ngày</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="pref-label"><i class="bi bi-geo-alt"></i>Điểm đến ưa thích</label>
                    <input type="text" name="destination" class="form-control pref-input"
                           placeholder="VD: Nhật Bản, Đà Nẵng, Châu Âu..."
                           value="{{ !empty($behaviorProfile['top_destinations']) ? ucwords($behaviorProfile['top_destinations'][0]) : '' }}">
                </div>

                <div class="col-md-6">
                    <label class="pref-label"><i class="bi bi-tag"></i>Loại hình du lịch</label>
                    <select name="type" class="form-select pref-input">
                        <option value="">Tất cả</option>
                        @php $favCat = mb_strtolower($behaviorProfile['top_categories'][0] ?? ''); @endphp
                        <option value="nghỉ dưỡng" @if(str_contains($favCat,'nghỉ') || str_contains($favCat,'dưỡng')) selected @endif>Nghỉ dưỡng, thư giãn</option>
                        <option value="khám phá"   @if(str_contains($favCat,'khám') || str_contains($favCat,'phá'))   selected @endif>Khám phá, mạo hiểm</option>
                        <option value="văn hóa"    @if(str_contains($favCat,'văn')  || str_contains($favCat,'hóa'))   selected @endif>Văn hóa, lịch sử</option>
                        <option value="mùa hoa"    @if(str_contains($favCat,'hoa')  || str_contains($favCat,'thiên')) selected @endif>Mùa hoa, thiên nhiên</option>
                        <option value="gia đình"   @if(str_contains($favCat,'gia')  || str_contains($favCat,'đình'))  selected @endif>Du lịch gia đình</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-ai" id="submitBtn">
                        <i class="bi bi-stars me-2"></i>Gợi ý tour cho tôi
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Skeleton loading (ẩn mặc định, hiện khi submit) --}}
    <div id="skeletonWrap" style="display:none; max-width:700px; margin:0 auto">
        @for($s = 0; $s < 3; $s++)
        <div class="ai-result-card skeleton-card" style="display:flex">
            <div class="skeleton sk-img"></div>
            <div class="p-3 flex-grow-1">
                <div class="skeleton sk-title"></div>
                <div class="skeleton sk-meta"></div>
                <div class="skeleton sk-price mt-2"></div>
            </div>
        </div>
        @endfor
        <p class="text-center mt-2" style="font-size:13px;color:#7C3AED">
            <span class="loading-dots" style="display:inline-flex; margin-right:6px">
                <span style="background:#7C3AED"></span><span style="background:#7C3AED"></span><span style="background:#7C3AED"></span>
            </span>
            AI đang phân tích hành vi & tìm tour phù hợp nhất cho bạn...
        </p>
    </div>

    {{-- Kết quả gợi ý --}}
    @if(isset($tours) && $tours->count())
    <div class="text-center mb-3" style="font-size:14px;color:#6B7280">
        <i class="bi bi-check-circle-fill text-success me-1"></i>
        AI tìm thấy <strong>{{ $tours->count() }}</strong> tour phù hợp nhất với bạn!
    </div>

    <div style="max-width:700px; margin:0 auto 60px">
        @foreach($tours as $i => $tour)
        <div class="ai-result-card">
            {{-- Match score bar --}}
            @if($tour->ai_match_score)
            <div class="match-score-wrap">
                <div class="match-score-label">
                    Độ phù hợp <span>{{ $tour->ai_match_score }}%</span>
                </div>
                <div class="match-bar">
                    <div class="match-bar-fill" style="width: 0%" data-width="{{ $tour->ai_match_score }}%"></div>
                </div>
            </div>
            @endif

            <div class="d-flex gap-0">
                {{-- Thumbnail --}}
                <div style="position:relative;flex-shrink:0;width:180px">
                    <span class="rank-badge"><i class="bi bi-stars"></i> #{{ $i + 1 }}</span>
                    <img src="{{ $tour->thumbnail ? asset('storage/'.$tour->thumbnail) : 'https://placehold.co/180x130/7C3AED/white?text=Tour' }}"
                         style="width:180px;height:130px;object-fit:cover;display:block" alt="{{ $tour->title }}">
                </div>

                {{-- Info --}}
                <div class="p-3 flex-grow-1">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <div style="font-size:15px;font-weight:700;color:#111827;margin-bottom:5px">
                                {{ $tour->title }}
                            </div>
                            <div style="font-size:12px;color:#6B7280">
                                <i class="bi bi-clock me-1"></i>{{ $tour->duration_days }}N{{ $tour->duration_days - 1 }}Đ
                                <i class="bi bi-geo-alt ms-2 me-1"></i>{{ $tour->destination->name ?? '' }}
                                <i class="bi bi-tag ms-2 me-1"></i>{{ $tour->category->name ?? '' }}
                            </div>
                            @if($tour->avg_rating)
                            <div style="font-size:12px;margin-top:4px">
                                @for($r = 1; $r <= 5; $r++)
                                    <i class="bi bi-star{{ $r <= round($tour->avg_rating) ? '-fill' : '' }}" style="color:#F59E0B;font-size:10px"></i>
                                @endfor
                                <span style="color:#9CA3AF;margin-left:3px">{{ number_format($tour->avg_rating, 1) }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div style="font-size:19px;font-weight:800;color:#FF6B00">
                                {{ number_format($tour->price_adult, 0, ',', '.') }}đ
                            </div>
                            <div style="font-size:11px;color:#9CA3AF">/người lớn</div>
                            <a href="{{ route('tours.show', $tour->slug) }}"
                               class="btn btn-sm btn-primary rounded-pill mt-2 px-3" style="font-size:12px">
                                Xem tour
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- AI reason --}}
            @if($tour->ai_reason)
            <div class="ai-reason">
                <i class="bi bi-lightbulb-fill me-1"></i>
                <strong>Tại sao phù hợp với bạn:</strong> {{ $tour->ai_reason }}
            </div>
            @endif
        </div>
        @endforeach

        {{-- CTA xem thêm --}}
        <div class="text-center mt-2">
            <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="font-size:14px">
                <i class="bi bi-grid me-1"></i>Xem tất cả tour
            </a>
        </div>
    </div>

    @elseif(isset($tours) && $tours->isEmpty())
    <div class="empty-state">
        <i class="bi bi-search"></i>
        <div class="mt-3 fw-bold" style="color:#374151">AI không tìm thấy tour phù hợp</div>
        <div style="font-size:13px;margin-top:4px">Thử thay đổi tiêu chí hoặc để trống để xem tất cả</div>
        <a href="{{ route('tours.index') }}" class="btn btn-primary rounded-pill mt-3 px-5">Xem tất cả tour</a>
    </div>
    @endif

</div>{{-- /container --}}

@endsection

@push('scripts')
<script>
// ── Submit: skeleton loading ──────────────────────────────────
document.getElementById('recommendForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<span class="loading-dots"><span></span><span></span><span></span></span> AI đang phân tích...';
    btn.disabled  = true;

    document.getElementById('skeletonWrap').style.display = 'block';
    document.getElementById('skeletonWrap').scrollIntoView({ behavior: 'smooth', block: 'start' });
});

// ── Animate match score bars on load ─────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        document.querySelectorAll('.match-bar-fill[data-width]').forEach(function (el) {
            el.style.width = el.dataset.width;
        });
    }, 300);
});

// ── Track tour view time (gửi lên server sau khi rời trang) ──
@auth
(function () {
    const viewStart = {};

    document.querySelectorAll('a[href*="/tours/"]').forEach(function (link) {
        const href = link.href;
        const card = link.closest('.ai-result-card');
        if (!card) return;

        // Lấy tour_id từ rank badge hoặc data attr nếu có
        const tourIdEl = card.querySelector('[data-tour-id]');
        if (!tourIdEl) return;
        const tourId = tourIdEl.dataset.tourId;

        link.addEventListener('mouseenter', function () { viewStart[tourId] = Date.now(); });
        link.addEventListener('click', function () {
            const secs = viewStart[tourId] ? Math.round((Date.now() - viewStart[tourId]) / 1000) : 0;
            navigator.sendBeacon('/ai/track', JSON.stringify({
                _token: '{{ csrf_token() }}',
                event: 'tour_view',
                tour_id: tourId,
                view_seconds: secs,
            }));
        });
    });
})();
@endauth
</script>
@endpush