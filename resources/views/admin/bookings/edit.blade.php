@extends('admin.layouts.admin')

@section('title', 'Sửa Booking #' . $booking->booking_code)
@section('page-title', 'Cập nhật Booking')
@section('breadcrumb', 'Admin / Booking / Cập nhật')

@section('content')

<div class="d-flex gap-2 mb-4">
    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-6">
        <div class="admin-form-card">
            <h6><i class="bi bi-pencil me-2"></i>Cập nhật trạng thái — #{{ $booking->booking_code }}</h6>
            <form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                    <select name="status" class="form-select">
                        @foreach(['pending'=>'Chờ xác nhận','paid'=>'Đã thanh toán','confirmed'=>'Đã xác nhận','completed'=>'Hoàn thành','cancelled'=>'Đã huỷ'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $booking->status) === $val ? 'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giảm giá (đ)</label>
                    <input type="number" name="discount_amount" class="form-control"
                           value="{{ old('discount_amount', $booking->discount_amount) }}" min="0" step="10000">
                </div>
                <div class="mb-3">
                    <label class="form-label">Ghi chú nội bộ</label>
                    <textarea name="note" class="form-control" rows="3">{{ old('note', $booking->note) }}</textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill rounded-pill">
                        <i class="bi bi-check2 me-1"></i> Lưu thay đổi
                    </button>
                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-outline-secondary rounded-pill">Huỷ</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection