@extends('layouts.app')
@section('title', $success ? 'Thanh toán thành công' : 'Thanh toán thất bại')

@push('styles')
<style>
.result-wrap { min-height: calc(100vh - 64px); background: #F5F6F8; display: flex; align-items: center; justify-content: center; padding: 40px 16px; }
.result-card { background: #fff; border-radius: 16px; padding: 40px; width: 100%; max-width: 480px; box-shadow: 0 8px 32px rgba(0,0,0,.08); text-align: center; }
.result-icon { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; margin: 0 auto 20px; }
.result-title { font-size: 22px; font-weight: 700; margin-bottom: 8px; }
.result-msg { font-size: 14px; color: #6B7280; margin-bottom: 24px; }
</style>
@endpush

@section('content')
<div class="result-wrap">
    <div class="result-card">
        @if($success)
        <div class="result-icon" style="background:#D1FAE5;color:#16A34A">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="result-title" style="color:#16A34A">Thanh toán thành công!</div>
        <div class="result-msg">{{ $message }}</div>

        @if(isset($booking))
        <div style="background:#F9FAFB;border-radius:10px;padding:16px;margin-bottom:24px;text-align:left">
            <div style="font-size:13px;font-weight:700;margin-bottom:8px">Chi tiết đơn hàng</div>
            <div style="font-size:13px;color:#6B7280">Mã đơn: <strong style="color:#1A1A2E">#{{ $booking->booking_code }}</strong></div>
            <div style="font-size:13px;color:#6B7280;margin-top:4px">Tour: <strong style="color:#1A1A2E">{{ Str::limit($booking->tourSlot->tour->title ?? '', 40) }}</strong></div>
            <div style="font-size:13px;color:#6B7280;margin-top:4px">Tổng tiền: <strong style="color:#FF6B00">{{ number_format($booking->total_price,0,',','.') }}đ</strong></div>
        </div>
        @endif

        <div class="d-flex gap-3 justify-content-center">
            <a href="{{ route('profile.index') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-calendar-check me-1"></i> Xem đơn của tôi
            </a>
            <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                Đặt thêm tour
            </a>
        </div>

        @else
        <div class="result-icon" style="background:#FEE2E2;color:#DC2626">
            <i class="bi bi-x-circle-fill"></i>
        </div>
        <div class="result-title" style="color:#DC2626">Thanh toán thất bại</div>
        <div class="result-msg">{{ $message }}</div>

        <div class="d-flex gap-3 justify-content-center">
            @if(isset($booking))
            <a href="{{ route('payment.select', $booking) }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Thử lại
            </a>
            @endif
            <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                Về trang của tôi
            </a>
        </div>
        @endif
    </div>
</div>
@endsection