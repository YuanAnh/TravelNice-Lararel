<div class="booking-item">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div class="flex-grow-1">
            <div class="booking-code">#{{ $booking->booking_code }}</div>
            <div class="booking-tour-name">
                {{ $booking->tourSlot->tour->title ?? 'Tour không xác định' }}
            </div>
            <div class="booking-meta mt-1">
                <span><i class="bi bi-calendar3"></i> {{ $booking->tourSlot->departure_date?->format('d/m/Y') ?? '—' }}</span>
                <span><i class="bi bi-people"></i> {{ $booking->num_adults }} người lớn
                    @if($booking->num_children > 0)
                    , {{ $booking->num_children }} trẻ em
                    @endif
                </span>
                <span><i class="bi bi-clock"></i> {{ $booking->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        <div class="text-end">
            <span class="status-badge status-{{ $booking->status }}">
                @switch($booking->status)
                    @case('pending')   Chờ xác nhận @break
                    @case('paid')      Đã thanh toán @break
                    @case('confirmed') Đã xác nhận @break
                    @case('cancelled') Đã huỷ @break
                    @case('completed') Hoàn thành @break
                @endswitch
            </span>
            <div class="booking-price mt-2">{{ number_format($booking->netTotal(),0,',','.') }}đ</div>
            <div class="d-flex gap-1 justify-content-end mt-2">
                @if($booking->isPending())
                <a href="{{ route('payment.select', $booking) }}" class="btn btn-sm btn-warning rounded-pill" style="font-size:12px">
                    <i class="bi bi-credit-card"></i> Thanh toán
                </a>
                @endif
                @if($booking->isCompleted() && !$booking->review)
                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill" style="font-size:12px"
                        data-bs-toggle="modal" data-bs-target="#reviewModal{{ $booking->id }}">
                    <i class="bi bi-star"></i> Đánh giá
                </button>
                @elseif($booking->isCompleted() && $booking->review)
                <span style="font-size:12px;color:#F59E0B"><i class="bi bi-star-fill"></i> Đã đánh giá</span>
                @endif
                @if($booking->isPending())
                <form method="POST" action="{{ route('bookings.cancel', $booking->id) }}" onsubmit="return confirm('Huỷ đơn này?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" style="font-size:12px">
                        <i class="bi bi-x-circle"></i> Huỷ
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@if($booking->isCompleted() && !$booking->review)
@include('profile.partials.review-modal', ['booking' => $booking])
@endif