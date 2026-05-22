<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AiController extends Controller
{
    public function __construct(private GeminiService $gemini) {}

    // =========================================================
    // CHAT
    // =========================================================

    public function chat(Request $request)
    {
        $request->validate([
            'message'           => 'required|string|max:500',
            'history'           => 'nullable|array|max:20',
            'history.*.role'    => 'required|in:user,assistant',
            'history.*.content' => 'required|string|max:2000',
        ]);

        try {
            $userId = Auth::id();

            $reply = $this->gemini->chat(
                $request->history ?? [],
                $request->message,
                $userId
            );

            \Log::info('RAW AI REPLY: ' . $reply);

            $safeReply = str_replace(['<', '>'], [' dưới ', ' trên '], $reply);

            return response()->json([
                'success' => true,
                'reply'   => Str::markdown($safeReply),
            ]);
        } catch (\Exception $e) {
            \Log::error('AiController@chat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'reply'   => 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau!',
            ], 500);
        }
    }

    // =========================================================
    // RECOMMEND
    // =========================================================

    public function recommend(Request $request)
    {
        $userId          = Auth::id();
        $behaviorProfile = $userId ? $this->gemini->getBehaviorProfile($userId) : null;

        if (!$request->isMethod('post')) {
            return view('ai.recommend', compact('behaviorProfile'));
        }

        $request->validate([
            'budget'      => 'nullable|string|max:50',
            'duration'    => 'nullable|string|max:20',
            'destination' => 'nullable|string|max:100',
            'type'        => 'nullable|string|max:50',
        ]);

        try {
            $suggestions = $this->gemini->recommendTours(
                $request->only('budget', 'duration', 'destination', 'type'),
                $userId
            );

            $tours = collect();
            foreach ($suggestions as $s) {
                $tour = Tour::with(['destination', 'category'])->find($s['tour_id'] ?? null);
                if ($tour) {
                    $tour->ai_reason      = $s['reason'] ?? '';
                    $tour->ai_match_score = $s['match_score'] ?? null;
                    $tours->push($tour);
                }
            }

            return view('ai.recommend', compact('tours', 'suggestions', 'behaviorProfile'));

        } catch (\Exception $e) {
            \Log::error('AiController@recommend: ' . $e->getMessage());
            return view('ai.recommend', compact('behaviorProfile'))
                ->with('error', 'Không thể kết nối AI. Vui lòng thử lại!');
        }
    }

    // =========================================================
    // TRACK — AJAX endpoint nhận tracking từ frontend
    // =========================================================

    /**
     * POST /ai/track
     * Body: { event: 'tour_view', tour_id: 5, view_seconds: 120 }
     */
    public function track(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'reason' => 'unauthenticated']);
        }

        $request->validate([
            'event'       => 'required|in:tour_view,wishlist',
            'tour_id'     => 'required|integer|exists:tours,id',
            'view_seconds'=> 'nullable|integer|min:0|max:3600',
        ]);

        $userId = Auth::id();

        try {
            match ($request->event) {
                'tour_view' => $this->gemini->trackTourView(
                    $userId,
                    $request->tour_id,
                    '',
                    $request->view_seconds ?? 0
                ),
                'wishlist'  => $this->gemini->trackWishlist($userId, $request->tour_id),
            };

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            \Log::error('AiController@track: ' . $e->getMessage());
            return response()->json(['ok' => false], 500);
        }
    }
}