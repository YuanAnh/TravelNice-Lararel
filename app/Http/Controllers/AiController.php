<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(private GeminiService $gemini) {}

    /**
     * Chatbot — nhận tin nhắn, trả về reply
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array',
            'history.*.role' => 'in:user,assistant',
            'history.*.content' => 'string|max:1000',
        ]);

        try {
            $reply = $this->gemini->chat(
                $request->history ?? [],
                $request->message
            );

            return response()->json([
                'success' => true,
                'reply'   => $reply,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'reply'   => 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau!',
            ], 500);
        }
    }

    /**
     * Trang gợi ý tour theo sở thích
     */
    public function recommend(Request $request)
    {
        if (!$request->isMethod('post')) {
            return view('ai.recommend');
        }

        $request->validate([
            'budget'      => 'nullable|string',
            'duration'    => 'nullable|string',
            'destination' => 'nullable|string',
            'type'        => 'nullable|string',
        ]);

        try {
            $suggestions = $this->gemini->recommendTours($request->only(
                'budget', 'duration', 'destination', 'type'
            ));

            $tours = collect();
            foreach ($suggestions as $s) {
                $tour = Tour::with(['destination', 'category'])
                    ->find($s['tour_id'] ?? null);
                if ($tour) {
                    $tour->ai_reason = $s['reason'] ?? '';
                    $tours->push($tour);
                }
            }

            return view('ai.recommend', compact('tours', 'suggestions'));
        } catch (\Exception $e) {
            return view('ai.recommend')->with('error', 'Không thể kết nối AI. Vui lòng thử lại!');
        }
    }
}