<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\TourSlot;
use App\Models\Tour;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tour_id'          => 'required|exists:tours,id',
            'slot_id'          => 'required|exists:tour_slots,id',
            'num_adults'       => 'required|integer|min:1',
            'num_children'     => 'nullable|integer|min:0',
            'note'             => 'nullable|string',
        ]);

        $slot = TourSlot::findOrFail($request->slot_id);
        $tour = Tour::findOrFail($request->tour_id);

        $adults   = $request->num_adults;
        $children = $request->num_children ?? 0;
        $total    = ($adults * $tour->price_adult) + ($children * $tour->price_child);

        Booking::create([
            'user_id'      => auth()->id(),
            'tour_slot_id' => $slot->id,
            'num_adults'   => $adults,
            'num_children' => $children,
            'total_price'  => $total,
            'note'         => $request->note,
            'status'       => 'pending',
        ]);

        // Tăng booked_slots
        $slot->increment('booked_slots', $adults + $children);
        if ($slot->remainingSlots() <= 0) {
            $slot->update(['status' => 'full']);
        }

        return redirect()->back()->with('success', 'Đặt tour thành công! Chúng tôi sẽ liên hệ xác nhận trong 24h.');
    }
}