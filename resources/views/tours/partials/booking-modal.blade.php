<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none">
            <div class="modal-header" style="background:var(--tn-blue);border-radius:16px 16px 0 0;padding:16px 24px">
                <div>
                    <div class="text-white fw-600" style="font-size:11px;opacity:.8;text-transform:uppercase;letter-spacing:.5px">YÊU CẦU ĐẶT TOUR</div>
                    <h5 class="modal-title text-white fw-700 mb-0" style="font-size:15px">{{ Str::limit($tour->title, 60) }}</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="tour_id" value="{{ $tour->id }}">
                    <input type="hidden" name="slot_id" id="selectedSlot" value="{{ $tour->availableSlots()->first()?->id }}">

                    <div class="row g-3">

                        {{-- Tên người đặt --}}
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="contact_name" class="form-control"
                                value="{{ auth()->user()->name ?? '' }}"
                                placeholder="Nhập họ tên đầy đủ" required>
                        </div>

                        {{-- SĐT --}}
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="contact_phone" class="form-control"
                                value="{{ auth()->user()->phone ?? '' }}"
                                placeholder="0912 345 678" required>
                        </div>

                        {{-- Số người lớn --}}
                        <div class="col-4">
                            <label class="form-label fw-600" style="font-size:13px">Người lớn <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty('adults', -1)">−</button>
                                <input type="number" name="num_adults" id="adults" class="form-control text-center" value="1" min="1" max="20" readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty('adults', 1)">+</button>
                            </div>
                            <div style="font-size:11px;color:var(--tn-muted);margin-top:3px">{{ number_format($tour->price_adult, 0, ',', '.') }}đ/người</div>
                        </div>

                        {{-- Trẻ em --}}
                        <div class="col-4">
                            <label class="form-label fw-600" style="font-size:13px">Trẻ em (5–11)</label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty('children', -1)">−</button>
                                <input type="number" name="num_children" id="children" class="form-control text-center" value="0" min="0" max="20" readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty('children', 1)">+</button>
                            </div>
                            <div style="font-size:11px;color:var(--tn-muted);margin-top:3px">{{ number_format($tour->price_child, 0, ',', '.') }}đ/người</div>
                        </div>

                        {{-- Em bé --}}
                        <div class="col-4">
                            <label class="form-label fw-600" style="font-size:13px">Em bé (&lt;5)</label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty('infants', -1)">−</button>
                                <input type="number" name="infants" id="infants" class="form-control text-center" value="0" min="0" max="10" readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty('infants', 1)">+</button>
                            </div>
                            <div style="font-size:11px;color:var(--tn-muted);margin-top:3px">Miễn phí</div>
                        </div>

                        {{-- Địa điểm đón --}}
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px">Địa điểm đón <span class="text-danger">*</span></label>
                            <input type="text" name="pickup_location" class="form-control" placeholder="Số nhà, đường, quận..." required>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label fw-600" style="font-size:13px">Email</label>
                            <input type="email" name="contact_email" class="form-control"
                                value="{{ auth()->user()->email ?? '' }}"
                                placeholder="email@example.com">
                        </div>

                        {{-- Yêu cầu đặc biệt --}}
                        <div class="col-12">
                            <label class="form-label fw-600" style="font-size:13px">Yêu cầu đặc biệt</label>
                            <textarea name="special_requests" class="form-control" rows="2"
                                placeholder="Ăn chay, phòng riêng, hỗ trợ xe lăn..."></textarea>
                        </div>

                        {{-- Tổng tiền --}}
                        <div class="col-12">
                            <div style="background:#F5F6F8;border-radius:10px;padding:14px">
                                <div class="d-flex justify-content-between mb-1" style="font-size:13px">
                                    <span id="adultLabel">Người lớn × 1</span>
                                    <span id="adultTotal">{{ number_format($tour->price_adult, 0, ',', '.') }}đ</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1" style="font-size:13px;color:var(--tn-muted)" id="childRow">
                                    <span id="childLabel">Trẻ em × 0</span>
                                    <span id="childTotal">0đ</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fw-700" style="font-size:16px">
                                    <span>Tổng cộng</span>
                                    <span id="grandTotal" style="color:var(--tn-orange)">{{ number_format($tour->price_adult, 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-light flex-fill py-2" data-bs-dismiss="modal">Huỷ</button>
                        <button type="submit" class="btn flex-fill py-2 fw-600" style="background:var(--tn-orange);color:#fff;font-size:15px">
                            <i class="bi bi-send me-1"></i> Gửi yêu cầu đặt tour
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
var priceAdult = {{ (int) $tour->price_adult }};
var priceChild = {{ (int) $tour->price_child }};

function changeQty(field, delta) {
    const input = document.getElementById(field);
    const min = parseInt(input.min);
    const max = parseInt(input.max);
    const newVal = Math.min(max, Math.max(min, parseInt(input.value) + delta));
    input.value = newVal;
    updateTotal();
}

function updateTotal() {
    const adults   = parseInt(document.getElementById("num_adults").value);
    const children = parseInt(document.getElementById("num_children").value);
    const adultAmt   = adults * priceAdult;
    const childAmt   = children * priceChild;
    const grand      = adultAmt + childAmt;

    const fmt = n => n.toLocaleString('vi-VN') + 'đ';

    document.getElementById('adultLabel').textContent  = `Người lớn × ${adults}`;
    document.getElementById('adultTotal').textContent  = fmt(adultAmt);
    document.getElementById('childLabel').textContent  = `Trẻ em × ${children}`;
    document.getElementById('childTotal').textContent  = fmt(childAmt);
    document.getElementById('grandTotal').textContent  = fmt(grand);
}
</script>