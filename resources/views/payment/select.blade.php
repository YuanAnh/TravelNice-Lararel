@extends('layouts.app')
@section('title', 'Chọn phương thức thanh toán')

@push('styles')
<style>
.payment-wrap { min-height: calc(100vh - 64px); background: #F5F6F8; display: flex; align-items: center; justify-content: center; padding: 40px 16px; }
.payment-card { background: #fff; border-radius: 16px; padding: 32px; width: 100%; max-width: 520px; box-shadow: 0 8px 32px rgba(0,0,0,.08); }
.payment-title { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
.payment-sub { font-size: 13px; color: #6B7280; margin-bottom: 24px; }

.booking-summary { background: #F9FAFB; border-radius: 10px; padding: 16px; margin-bottom: 24px; border: 1px solid #E5E7EB; }
.booking-summary .tour-name { font-size: 14px; font-weight: 700; margin-bottom: 8px; }
.booking-summary .info-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px; }
.booking-summary .info-row span:first-child { color: #6B7280; }
.booking-summary .total-row { display: flex; justify-content: space-between; font-size: 16px; font-weight: 700; margin-top: 10px; padding-top: 10px; border-top: 1px solid #E5E7EB; }
.booking-summary .total-row span:last-child { color: #FF6B00; }

.payment-method { border: 2px solid #E5E7EB; border-radius: 12px; padding: 16px 20px; margin-bottom: 12px; cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: 16px; text-decoration: none; }
.payment-method:hover { border-color: #0066CC; background: #F0F7FF; }
.payment-method .method-logo { width: 56px; height: 36px; object-fit: contain; }
.payment-method .method-info { flex: 1; }
.payment-method .method-name { font-size: 15px; font-weight: 700; color: #1A1A2E; }
.payment-method .method-desc { font-size: 12px; color: #6B7280; margin-top: 2px; }
.payment-method .method-arrow { color: #9CA3AF; font-size: 18px; }
.payment-method:hover .method-arrow { color: #0066CC; }

.vnpay-btn { border-color: #005BAA; }
.vnpay-btn:hover { border-color: #005BAA; background: #EEF5FF; }
.momo-btn { border-color: #AE2070; }
.momo-btn:hover { border-color: #AE2070; background: #FDF2F7; }
</style>
@endpush

@section('content')
<div class="payment-wrap">
    <div class="payment-card">
        <div class="text-center mb-4">
            <i class="bi bi-credit-card" style="font-size:36px;color:#0066CC"></i>
            <div class="payment-title mt-2">Chọn phương thức thanh toán</div>
            <div class="payment-sub">Mã đơn: <strong>#{{ $booking->booking_code }}</strong></div>
        </div>

        {{-- Booking summary --}}
        <div class="booking-summary">
            <div class="tour-name">{{ $booking->tourSlot->tour->title ?? 'Tour' }}</div>
            <div class="info-row">
                <span>Ngày khởi hành</span>
                <span>{{ $booking->tourSlot->departure_date?->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span>Số khách</span>
                <span>
                    {{ $booking->num_adults }} người lớn
                    @if($booking->num_children > 0)
                    , {{ $booking->num_children }} trẻ em
                    @endif
                </span>
            </div>
            @if($booking->discount_amount > 0)
            <div class="info-row">
                <span>Giảm giá</span>
                <span style="color:#16A34A">-{{ number_format($booking->discount_amount,0,',','.') }}đ</span>
            </div>
            @endif
            <div class="total-row">
                <span>Tổng thanh toán</span>
                <span>{{ number_format($booking->total_price - $booking->discount_amount,0,',','.') }}đ</span>
            </div>
        </div>

        {{-- VNPay --}}
        <a href="{{ route('payment.vnpay', $booking) }}" class="payment-method vnpay-btn">
            <div style="width:56px;height:36px;background:linear-gradient(135deg,#005BAA,#0099FF);border-radius:8px;display:flex;align-items:center;justify-content:center">
                <span style="color:#fff;font-size:11px;font-weight:800;letter-spacing:-0.5px">VNPay</span>
            </div>
            <div class="method-info">
                <div class="method-name">Thanh toán VNPay</div>
                <div class="method-desc">ATM, Visa, Mastercard, QR Code • An toàn & nhanh chóng</div>
            </div>
            <i class="bi bi-chevron-right method-arrow"></i>
        </a>

        {{-- MoMo --}}
        <a href="{{ route('payment.momo', $booking) }}" class="payment-method momo-btn">
            <div style="width:56px;height:36px;background:linear-gradient(135deg,#AE2070,#D63A8A);border-radius:8px;display:flex;align-items:center;justify-content:center">
                <span style="color:#fff;font-size:11px;font-weight:800">MoMo</span>
            </div>
            <div class="method-info">
                <div class="method-name">Ví MoMo</div>
                <div class="method-desc">Thanh toán qua app MoMo • Hoàn tiền nhanh</div>
            </div>
            <i class="bi bi-chevron-right method-arrow"></i>
        </a>

        <div class="text-center mt-3">
            <a href="{{ route('profile.index') }}" class="text-muted" style="font-size:13px;text-decoration:none">
                <i class="bi bi-arrow-left me-1"></i> Thanh toán sau
            </a>
        </div>

        <div class="mt-4 p-3 rounded-3" style="background:#F0FDF4;font-size:12px;color:#166534">
            <i class="bi bi-shield-lock me-2"></i>
            Giao dịch được mã hóa SSL 256-bit. TravelNice không lưu thông tin thẻ của bạn.
        </div>
    </div>
</div>
@endsection