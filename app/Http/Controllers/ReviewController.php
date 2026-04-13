<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Tour;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tour_id'    => 'required|exists:tours,id',
            'booking_id' => 'required|exists:bookings,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        // Chỉ user sở hữu booking mới được review
        if ($booking->user_id !== auth()->id()) abort(403);

        // Chỉ đánh giá booking đã hoàn thành
        if (!$booking->isCompleted()) {
            return redirect()->back()->with('error', 'Chỉ có thể đánh giá tour đã hoàn thành!');
        }

        // Không được đánh giá 2 lần
        if (Review::where('booking_id', $request->booking_id)->exists()) {
            return redirect()->back()->with('error', 'Bạn đã đánh giá tour này rồi!');
        }

        Review::create([
            'user_id'    => auth()->id(),
            'tour_id'    => $request->tour_id,
            'booking_id' => $request->booking_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'is_approved' => 0,
        ]);

        // Cập nhật avg_rating cho tour
        $this->updateTourRating($request->tour_id);

        return redirect()->back()->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt!');
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== auth()->id()) abort(403);
        $tourId = $review->tour_id;
        $review->delete();
        $this->updateTourRating($tourId);
        return redirect()->back()->with('success', 'Đã xoá đánh giá!');
    }

    private function updateTourRating(int $tourId): void
    {
        $avg = Review::where('tour_id', $tourId)
            ->where('is_approved', 1)
            ->avg('rating');

        Tour::where('id', $tourId)->update(['avg_rating' => $avg ?? 0]);
    }
}