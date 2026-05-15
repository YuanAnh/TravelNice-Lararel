<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Toggle wishlist — thêm hoặc xoá
     */
    public function toggle(Request $request, int $tourId)
    {
        $userId = auth()->id();

        $existing = Wishlist::where('user_id', $userId)
                            ->where('tour_id', $tourId)
                            ->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
        } else {
            Wishlist::create(['user_id' => $userId, 'tour_id' => $tourId]);
            $wishlisted = true;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'wishlisted' => $wishlisted,
                'message'    => $wishlisted ? 'Đã thêm vào yêu thích!' : 'Đã xoá khỏi yêu thích!',
            ]);
        }

        return redirect()->back()->with('success', $wishlisted ? 'Đã thêm vào yêu thích!' : 'Đã xoá khỏi yêu thích!');
    }
}