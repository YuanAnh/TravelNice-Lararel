@extends('admin.layouts.admin')

@section('title', 'Chi tiết Booking #' . $booking->booking_code)
@section('page-title', 'Chi tiết Booking')
@section('breadcrumb', 'Admin / Booking / #' . $booking->booking_code)

@push('styles')
<style>
.info-row { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #F3F4F6; font-size:13px; }
.info-row:last-child { border-bottom:none; }
.info-label { color:#6B7280; font-weight:500; }
.info-value { font-weight:600; color:#1A1A2E; text-align:right; }
.status-badge-admin { font-size:12px; font-weight:700; padding:4px 12px; border-radius:20px; }
.status-pending   { background:#FEF3C7; color:#92400E; }
.status-paid      { background:#DBEAFE; color:#1E40AF; }
.status-confirmed { background:#D1FAE5; color:#065F46; }
.status-cancelled { background:#FEE2E2; color:#991B1B; }
.status-completed { background:#E0E7FF; color:#3730A3; }
</style>
@endpush

@section('content')

<div class="d-flex gap-2 mb-4">
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Quay lại
    </a>
    <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-sm btn-primary rounded-pill">
        <i class="bi bi-pencil me-1"></i> Cập nhật trạng thái
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">

        {{-- Thông tin booking --}}
        <div class="admin-form-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Thông tin đơn đặt</h6>
                <span class="status-badge-admin status-{{ $booking->status }}">
                    @switch($booking->status)
                        @case('pending')   Chờ xác nhận @break
                        @case('paid')      Đã thanh toán @break
                        @case('confirmed') Đã xác nhận @break
                        @case('cancelled') Đã huỷ @break
                        @case('completed') Hoàn thành @break
                    @endswitch
                </span>
            </div>
            <div class="info-row"><span class="info-label">Mã đơn</span><span class="info-value" style="color:var(--admin-blue)">#{{ $booking->booking_code }}</span></div>
            <div class="info-row"><span class="info-label">Ngày đặt</span><span class="info-value">{{ $booking->created_at->format('d/m/Y H:i') }}</span></div>
            <div class="info-row"><span class="info-label">Số người lớn</span><span class="info-value">{{ $booking->num_adults }} người</span></div>
            <div class="info-row"><span class="info-label">Số trẻ em</span><span class="info-value">{{ $booking->num_children }} người</span></div>
            <div class="info-row"><span class="info-label">Tổng tiền</span><span class="info-value" style="color:#FF6B00;font-size:16px">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span></div>
            @if($booking->discount_amount > 0)
            <div class="info-row"><span class="info-label">Giảm giá</span><span class="info-value" style="color:#16A34A">-{{ number_format($booking->discount_amount, 0, ',', '.') }}đ</span></div>
            <div class="info-row"><span class="info-label">Thực thu</span><span class="info-value" style="color:#FF6B00;font-size:18px;font-weight:800">{{ number_format($booking->netTotal(), 0, ',', '.') }}đ</span></div>
            @endif
            @if($booking->note)
            <div class="info-row"><span class="info-label">Ghi chú</span><span class="info-value">{{ $booking->note }}</span></div>
            @endif
            @if($booking->cancelled_at)
            <div class="info-row"><span class="info-label">Thời gian huỷ</span><span class="info-value text-danger">{{ $booking->cancelled_at->format('d/m/Y H:i') }}</span></div>
            @endif
        </div>

        {{-- Thông tin tour --}}
        <div class="admin-form-card">
            <h6><i class="bi bi-map me-2"></i>Thông tin Tour</h6>
            @php $tour = $booking->tourSlot->tour ?? null; @endphp
            @if($tour)
            <div class="d-flex gap-3 mb-3">
                <img src="{{ $tour->thumbnail ?? 'https://placehold.co/80x60/0066CC/white?text=Tour' }}"
                     style="width:80px;height:60px;object-fit:cover;border-radius:8px">
                <div>
                    <div class="fw-700" style="font-size:15px">{{ $tour->title }}</div>
                    <div style="font-size:12px;color:#6B7280">{{ $tour->destination->name ?? '' }} · {{ $tour->duration_days }}N{{ $tour->duration_days-1 }}Đ</div>
                </div>
            </div>
            <div class="info-row"><span class="info-label">Ngày khởi hành</span><span class="info-value">{{ $booking->tourSlot->departure_date?->format('d/m/Y') ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Tổng slot</span><span class="info-value">{{ $booking->tourSlot->total_slots }} chỗ</span></div>
            <div class="info-row"><span class="info-label">Đã đặt</span><span class="info-value">{{ $booking->tourSlot->booked_slots }} chỗ</span></div>
            @endif
        </div>

    </div>

    <div class="col-lg-4">
        {{-- Thông tin khách hàng --}}
        <div class="admin-form-card">
            <h6><i class="bi bi-person me-2"></i>Khách hàng</h6>
            @php $user = $booking->user; @endphp
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:44px;height:44px;border-radius:50%;background:var(--admin-blue);color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div class="fw-700">{{ $user->name ?? '—' }}</div>
                    <div style="font-size:12px;color:#6B7280">{{ $user->email ?? '' }}</div>
                </div>
            </div>
            <div class="info-row"><span class="info-label">SĐT</span><span class="info-value">{{ $user->phone ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Trạng thái TK</span><span class="info-value">{{ $user->status ?? '—' }}</span></div>
        </div>

        {{-- Cập nhật nhanh --}}
        <div class="admin-form-card">
            <h6><i class="bi bi-arrow-repeat me-2"></i>Cập nhật nhanh</h6>
            <form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        @foreach(['pending'=>'Chờ xác nhận','paid'=>'Đã thanh toán','confirmed'=>'Đã xác nhận','completed'=>'Hoàn thành','cancelled'=>'Đã huỷ'] as $val => $label)
                        <option value="{{ $val }}" {{ $booking->status === $val ? 'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giảm giá (đ)</label>
                    <input type="number" name="discount_amount" class="form-control"
                           value="{{ $booking->discount_amount }}" min="0" step="10000">
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    <i class="bi bi-check2 me-1"></i> Lưu thay đổi
                </button>
            </form>
        </div>
    </div>
</div>

@endsection