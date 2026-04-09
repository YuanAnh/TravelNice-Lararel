<div class="booking-item">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div class="flex-grow-1">
            <div class="booking-code">#{{ $booking->booking_code }}</div>
            <div class="booking-tour-name">
                {{ $booking->tourSlot->tour->title ?? 'Tour không xác định' }}
            </div>
            <div class="booking-meta mt-1">
                <span><i class="bi bi-calendar3"></i>
                    {{ $booking->tourSlot->departure_date?->format('d/m/Y') ?? '—' }}
                </span>
                <span><i class="bi bi-people"></i>
                    {{ $booking->num_adults }} người lớn
                    @if($booking->num_children > 0), {{ $booking->num_children }} trẻ em @endif
                </span>
                <span><i class="bi bi-clock"></i>
                    {{ $booking->created_at->format('d/m/Y H:i') }}
                </span>
            </div>
            @if($booking->note)
            <div style="font-size:12px;color:var(--tn-muted);margin-top:4px">
                <i class="bi bi-chat-left-text me-1"></i>{{ Str::limit($booking->note, 80) }}
            </div>
            @endif
        </div>
        <div class="text-end">
            <span class="status-badge status-{{ $booking->status }}">
                @switch($booking->status)
                    @case('pending')    Chờ xác nhận @break
                    @case('paid')       Đã thanh toán @break
                    @case('confirmed')  Đã xác nhận @break
                    @case('cancelled')  Đã huỷ @break
                    @case('completed')  Hoàn thành @break
                @endswitch
            </span>
            <div class="booking-price mt-2">
                {{ number_format($booking->total_price, 0, ',', '.') }}đ
            </div>
            @if($booking->discount_amount > 0)
            <div style="font-size:11px;color:#16A34A">
                Giảm: -{{ number_format($booking->discount_amount, 0, ',', '.') }}đ
            </div>
            @endif
            <div class="d-flex gap-1 justify-content-end mt-2">
                @if($booking->isPending())
                <form method="POST" action="{{ route('bookings.cancel', $booking->id) }}"
                      onsubmit="return confirm('Bạn có chắc muốn huỷ đơn này?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" style="font-size:12px">
                        <i class="bi bi-x-circle"></i> Huỷ
                    </button>
                </form>
                @endif
                @if($booking->isCompleted() && !$booking->review)
                <a href="#" class="btn btn-sm btn-outline-warning rounded-pill" style="font-size:12px">
                    <i class="bi bi-star"></i> Đánh giá
                </a>
                @endif
            </div>
        </div>
    </div>
</div>