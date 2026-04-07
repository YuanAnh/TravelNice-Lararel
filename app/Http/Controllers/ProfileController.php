<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Bookings với filter status
        $bookingQuery = Booking::with(['tourSlot.tour'])
            ->where('user_id', $user->id)
            ->latest();

        if ($status = $request->status) {
            if ($status !== 'all') {
                $bookingQuery->where('status', $status);
            }
        }

        $allBookings      = $bookingQuery->paginate(8);
        $recentBookings   = Booking::with(['tourSlot.tour'])
                                ->where('user_id', $user->id)
                                ->latest()->take(3)->get();

        $totalBookings    = Booking::where('user_id', $user->id)->count();
        $completedBookings = Booking::where('user_id', $user->id)->where('status', 'completed')->count();
        $pendingCount     = Booking::where('user_id', $user->id)->where('status', 'pending')->count();
        $wishlists        = $user->wishlistedTours()->with('destination')->get();
        $wishlistCount    = $wishlists->count();

        return view('profile.index', compact(
            'allBookings', 'recentBookings',
            'totalBookings', 'completedBookings',
            'pendingCount', 'wishlists', 'wishlistCount'
        ));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user->update($request->only('name', 'phone', 'address'));

        return redirect()->route('profile.index')->with('success', 'Cập nhật thông tin thành công!');
    }

    public function cancelBooking(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$booking->isPending()) {
            return redirect()->back()->with('error', 'Chỉ có thể huỷ đơn đang chờ xác nhận.');
        }

        $booking->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Hoàn lại slot
        $booking->tourSlot->decrement('booked_slots', $booking->num_adults + $booking->num_children);

        return redirect()->back()->with('success', 'Đã huỷ đơn đặt tour thành công.');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        auth()->logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Tài khoản đã được xoá.');
    }
}