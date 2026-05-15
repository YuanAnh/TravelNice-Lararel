@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Admin / Dashboard')

@section('content')

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="stat-icon" style="background:#EEF5FF;color:#0066CC"><i class="bi bi-map"></i></div>
                <span style="font-size:11px;background:#D1FAE5;color:#065F46;padding:2px 8px;border-radius:10px;font-weight:600">Tour</span>
            </div>
            <div class="stat-num" style="color:#0066CC">{{ $totalTours }}</div>
            <div class="stat-label">Tổng số Tour</div>
            <div class="stat-change" style="font-size:12px;color:#16A34A;margin-top:6px"><i class="bi bi-arrow-up-short"></i>{{ $activeTours }} đang hoạt động</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="stat-icon" style="background:#FEF3C7;color:#D97706"><i class="bi bi-calendar-check"></i></div>
                <span style="font-size:11px;background:#FEF3C7;color:#92400E;padding:2px 8px;border-radius:10px;font-weight:600">Booking</span>
            </div>
            <div class="stat-num" style="color:#D97706">{{ $totalBookings }}</div>
            <div class="stat-label">Tổng đơn đặt</div>
            <div style="font-size:12px;color:#D97706;margin-top:6px"><i class="bi bi-clock"></i> {{ $pendingBookings }} chờ xác nhận</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="stat-icon" style="background:#F0FDF4;color:#16A34A"><i class="bi bi-people"></i></div>
                <span style="font-size:11px;background:#F0FDF4;color:#166534;padding:2px 8px;border-radius:10px;font-weight:600">User</span>
            </div>
            <div class="stat-num" style="color:#16A34A">{{ $totalUsers }}</div>
            <div class="stat-label">Người dùng</div>
            <div style="font-size:12px;color:#16A34A;margin-top:6px"><i class="bi bi-person-plus"></i> {{ $newUsersThisMonth }} mới tháng này</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="stat-icon" style="background:#FFF1F2;color:#E11D48"><i class="bi bi-cash-stack"></i></div>
                <span style="font-size:11px;background:#FFF1F2;color:#9F1239;padding:2px 8px;border-radius:10px;font-weight:600">Doanh thu</span>
            </div>
            <div class="stat-num" style="color:#E11D48;font-size:22px">{{ number_format($totalRevenue/1000000,1) }}M</div>
            <div class="stat-label">Tổng doanh thu (đ)</div>
            <div style="font-size:12px;color:#E11D48;margin-top:6px"><i class="bi bi-graph-up"></i> {{ number_format($monthRevenue/1000000,1) }}M tháng này</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="chart-card" style="background:#fff;border-radius:12px;padding:20px;border:1px solid var(--admin-border)">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div style="font-size:14px;font-weight:700">Đơn đặt tour gần đây</div>
                    <div style="font-size:12px;color:#9CA3AF">10 đơn mới nhất</div>
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary rounded-pill" style="font-size:12px">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="w-100" style="border-collapse:collapse">
                    <thead>
                        <tr>
                            <th style="background:#F9FAFB;font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;padding:10px 14px;letter-spacing:.5px">Mã đơn</th>
                            <th style="background:#F9FAFB;font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;padding:10px 14px;letter-spacing:.5px">Tour</th>
                            <th style="background:#F9FAFB;font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;padding:10px 14px;letter-spacing:.5px">Khách hàng</th>
                            <th style="background:#F9FAFB;font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;padding:10px 14px;letter-spacing:.5px">Tổng tiền</th>
                            <th style="background:#F9FAFB;font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;padding:10px 14px;letter-spacing:.5px">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $b)
                        <tr>
                            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #F3F4F6;font-weight:600;color:#0066CC">#{{ $b->booking_code }}</td>
                            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #F3F4F6">{{ Str::limit($b->tourSlot->tour->title ?? '—', 25) }}</td>
                            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #F3F4F6">{{ $b->user->name ?? '—' }}</td>
                            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #F3F4F6;font-weight:600">{{ number_format($b->total_price/1000000,1) }}Mđ</td>
                            <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #F3F4F6">
                                @php $sc=['pending'=>['#FEF3C7','#92400E'],'paid'=>['#DBEAFE','#1E40AF'],'confirmed'=>['#D1FAE5','#065F46'],'cancelled'=>['#FEE2E2','#991B1B'],'completed'=>['#E0E7FF','#3730A3']]; $c=$sc[$b->status]??['#F3F4F6','#6B7280']; @endphp
                                <span style="background:{{ $c[0] }};color:{{ $c[1] }};font-size:11px;padding:2px 8px;border-radius:10px;font-weight:600">{{ ucfirst($b->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3" style="padding:20px">Chưa có đơn nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div style="background:#fff;border-radius:12px;padding:20px;border:1px solid var(--admin-border);margin-bottom:16px">
            <div style="font-size:14px;font-weight:700;margin-bottom:4px">Tour được đặt nhiều nhất</div>
            <div style="font-size:12px;color:#9CA3AF;margin-bottom:16px">Top 5 tour nổi bật</div>
            @forelse($topTours as $i => $tour)
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:26px;height:26px;border-radius:50%;background:{{ ['#EEF5FF','#FEF3C7','#F0FDF4','#FFF1F2','#F5F3FF'][$i] }};color:{{ ['#0066CC','#D97706','#16A34A','#E11D48','#7C3AED'][$i] }};font-size:12px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ $i+1 }}</div>
                <div class="flex-grow-1">
                    <div style="font-size:13px;font-weight:600">{{ Str::limit($tour->title,30) }}</div>
                    <div style="font-size:12px;color:#9CA3AF">{{ $tour->bookings_count }} đơn đặt</div>
                </div>
                <div style="font-size:13px;font-weight:700;color:#FF6B00">{{ number_format($tour->price_adult/1000000,1) }}M</div>
            </div>
            @empty
            <div class="text-center text-muted py-2" style="font-size:13px">Chưa có dữ liệu</div>
            @endforelse
        </div>

        <div style="background:#fff;border-radius:12px;padding:20px;border:1px solid var(--admin-border)">
            <div style="font-size:14px;font-weight:700;margin-bottom:4px">Tình trạng đơn đặt</div>
            <div style="font-size:12px;color:#9CA3AF;margin-bottom:16px">Phân bổ theo trạng thái</div>
            @foreach($bookingStats as $stat)
            @php $total=$totalBookings?:1; $pct=round($stat->count/$total*100); $bc=['pending'=>'#D97706','paid'=>'#2563EB','confirmed'=>'#16A34A','cancelled'=>'#DC2626','completed'=>'#7C3AED']; $color=$bc[$stat->status]??'#6B7280'; @endphp
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1" style="font-size:12px">
                    <span style="font-weight:500">{{ ucfirst($stat->status) }}</span>
                    <span style="color:#6B7280">{{ $stat->count }} ({{ $pct }}%)</span>
                </div>
                <div style="height:6px;background:#F3F4F6;border-radius:3px;overflow:hidden">
                    <div style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:3px"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection