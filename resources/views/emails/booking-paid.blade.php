<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn đặt tour mới đã thanh toán</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #F5F6F8; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #0066CC, #0099FF); padding: 28px 32px; text-align: center; }
        .header .logo { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .header .logo span { color: #FF6B00; }
        .header p { color: rgba(255,255,255,.85); font-size: 14px; margin: 8px 0 0; }
        .body { padding: 28px 32px; }
        .alert-box { background: #D1FAE5; border: 1px solid #6EE7B7; border-radius: 10px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; }
        .alert-box .icon { font-size: 28px; }
        .alert-box .text { font-size: 14px; color: #065F46; }
        .alert-box .text strong { font-size: 16px; display: block; margin-bottom: 2px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .info-table tr { border-bottom: 1px solid #F3F4F6; }
        .info-table tr:last-child { border-bottom: none; }
        .info-table td { padding: 10px 0; font-size: 13px; }
        .info-table .label { color: #6B7280; width: 40%; }
        .info-table .value { font-weight: 600; color: #1A1A2E; }
        .price { font-size: 22px; font-weight: 800; color: #FF6B00; }
        .btn { display: inline-block; background: #0066CC; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 14px; font-weight: 600; margin-top: 8px; }
        .footer { background: #F9FAFB; padding: 16px 32px; text-align: center; font-size: 12px; color: #9CA3AF; border-top: 1px solid #E5E7EB; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Travel<span>Nice</span></div>
            <p>Hệ thống quản lý đặt tour</p>
        </div>

        <div class="body">
            <div class="alert-box">
                <div class="icon">💳</div>
                <div class="text">
                    <strong>Có đơn đặt tour mới đã thanh toán!</strong>
                    Vui lòng xác nhận đơn để hoàn tất quy trình.
                </div>
            </div>

            <table class="info-table">
                <tr>
                    <td class="label">Mã đơn</td>
                    <td class="value" style="color:#0066CC">#{{ $booking->booking_code }}</td>
                </tr>
                <tr>
                    <td class="label">Khách hàng</td>
                    <td class="value">{{ $booking->user->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">SĐT</td>
                    <td class="value">{{ $booking->user->phone ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Tour</td>
                    <td class="value">{{ $booking->tourSlot->tour->title ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Ngày khởi hành</td>
                    <td class="value">{{ $booking->tourSlot->departure_date?->format('d/m/Y') ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Số khách</td>
                    <td class="value">{{ $booking->num_adults }} người lớn@if($booking->num_children > 0), {{ $booking->num_children }} trẻ em@endif</td>
                </tr>
                <tr>
                    <td class="label">Phương thức TT</td>
                    <td class="value">{{ strtoupper($booking->payment->gateway ?? '—') }}</td>
                </tr>
                <tr>
                    <td class="label">Tổng tiền</td>
                    <td class="value"><span class="price">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span></td>
                </tr>
                <tr>
                    <td class="label">Thời gian TT</td>
                    <td class="value">{{ $booking->payment?->paid_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</td>
                </tr>
            </table>

            <div style="text-align:center">
                <a href="{{ config('app.url') }}/admin/bookings/{{ $booking->id }}" class="btn">
                    👉 Xem & Xác nhận đơn ngay
                </a>
            </div>
        </div>

        <div class="footer">
            © {{ date('Y') }} TravelNice. Email này được gửi tự động từ hệ thống.
        </div>
    </div>
</body>
</html>