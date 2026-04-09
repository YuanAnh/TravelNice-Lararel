@extends('admin.layouts.admin')

@section('title', 'Quản lý Tour')
@section('page-title', 'Quản lý Tour')
@section('breadcrumb', 'Admin / Tour')

@section('content')

{{-- Header actions --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div style="font-size:13px;color:#6B7280">Tổng cộng <strong>{{ $tours->total() }}</strong> tour</div>
    </div>
    <a href="{{ route('admin.tours.create') }}" class="btn btn-primary btn-sm px-3 rounded-pill">
        <i class="bi bi-plus-lg me-1"></i> Thêm Tour mới
    </a>
</div>

{{-- Table --}}
<div class="admin-table-wrap">
    <div class="admin-table-header">
        <div class="admin-table-title"><i class="bi bi-map me-2 text-primary"></i>Danh sách Tour</div>

        {{-- Search --}}
        <form method="GET" class="search-wrap ms-auto">
            <i class="bi bi-search"></i>
            <input type="text" name="q" class="admin-search" placeholder="Tìm theo tên tour..." value="{{ request('q') }}">
        </form>

        {{-- Status filter --}}
        <select class="form-select form-select-sm rounded-pill" style="width:130px;font-size:13px" onchange="window.location=this.value">
            <option value="{{ route('admin.tours.index') }}" {{ !request('status') ? 'selected':'' }}>Tất cả</option>
            <option value="{{ route('admin.tours.index', ['status'=>'active']) }}" {{ request('status')==='active' ? 'selected':'' }}>Đang hoạt động</option>
            <option value="{{ route('admin.tours.index', ['status'=>'inactive']) }}" {{ request('status')==='inactive' ? 'selected':'' }}>Tạm dừng</option>
            <option value="{{ route('admin.tours.index', ['status'=>'draft']) }}" {{ request('status')==='draft' ? 'selected':'' }}>Bản nháp</option>
        </select>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Tour</th>
                    <th>Điểm đến</th>
                    <th>Danh mục</th>
                    <th>Thời gian</th>
                    <th>Giá người lớn</th>
                    <th>Trạng thái</th>
                    <th>Đánh giá</th>
                    <th style="width:100px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tours as $tour)
                <tr>
                    <td style="color:#9CA3AF">{{ $tour->id }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $tour->thumbnail ?? 'https://placehold.co/56x42/0066CC/white?text=Tour' }}"
                                 class="tour-thumb" alt="">
                            <div class="tour-name">
                                {{ Str::limit($tour->title, 40) }}
                                <small>{{ $tour->slug }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $tour->destination->name ?? '—' }}</td>
                    <td>{{ $tour->category->name ?? '—' }}</td>
                    <td>{{ $tour->duration_days }}N{{ $tour->duration_days - 1 }}Đ</td>
                    <td class="fw-600" style="color:#FF6B00">{{ number_format($tour->price_adult, 0, ',', '.') }}đ</td>
                    <td>
                        @if($tour->status === 'active')
                            <span class="badge-active">Hoạt động</span>
                        @elseif($tour->status === 'inactive')
                            <span class="badge-inactive">Tạm dừng</span>
                        @else
                            <span class="badge-draft">Nháp</span>
                        @endif
                    </td>
                    <td>
                        @if($tour->avg_rating > 0)
                        <span style="color:#F59E0B">★</span> {{ $tour->avg_rating }}
                        @else
                        <span style="color:#D1D5DB">Chưa có</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.tours.edit', $tour) }}" class="btn-action btn-action-edit" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('tours.show', $tour->slug) }}" target="_blank" class="btn-action btn-action-view" title="Xem">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.tours.destroy', $tour) }}"
                                  onsubmit="return confirm('Xoá tour {{ addslashes($tour->title) }}?')">
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
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-map" style="font-size:36px;display:block;margin-bottom:8px"></i>
                        Chưa có tour nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tours->hasPages())
    <div class="p-3 border-top">
        {{ $tours->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection