<div class="modal fade" id="reviewModal{{ $booking->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none">
            <div class="modal-header" style="background:var(--tn-blue);border-radius:16px 16px 0 0">
                <h5 class="modal-title text-white fw-700" style="font-size:15px">
                    <i class="bi bi-star me-2"></i>Đánh giá tour
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="fw-600 mb-3" style="font-size:14px">
                    {{ Str::limit($booking->tourSlot->tour->title ?? 'Tour', 50) }}
                </div>

                <form method="POST" action="{{ route('reviews.store') }}">
                    @csrf
                    <input type="hidden" name="tour_id" value="{{ $booking->tourSlot->tour_id ?? '' }}">
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                    {{-- Star rating --}}
                    <div class="mb-3">
                        <label class="fw-600 mb-2 d-block" style="font-size:13px">Đánh giá của bạn <span class="text-danger">*</span></label>
                        <div class="star-rating d-flex gap-2" id="stars{{ $booking->id }}">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star star-icon" data-value="{{ $i }}"
                               style="font-size:28px;cursor:pointer;color:#D1D5DB;transition:color .1s"
                               onclick="setRating({{ $booking->id }}, {{ $i }})"
                               onmouseover="hoverRating({{ $booking->id }}, {{ $i }})"
                               onmouseout="resetHover({{ $booking->id }})"></i>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="rating{{ $booking->id }}" required>
                        <div id="ratingLabel{{ $booking->id }}" style="font-size:12px;color:#6B7280;margin-top:4px"></div>
                    </div>

                    {{-- Comment --}}
                    <div class="mb-3">
                        <label class="fw-600 mb-2 d-block" style="font-size:13px">Nhận xét</label>
                        <textarea name="comment" class="form-control" rows="3"
                                  placeholder="Chia sẻ trải nghiệm của bạn về tour này..."
                                  style="border-radius:8px;font-size:13px"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill rounded-pill">
                            <i class="bi bi-send me-1"></i> Gửi đánh giá
                        </button>
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Huỷ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const ratingLabels = ['', 'Tệ', 'Không tốt', 'Bình thường', 'Tốt', 'Tuyệt vời'];
let selectedRating{{ $booking->id }} = 0;

function setRating(bookingId, value) {
    selectedRating{{ $booking->id }} = value;
    document.getElementById('rating' + bookingId).value = value;
    document.getElementById('ratingLabel' + bookingId).textContent = ratingLabels[value];
    updateStars(bookingId, value, true);
}

function hoverRating(bookingId, value) {
    updateStars(bookingId, value, false);
}

function resetHover(bookingId) {
    updateStars(bookingId, selectedRating{{ $booking->id }}, true);
}

function updateStars(bookingId, value, isSelected) {
    const stars = document.querySelectorAll('#stars' + bookingId + ' .star-icon');
    stars.forEach((star, i) => {
        if (i < value) {
            star.className = 'bi bi-star-fill star-icon';
            star.style.color = '#F59E0B';
        } else {
            star.className = 'bi bi-star star-icon';
            star.style.color = '#D1D5DB';
        }
    });
}
</script>