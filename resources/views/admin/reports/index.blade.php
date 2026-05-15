@extends('admin.layouts.admin')
@section('title', 'Báo cáo & Export')
@section('page-title', 'Báo cáo & Export')
@section('breadcrumb', 'Admin / Báo cáo')

@section('content')

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#EEF5FF;color:#0066CC"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-num" style="color:#0066CC;font-size:20px">{{ number_format($totalRevenue/1000000,1) }}M</div>
            <div class="stat-label">Tổng doanh thu</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#FEF3C7;color:#D97706"><i class="bi bi-graph-up"></i></div>
            <div class="stat-num" style="color:#D97706;font-size:20px">{{ number_format($monthRevenue/1000000,1) }}M</div>
            <div class="stat-label">Doanh thu tháng này</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#F0FDF4;color:#16A34A"><i class="bi bi-calendar-check"></i></div>
            <div class="stat-num" style="color:#16A34A">{{ $totalBookings }}</div>
            <div class="stat-label">Tổng đơn đặt</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#FFF1F2;color:#E11D48"><i class="bi bi-people"></i></div>
            <div class="stat-num" style="color:#E11D48">{{ $totalUsers }}</div>
            <div class="stat-label">Tổng người dùng</div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Export Bookings --}}
    <div class="col-lg-6">
        <div class="admin-form-card">
            <h6><i class="bi bi-file-earmark-spreadsheet me-2 text-success"></i>Export Danh sách Đặt Tour</h6>
            <form method="GET" action="{{ route('admin.reports.export-bookings') }}">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" name="to" class="form-control" value="{{ request('to', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach(['pending'=>'Chờ TT','paid'=>'Đã TT','confirmed'=>'Đã XN','completed'=>'Hoàn thành','cancelled'=>'Đã huỷ'] as $v => $l)
                            <option value="{{ $v }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <button type="submit" name="format" value="excel" class="btn btn-success w-100 rounded-pill">
                            <i class="bi bi-file-earmark-excel me-1"></i> Export CSV/Excel
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.reports.pdf-bookings') }}" target="_blank"
                           class="btn btn-danger w-100 rounded-pill">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Xem PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Export Revenue --}}
    <div class="col-lg-6">
        <div class="admin-form-card">
            <h6><i class="bi bi-bar-chart me-2 text-primary"></i>Export Báo cáo Doanh thu theo tháng</h6>
            <form method="GET" action="{{ route('admin.reports.export-revenue') }}">
                <div class="mb-3">
                    <label class="form-label">Năm</label>
                    <select name="year" class="form-select">
                        @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected':'' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    <i class="bi bi-download me-1"></i> Export Doanh thu CSV
                </button>
            </form>

            {{-- Preview bảng doanh thu theo tháng --}}
            <div class="mt-4">
                <div style="font-size:12px;font-weight:700;color:#6B7280;text-transform:uppercase;margin-bottom:8px">
                    Doanh thu {{ now()->year }} theo tháng
                </div>
                @php
                    $months = [];
                    for ($m = 1; $m <= 12; $m++) {
                        $months[] = [
                            'month' => $m,
                            'revenue' => \App\Models\Booking::whereIn('status',['paid','confirmed','completed'])
                                ->whereYear('created_at', now()->year)
                                ->whereMonth('created_at', $m)
                                ->sum('total_price'),
                            'count' => \App\Models\Booking::whereYear('created_at', now()->year)
                                ->whereMonth('created_at', $m)->count(),
                        ];
                    }
                    $maxRevenue = max(array_column($months, 'revenue')) ?: 1;
                @endphp
                @foreach($months as $m)
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div style="width:30px;font-size:11px;color:#6B7280;text-align:right">T{{ $m['month'] }}</div>
                    <div style="flex:1;height:18px;background:#F3F4F6;border-radius:4px;overflow:hidden">
                        <div style="height:100%;width:{{ $maxRevenue > 0 ? round($m['revenue']/$maxRevenue*100) : 0 }}%;background:#0066CC;border-radius:4px;transition:width .3s"></div>
                    </div>
                    <div style="font-size:11px;color:#374151;width:80px;text-align:right">
                        {{ $m['revenue'] > 0 ? number_format($m['revenue']/1000000,1).'M' : '—' }}
                    </div>
                    <div style="font-size:11px;color:#9CA3AF;width:30px">{{ $m['count'] }}đ</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection