<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\AdminLog;
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

        $bookings       = $query->paginate(20);
        $pendingCount   = Booking::where('status', 'pending')->count();
        $paidCount      = Booking::where('status', 'paid')->count();
        $confirmedCount = Booking::where('status', 'confirmed')->count();
        $cancelledCount = Booking::where('status', 'cancelled')->count();

        $stats = ['pending'=>$pendingCount,'confirmed'=>$confirmedCount,'paid'=>$paidCount,'cancelled'=>$cancelledCount];

        return view('admin.bookings.index', compact('bookings','stats','paidCount','pendingCount','confirmedCount','cancelledCount'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user','tourSlot.tour.destination']);
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
        if ($request->filled('discount_amount')) $data['discount_amount'] = $request->discount_amount;
        if ($request->filled('note')) $data['note'] = $request->note;
        if ($request->status === 'cancelled' && !$booking->cancelled_at) $data['cancelled_at'] = now();

        $booking->update($data);
        AdminLog::log('update', "Cập nhật đơn #{$booking->booking_code} → {$request->status}", $booking->id, 'Booking');

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Cập nhật booking thành công!');
    }

    public function confirm(Booking $booking)
    {
        $booking->update(['status' => 'confirmed']);
        AdminLog::log('confirm', "Xác nhận đơn #{$booking->booking_code}", $booking->id, 'Booking');
        return redirect()->back()->with('success', "Đã xác nhận đơn #{$booking->booking_code}!");
    }

    public function complete(Booking $booking)
    {
        $booking->update(['status' => 'completed']);
        AdminLog::log('update', "Hoàn thành đơn #{$booking->booking_code}", $booking->id, 'Booking');
        return redirect()->back()->with('success', "Đơn #{$booking->booking_code} đã hoàn thành!");
    }

    public function destroy(Booking $booking)
    {
        AdminLog::log('delete', "Xoá đơn #{$booking->booking_code}", $booking->id, 'Booking');
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Đã xoá booking!');
    }
}