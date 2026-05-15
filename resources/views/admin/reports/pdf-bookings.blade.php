<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo đặt tour — TravelNice</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #1A1A2E; padding: 20px; }
        .header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #0066CC; }
        .header h1 { font-size: 20px; color: #0066CC; }
        .header p { font-size: 12px; color: #6B7280; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #0066CC; color: #fff; padding: 8px 10px; font-size: 12px; text-align: left; }
        td { padding: 7px 10px; border-bottom: 1px solid #E5E7EB; font-size: 12px; }
        tr:nth-child(even) td { background: #F9FAFB; }
        .status { padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
        .s-pending   { background:#FEF3C7; color:#92400E; }
        .s-paid      { background:#DBEAFE; color:#1E40AF; }
        .s-confirmed { background:#D1FAE5; color:#065F46; }
        .s-cancelled { background:#FEE2E2; color:#991B1B; }
        .s-completed { background:#E0E7FF; color:#3730A3; }
        .footer { text-align: center; font-size: 11px; color: #9CA3AF; margin-top: 20px; border-top: 1px solid #E5E7EB; padding-top: 12px; }
        .total-row { background: #EEF5FF; font-weight: 700; }
        @media print {
            .no-print { display: none; }
            body { padding: 10px; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center;margin-bottom:16px">
    <button onclick="window.print()" style="background:#0066CC;color:#fff;border:none;padding:8px 24px;border-radius:8px;font-size:14px;cursor:pointer">
        🖨️ In / Lưu PDF
    </button>
    <button onclick="window.close()" style="background:#6B7280;color:#fff;border:none;padding:8px 24px;border-radius:8px;font-size:14px;cursor:pointer;margin-left:8px">
        ✕ Đóng
    </button>
</div>

<div class="header">
    <h1>TravelNice — Báo cáo Đặt Tour</h1>
    <p>Xuất ngày: {{ now()->format('d/m/Y H:i') }} | Tổng: {{ $bookings->count() }} đơn</p>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Mã đơn</th>
            <th>Khách hàng</th>
            <th>Tour</th>
            <th>Ngày KH</th>
            <th>Số NL</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Ngày đặt</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bookings as $i => $b)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>#{{ $b->booking_code }}</strong></td>
            <td>{{ $b->user->name ?? '—' }}</td>
            <td>{{ Str::limit($b->tourSlot->tour->title ?? '—', 30) }}</td>
            <td>{{ $b->tourSlot->departure_date?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $b->num_adults }}</td>
            <td>{{ number_format($b->total_price, 0, ',', '.') }}đ</td>
            <td><span class="status s-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
            <td>{{ $b->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="6" style="text-align:right">TỔNG:</td>
            <td>{{ number_format($bookings->sum('total_price'), 0, ',', '.') }}đ</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>

<div class="footer">
    © {{ date('Y') }} TravelNice — Tài liệu nội bộ, không phân phối ra ngoài
</div>

</body>
</html>