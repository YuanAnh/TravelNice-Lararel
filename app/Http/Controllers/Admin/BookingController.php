<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'tourSlot.tour'])->latest();

        if ($q = $request->q) {
            $query->where(function ($q2) use ($q) {
                $q2->where('booking_code', 'like', "%$q%")
                   ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%$q%")
                       ->orWhere('phone', 'like', "%$q%"));
            });
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(20);

        $stats = [
            'pending'   => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'paid'      => Booking::where('status', 'paid')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'tourSlot.tour.destination']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        return view('admin.bookings.edit', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'status'          => 'required|in:pending,paid,confirmed,cancelled,completed',
            'discount_amount' => 'nullable|numeric|min:0',
            'note'            => 'nullable|string',
        ]);

        $data = ['status' => $request->status];

        if ($request->filled('discount_amount')) {
            $data['discount_amount'] = $request->discount_amount;
        }
        if ($request->filled('note')) {
            $data['note'] = $request->note;
        }
        if ($request->status === 'cancelled' && !$booking->cancelled_at) {
            $data['cancelled_at'] = now();
        }

        $booking->update($data);

        return redirect()->route('admin.bookings.show', $booking)
                         ->with('success', 'Cập nhật booking thành công!');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Đã xoá booking!');
    }
}