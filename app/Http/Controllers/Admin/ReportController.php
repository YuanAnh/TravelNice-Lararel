<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AdminLog;

class ReportController extends Controller
{
    public function index()
    {
        $totalRevenue  = Booking::whereIn('status', ['paid','confirmed','completed'])->sum('total_price');
        $monthRevenue  = Booking::whereIn('status', ['paid','confirmed','completed'])
                                ->whereMonth('created_at', now()->month)->sum('total_price');
        $totalBookings = Booking::count();
        $totalUsers    = User::count();

        return view('admin.reports.index', compact(
            'totalRevenue', 'monthRevenue', 'totalBookings', 'totalUsers'
        ));
    }

    public function exportBookingsExcel(Request $request)
    {
        $query = Booking::with(['user', 'tourSlot.tour'])->latest();

        if ($request->from) $query->whereDate('created_at', '>=', $request->from);
        if ($request->to)   $query->whereDate('created_at', '<=', $request->to);
        if ($request->status) $query->where('status', $request->status);

        $bookings = $query->get();

        $filename = 'bookings_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fputs($file, "\xEF\xBB\xBF");

            // Header row
            fputcsv($file, [
                'Mã đơn', 'Khách hàng', 'SĐT', 'Tour',
                'Ngày khởi hành', 'Số NL', 'Số TE',
                'Tổng tiền', 'Giảm giá', 'Thực thu',
                'Trạng thái', 'Ngày đặt',
            ]);

            foreach ($bookings as $b) {
                fputcsv($file, [
                    $b->booking_code,
                    $b->user->name ?? '',
                    $b->user->phone ?? '',
                    $b->tourSlot->tour->title ?? '',
                    $b->tourSlot->departure_date?->format('d/m/Y') ?? '',
                    $b->num_adults,
                    $b->num_children,
                    $b->total_price,
                    $b->discount_amount,
                    $b->netTotal(),
                    $b->status,
                    $b->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        AdminLog::log('export', "Xuất báo cáo đơn đặt chỗ (CSV)", null, 'Booking');

        return response()->stream($callback, 200, $headers);
    }

    public function exportRevenueExcel(Request $request)
    {
        $year  = $request->year ?? now()->year;
        $rows  = [];

        for ($m = 1; $m <= 12; $m++) {
            $revenue = Booking::whereIn('status', ['paid','confirmed','completed'])
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->sum('total_price');

            $count = Booking::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();

            $rows[] = ["Tháng {$m}/{$year}", $count, $revenue];
        }

        $filename = "doanh_thu_{$year}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows, $year) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Tháng', 'Số đơn', 'Doanh thu (đ)']);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            $total = array_sum(array_column($rows, 2));
            fputcsv($file, ["TỔNG NĂM {$year}", array_sum(array_column($rows, 1)), $total]);
            fclose($file);
        };

        AdminLog::log('export', "Xuất báo cáo doanh thu (CSV)", null, 'Booking');

        return response()->stream($callback, 200, $headers);
    }

    public function exportBookingsPdf(Request $request)
    {
        $query = Booking::with(['user', 'tourSlot.tour'])->latest();
        if ($request->from)   $query->whereDate('created_at', '>=', $request->from);
        if ($request->to)     $query->whereDate('created_at', '<=', $request->to);
        if ($request->status) $query->where('status', $request->status);

        $bookings = $query->take(500)->get();

        AdminLog::log('export', "Xuất báo cáo đơn đặt chỗ (PDF)", null, 'Booking');

        return view('admin.reports.pdf-bookings', compact('bookings'));
    }
}