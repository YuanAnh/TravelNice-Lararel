@extends('admin.layouts.admin')
@section('title', 'Quản lý Đánh giá')
@section('page-title', 'Quản lý Đánh giá')
@section('breadcrumb', 'Admin / Đánh giá')

@section('content')

<div class="d-flex gap-2 mb-4 flex-wrap align-items-center">
    <a href="{{ route('admin.reviews.index') }}"
       class="btn btn-sm rounded-pill {{ !request('approved') ? 'btn-primary' : 'btn-outline-secondary' }}">
        Tất cả <span class="badge ms-1 bg-secondary">{{ $pendingCount + $approvedCount }}</span>
    </a>
    <a href="{{ route('admin.reviews.index', ['approved' => 'no']) }}"
       class="btn btn-sm rounded-pill {{ request('approved') === 'no' ? 'btn-primary' : 'btn-outline-secondary' }}">
        Chờ duyệt
        @if($pendingCount > 0)
        <span class="badge ms-1" style="background:#DC2626">{{ $pendingCount }}</span>
        @endif
    </a>
    <a href="{{ route('admin.reviews.index', ['approved' => 'yes']) }}"
       class="btn btn-sm rounded-pill {{ request('approved') === 'yes' ? 'btn-primary' : 'btn-outline-secondary' }}">
        Đã duyệt <span class="badge ms-1 bg-success">{{ $approvedCount }}</span>
    </a>
</div>

@if($pendingCount > 0 && !request('approved'))
<div class="alert d-flex align-items-center gap-3 mb-4"
     style="background:#FEF3C7;border:1px solid #FCD34D;border-radius:10px;font-size:13px">
    <i class="bi bi-star-fill text-warning fs-5"></i>
    <div>
        <strong>{{ $pendingCount }} đánh giá</strong> đang chờ duyệt!
        <a href="{{ route('admin.reviews.index', ['approved' => 'no']) }}" class="fw-600 ms-2">Duyệt ngay →</a>
    </div>
</div>
@endif

<div class="admin-table-wrap">
    <div class="admin-table-header">
        <div class="admin-table-title"><i class="bi bi-star me-2 text-warning"></i>Danh sách đánh giá</div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Khách hàng</th>
                    <th>Tour</th>
                    <th>Đánh giá</th>
                    <th>Nhận xét</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th style="width:100px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr>
                    <td>
                        <div style="font-size:13px;font-weight:600">{{ $review->user->name ?? '—' }}</div>
                        <div style="font-size:11px;color:#9CA3AF">{{ $review->user->email ?? '' }}</div>
                    </td>
                    <td>
                        <div style="font-size:13px;font-weight:600;max-width:180px">
                            {{ Str::limit($review->tour->title ?? '—', 35) }}
                        </div>
                    </td>
                    <td>
                        <div style="color:#F59E0B;font-size:16px;letter-spacing:2px">
                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                        </div>
                        <div style="font-size:11px;color:#9CA3AF">{{ $review->rating }}/5</div>
                    </td>
                    <td style="max-width:200px">
                        <div style="font-size:13px;color:#374151">
                            {{ Str::limit($review->comment ?? 'Không có nhận xét', 80) }}
                        </div>
                    </td>
                    <td style="font-size:12px;color:#9CA3AF">
                        {{ $review->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        @if($review->is_approved)
                        <span style="background:#D1FAE5;color:#065F46;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:600">
                            <i class="bi bi-check-circle me-1"></i>Đã duyệt
                        </span>
                        @else
                        <span style="background:#FEF3C7;color:#92400E;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:600">
                            <i class="bi bi-clock me-1"></i>Chờ duyệt
                        </span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if(!$review->is_approved)
                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-action" style="background:#D1FAE5;color:#065F46" title="Duyệt">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                  onsubmit="return confirm('Xoá đánh giá này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-action-delete" title="Xoá">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-star" style="font-size:36px;display:block;margin-bottom:8px"></i>
                        Chưa có đánh giá nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reviews->hasPages())
    <div class="p-3 border-top">
        {{ $reviews->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection