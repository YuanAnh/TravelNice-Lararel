<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Booking;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTours        = Tour::count();
        $activeTours       = Tour::where('status', 'active')->count();
        $totalBookings     = Booking::count();
        $pendingBookings   = Booking::where('status', 'pending')->count();
        $totalUsers        = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)->count();
        $totalRevenue      = Booking::whereIn('status', ['paid','confirmed','completed'])->sum('total_price');
        $monthRevenue      = Booking::whereIn('status', ['paid','confirmed','completed'])
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)->sum('total_price');

        $recentBookings = Booking::with(['user','tourSlot.tour'])->latest()->take(10)->get();

        $topTours = Tour::withCount(['slots as bookings_count' => fn($q) =>
                        $q->whereHas('bookings')
                    ])->orderByDesc('bookings_count')->take(5)->get();

        $bookingStats = Booking::selectRaw('status, count(*) as count')->groupBy('status')->get();

        return view('admin.dashboard', compact(
            'totalTours','activeTours','totalBookings','pendingBookings',
            'totalUsers','newUsersThisMonth','totalRevenue','monthRevenue',
            'recentBookings','topTours','bookingStats'
        ));
    }
}