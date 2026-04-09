@extends('admin.layouts.admin')

@section('title', 'Quản lý Booking')
@section('page-title', 'Quản lý Booking')
@section('breadcrumb', 'Admin / Booking')

@section('content')

{{-- Stats row --}}
<div class="row g-3 mb-4">
    @foreach([
        ['pending',   'Chờ xác nhận', $stats['pending'],   '#F59E0B', '#FEF3C7'],
        ['confirmed', 'Đã xác nhận',  $stats['confirmed'],  '#16A34A', '#D1FAE5'],
        ['paid',      'Đã thanh toán',$stats['paid'],       '#2563EB', '#DBEAFE'],
        ['cancelled', 'Đã huỷ',       $stats['cancelled'],  '#DC2626', '#FEE2E2'],
    ] as $s)
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="cursor:pointer" onclick="window.location='{{ route('admin.bookings.index', ['status'=>$s[0]]) }}'">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-num" style="color:{{ $s[3] }}">{{ $s[2] }}</div>
                    <div class="stat-label">{{ $s[1] }}</div>
                </div>
                <div class="stat-icon" style="background:{{ $s[4] }};color:{{ $s[3] }}">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="admin-table-wrap">
    <div class="admin-table-header flex-wrap gap-2">
        <div class="admin-table-title"><i class="bi bi-calendar-check me-2 text-primary"></i>Danh sách Booking</div>

        {{-- Search --}}
        <form method="GET" class="search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" name="q" class="admin-search" placeholder="Mã đơn, tên khách..." value="{{ request('q') }}">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        </form>

        {{-- Status filter --}}
        <div class="d-flex gap-1 flex-wrap">
            @foreach([
                [null,        'Tất cả'],
                ['pending',   'Chờ XN'],
                ['confirmed', 'Đã XN'],
                ['paid',      'Đã TT'],
                ['completed', 'HT'],
                ['cancelled', 'Huỷ'],
            ] as $f)
            <a href="{{ route('admin.bookings.index', array_filter(['status'=>$f[0], 'q'=>request('q')])) }}"
               class="btn btn-sm rounded-pill {{ request('status') === $f[0] ? 'btn-primary' : 'btn-outline-secondary' }}"
               style="font-size:12px">{{ $f[1] }}</a>
            @endforeach
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Tour</th>
                    <th>Khách hàng</th>
                    <th>Ngày KH</th>
                    <th>Số người</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td class="fw-600" style="color:var(--admin-blue)">#{{ $b->booking_code }}</td>
                    <td>
                        <div style="max-width:180px;font-size:12px;font-weight:600">
                            {{ Str::limit($b->tourSlot->tour->title ?? '—', 35) }}
                        </div>
                    </td>
                    <td>
                        <div style="font-size:13px;font-weight:600">{{ $b->user->name ?? '—' }}</div>
                        <div style="font-size:11px;color:#9CA3AF">{{ $b->user->phone ?? '' }}</div>
                    </td>
                    <td style="font-size:12px">
                        {{ $b->tourSlot->departure_date?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td style="font-size:12px">
                        {{ $b->num_adults }} NL
                        @if($b->num_children > 0), {{ $b->num_children }} TE @endif
                    </td>
                    <td class="fw-600" style="color:#FF6B00;font-size:13px">
                        {{ number_format($b->total_price, 0, ',', '.') }}đ
                        @if($b->discount_amount > 0)
                        <div style="font-size:11px;color:#16A34A">-{{ number_format($b->discount_amount,0,',','.') }}đ</div>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge-admin status-{{ $b->status }}">
                            @switch($b->status)
                                @case('pending')   Chờ XN @break
                                @case('paid')      Đã TT @break
                                @case('confirmed') Đã XN @break
                                @case('cancelled') Huỷ @break
                                @case('completed') HT @break
                            @endswitch
                        </span>
                    </td>
                    <td style="font-size:11px;color:#9CA3AF">
                        {{ $b->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.bookings.show', $b) }}" class="btn-action btn-action-view" title="Chi tiết">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.bookings.edit', $b) }}" class="btn-action btn-action-edit" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x" style="font-size:36px;display:block;margin-bottom:8px"></i>
                        Không có booking nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
    <div class="p-3 border-top">
        {{ $bookings->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@push('styles')
<style>
.status-badge-admin { font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; }
.status-pending   { background:#FEF3C7; color:#92400E; }
.status-paid      { background:#DBEAFE; color:#1E40AF; }
.status-confirmed { background:#D1FAE5; color:#065F46; }
.status-cancelled { background:#FEE2E2; color:#991B1B; }
.status-completed { background:#E0E7FF; color:#3730A3; }
</style>
@endpush

@endsection