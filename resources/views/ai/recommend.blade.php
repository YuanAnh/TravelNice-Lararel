@extends('layouts.app')
@section('title', 'Gợi ý tour AI — TravelNice')

@push('styles')
<style>
.ai-hero { background: linear-gradient(135deg, #7C3AED, #4F46E5); padding: 48px 0 40px; color: #fff; text-align: center; }
.ai-hero h1 { font-size: 28px; font-weight: 800; margin-bottom: 8px; }
.ai-hero p { font-size: 15px; opacity: .85; }

.pref-card { background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 4px 24px rgba(0,0,0,.08); max-width: 640px; margin: -32px auto 32px; position: relative; }
.pref-label { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 6px; display: flex; align-items: center; gap-6px; }
.pref-label i { color: #7C3AED; }
.pref-input { border: 1.5px solid #E5E7EB; border-radius: 10px; font-size: 14px; }
.pref-input:focus { border-color: #7C3AED; box-shadow: 0 0 0 3px rgba(124,58,237,.1); }
.btn-ai { background: linear-gradient(135deg,#7C3AED,#4F46E5); color:#fff; border:none; border-radius:10px; font-size:15px; font-weight:600; padding:12px 32px; width:100%; transition: opacity .15s; }
.btn-ai:hover { opacity: .9; color:#fff; }

.ai-result-card { background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; overflow: hidden; margin-bottom: 16px; transition: box-shadow .2s; }
.ai-result-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.ai-reason { background: linear-gradient(135deg,#F5F3FF,#EDE9FE); border-left: 3px solid #7C3AED; padding: 10px 14px; font-size: 13px; color: #4C1D95; margin: 0 16px 16px; border-radius: 0 8px 8px 0; }
.ai-reason i { margin-right: 6px; }

.loading-dots span { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #7C3AED; margin: 0 2px; animation: dot-bounce .8s infinite; }
.loading-dots span:nth-child(2) { animation-delay: .15s; }
.loading-dots span:nth-child(3) { animation-delay: .3s; }
@keyframes dot-bounce { 0%,80%,100%{transform:scale(0.6);opacity:.5} 40%{transform:scale(1);opacity:1} }
</style>
@endpush

@section('content')

<div class="ai-hero">
    <div class="container">
        <h1><i class="bi bi-stars me-2"></i>Gợi ý Tour bằng AI</h1>
        <p>Cho AI biết sở thích của bạn — nhận ngay gợi ý tour phù hợp nhất!</p>
    </div>
</div>

<div class="container">
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
                    <label class="pref-label"><i class="bi bi-cash-coin"></i> Ngân sách</label>
                    <select name="budget" class="form-select pref-input">
                        <option value="">Không giới hạn</option>
                        <option value="dưới 5 triệu">Dưới 5 triệu đồng</option>
                        <option value="5-15 triệu">5 – 15 triệu đồng</option>
                        <option value="15-30 triệu">15 – 30 triệu đồng</option>
                        <option value="trên 30 triệu">Trên 30 triệu đồng</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="pref-label"><i class="bi bi-clock"></i> Thời gian đi</label>
                    <select name="duration" class="form-select pref-input">
                        <option value="">Linh hoạt</option>
                        <option value="1-3">1 – 3 ngày</option>
                        <option value="4-6">4 – 6 ngày</option>
                        <option value="7-10">7 – 10 ngày</option>
                        <option value="trên 10">Trên 10 ngày</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="pref-label"><i class="bi bi-geo-alt"></i> Điểm đến ưa thích</label>
                    <input type="text" name="destination" class="form-control pref-input"
                           placeholder="VD: Nhật Bản, Đà Nẵng, Châu Âu...">
                </div>
                <div class="col-md-6">
                    <label class="pref-label"><i class="bi bi-tag"></i> Loại hình du lịch</label>
                    <select name="type" class="form-select pref-input">
                        <option value="">Tất cả</option>
                        <option value="nghỉ dưỡng">Nghỉ dưỡng, thư giãn</option>
                        <option value="khám phá">Khám phá, mạo hiểm</option>
                        <option value="văn hóa">Văn hóa, lịch sử</option>
                        <option value="mùa hoa">Mùa hoa, thiên nhiên</option>
                        <option value="gia đình">Du lịch gia đình</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-ai" id="submitBtn">
                        <i class="bi bi-stars me-2"></i> Gợi ý tour cho tôi
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Kết quả gợi ý --}}
    @if(isset($tours) && $tours->count())
    <div class="mb-2" style="font-size:14px;color:#6B7280;text-align:center">
        <i class="bi bi-check-circle text-success me-1"></i>
        AI đã tìm thấy <strong>{{ $tours->count() }}</strong> tour phù hợp với bạn!
    </div>
    <div class="row g-3 mb-5">
        @foreach($tours as $i => $tour)
        <div class="col-12">
            <div class="ai-result-card">
                <div class="d-flex gap-0">
                    <img src="{{ $tour->thumbnail ? asset('storage/'.$tour->thumbnail) : 'https://placehold.co/180x130/7C3AED/white?text=Tour' }}"
                         style="width:180px;height:130px;object-fit:cover;flex-shrink:0" alt="{{ $tour->title }}">
                    <div class="p-3 flex-grow-1">
                        <div class="d-flex align-items-start justify-content-between gap-2">
                            <div>
                                <span style="background:#F5F3FF;color:#7C3AED;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px">
                                    <i class="bi bi-stars"></i> Gợi ý #{{ $i+1 }}
                                </span>
                                <div style="font-size:15px;font-weight:700;margin-top:6px">{{ $tour->title }}</div>
                                <div style="font-size:12px;color:#6B7280;margin-top:3px">
                                    <i class="bi bi-clock me-1"></i>{{ $tour->duration_days }}N{{ $tour->duration_days-1 }}Đ
                                    <i class="bi bi-geo-alt ms-2 me-1"></i>{{ $tour->destination->name ?? '' }}
                                    <i class="bi bi-tag ms-2 me-1"></i>{{ $tour->category->name ?? '' }}
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <div style="font-size:18px;font-weight:800;color:#FF6B00">
                                    {{ number_format($tour->price_adult,0,',','.') }}đ
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
                @if($tour->ai_reason)
                <div class="ai-reason">
                    <i class="bi bi-lightbulb-fill"></i>
                    <strong>Tại sao phù hợp:</strong> {{ $tour->ai_reason }}
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @elseif(isset($tours) && $tours->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-search" style="font-size:48px"></i>
        <div class="mt-3 fw-600">AI không tìm thấy tour phù hợp</div>
        <div style="font-size:13px">Thử thay đổi tiêu chí tìm kiếm</div>
        <a href="{{ route('tours.index') }}" class="btn btn-primary rounded-pill mt-3 px-4">Xem tất cả tour</a>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.getElementById('recommendForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<span class="loading-dots"><span></span><span></span><span></span></span> AI đang phân tích...';
    btn.disabled = true;
});
</script>
@endpush