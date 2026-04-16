<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'tour'])->latest();

        if ($approved = $request->approved) {
            $query->where('is_approved', $approved === 'yes' ? 1 : 0);
        }

        $reviews        = $query->paginate(20);
        $pendingCount   = Review::where('is_approved', 0)->count();
        $approvedCount  = Review::where('is_approved', 1)->count();

        return view('admin.reviews.index', compact('reviews', 'pendingCount', 'approvedCount'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => 1]);

        // Cập nhật avg_rating cho tour
        $avg = Review::where('tour_id', $review->tour_id)
                     ->where('is_approved', 1)
                     ->avg('rating');
        $review->tour->update(['avg_rating' => $avg ?? 0]);

        return redirect()->back()->with('success', 'Đã duyệt đánh giá!');
    }

    public function destroy(Review $review)
    {
        $tourId = $review->tour_id;
        $review->delete();

        $avg = Review::where('tour_id', $tourId)->where('is_approved', 1)->avg('rating');
        \App\Models\Tour::where('id', $tourId)->update(['avg_rating' => $avg ?? 0]);

        return redirect()->back()->with('success', 'Đã xoá đánh giá!');
    }
}