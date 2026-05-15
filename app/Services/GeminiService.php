<?php

namespace App\Services;

use App\Models\Tour;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', '');
        $this->model  = config('services.gemini.model', 'gemini-2.0-flash-lite');
    }

    public function chat(array $history, string $userMessage): string
    {
        if (!empty($this->apiKey)) {
            try {
                return $this->callGeminiApi($history, $userMessage);
            } catch (\Exception $e) {
                \Log::warning('Gemini failed: ' . $e->getMessage());
            }
        }
        return $this->smartFallback($userMessage);
    }

    public function recommendTours(array $preferences): array
    {
        if (!empty($this->apiKey)) {
            try {
                return $this->callGeminiRecommend($preferences);
            } catch (\Exception $e) {
                \Log::warning('Gemini recommend failed');
            }
        }
        return $this->fallbackRecommend($preferences);
    }

    private function callGeminiApi(array $history, string $userMessage): string
    {
        $toursContext = $this->buildToursContext();
        $contents = [];
        $contents[] = ['role' => 'user', 'parts' => [['text' =>
            "Bạn là trợ lý AI TravelNice, trả lời tiếng Việt thân thiện. Tour hiện có:\n{$toursContext}\n\nXin chào!"
        ]]];
        $contents[] = ['role' => 'model', 'parts' => [['text' => 'Xin chào! Tôi là trợ lý AI TravelNice. Tôi có thể giúp bạn tìm tour phù hợp! 😊']]];
        foreach (array_slice($history, -8) as $msg) {
            $contents[] = ['role' => $msg['role'] === 'user' ? 'user' : 'model', 'parts' => [['text' => $msg['content']]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

        $response = \Illuminate\Support\Facades\Http::timeout(20)->post(
            "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}",
            ['contents' => $contents, 'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 600]]
        );
        if ($response->failed()) throw new \Exception($response->body());
        return $response->json('candidates.0.content.parts.0.text') ?? 'Xin lỗi, tôi không thể trả lời lúc này!';
    }

    private function callGeminiRecommend(array $preferences): array
    {
        $toursContext = $this->buildToursContext();
        $prompt = "Tour:\n{$toursContext}\n\nGợi ý 3 tour phù hợp:\n"
            . "Ngân sách: " . ($preferences['budget'] ?? 'bất kỳ') . "\n"
            . "Thời gian: " . ($preferences['duration'] ?? 'linh hoạt') . "\n"
            . "Điểm đến: " . ($preferences['destination'] ?? 'bất kỳ') . "\n"
            . "Loại hình: " . ($preferences['type'] ?? 'bất kỳ') . "\n"
            . "Trả về JSON (không markdown): [{\"tour_id\":1,\"reason\":\"lý do\"}]";

        $response = \Illuminate\Support\Facades\Http::timeout(20)->post(
            "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}",
            ['contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
             'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 400]]
        );
        if ($response->failed()) throw new \Exception($response->body());
        $text = preg_replace('/```json|```/', '', $response->json('candidates.0.content.parts.0.text') ?? '[]');
        return json_decode(trim($text), true) ?? [];
    }

    private function smartFallback(string $message): string
    {
        $msg = mb_strtolower($message);
        $tours = Tour::with(['destination', 'category'])->where('status', 'active')->take(10)->get();

        if (preg_match('/^(xin chào|chào|hello|hi|hey)/u', $msg)) {
            return "Xin chào! 👋 Tôi là trợ lý AI của TravelNice.\n\nTôi có thể giúp bạn:\n• Tìm tour theo điểm đến\n• Tư vấn ngân sách phù hợp\n• Hướng dẫn đặt tour\n\nBạn muốn đi đâu? 😊";
        }

        if (preg_match('/giá|bao nhiêu|chi phí|budget/u', $msg)) {
            $cheap = $tours->sortBy('price_adult')->first();
            $exp = $tours->sortByDesc('price_adult')->first();
            if ($cheap && $exp) {
                return "💰 Tour tại TravelNice từ **" . number_format($cheap->price_adult,0,',','.') . "đ** đến **"
                    . number_format($exp->price_adult,0,',','.') . "đ**.\n\nBạn có ngân sách khoảng bao nhiêu để tôi gợi ý phù hợp?";
            }
        }

        $dests = ['nhật bản','hàn quốc','đà nẵng','phú quốc','hạ long','châu âu','thái lan','singapore','trung quốc','nha trang','sapa','hội an'];
        foreach ($dests as $dest) {
            if (str_contains($msg, $dest)) {
                $matched = $tours->filter(fn($t) =>
                    str_contains(mb_strtolower($t->destination->name ?? ''), $dest) ||
                    str_contains(mb_strtolower($t->title), $dest)
                );
                if ($matched->isNotEmpty()) {
                    $list = $matched->take(3)->map(fn($t) =>
                        "• **{$t->title}** — " . number_format($t->price_adult,0,',','.') . "đ | {$t->duration_days}N"
                    )->implode("\n");
                    return "🗺️ Tour " . ucwords($dest) . " hiện có:\n\n{$list}\n\nBạn muốn biết thêm chi tiết tour nào?";
                }
                return "Hiện chưa có tour " . ucwords($dest) . ". Bạn thử xem các điểm đến khác nhé!";
            }
        }

        if (preg_match('/ngắn|cuối tuần|2 ngày|3 ngày/u', $msg)) {
            $short = $tours->filter(fn($t) => $t->duration_days <= 3);
            if ($short->isNotEmpty()) {
                $list = $short->take(3)->map(fn($t) => "• **{$t->title}** — {$t->duration_days}N — " . number_format($t->price_adult,0,',','.') . "đ")->implode("\n");
                return "⏱️ Tour ngắn ngày phù hợp:\n\n{$list}";
            }
        }

        if (preg_match('/gia đình|trẻ em|family/u', $msg)) {
            return "👨‍👩‍👧 Tour gia đình tại TravelNice:\n\n• Giá trẻ em ưu đãi\n• Lịch trình nhẹ nhàng\n• Hỗ trợ gia đình có con nhỏ\n\nBạn muốn đi điểm đến nào và bé bao nhiêu tuổi?";
        }

        if (preg_match('/đặt|booking|mua|thanh toán/u', $msg)) {
            return "📋 Cách đặt tour:\n\n1️⃣ Chọn tour → bấm **Đặt ngay**\n2️⃣ Điền thông tin & chọn ngày\n3️⃣ Thanh toán qua **VNPay** hoặc **MoMo**\n4️⃣ Nhận xác nhận!\n\nCần hỗ trợ thêm không? 😊";
        }

        if (preg_match('/gợi ý|recommend|đi đâu|tư vấn/u', $msg)) {
            if ($tours->isNotEmpty()) {
                $random = $tours->random(min(3, $tours->count()));
                $list = $random->map(fn($t) => "• **{$t->title}**\n  📍 {$t->destination->name} | {$t->duration_days}N | " . number_format($t->price_adult,0,',','.') . "đ")->implode("\n\n");
                return "✨ Gợi ý tour nổi bật:\n\n{$list}\n\nBạn thích loại hình nào? Nghỉ dưỡng, khám phá hay văn hóa?";
            }
        }

        return "Cảm ơn bạn! 😊 TravelNice có **" . $tours->count() . " tour** hấp dẫn.\n\nBạn hỏi về:\n• Tour theo điểm đến\n• Giá & ngân sách\n• Cách đặt tour\n• Tour gia đình, ngắn ngày\n\nHoặc vào **Gợi ý AI** để tìm tour phù hợp nhất! 🎯";
    }

    private function fallbackRecommend(array $preferences): array
    {
        $query = Tour::with(['destination','category'])->where('status','active');
        if (!empty($preferences['budget'])) {
            $b = $preferences['budget'];
            if (str_contains($b,'dưới 5')) $query->where('price_adult','<',5000000);
            elseif (str_contains($b,'5-15')) $query->whereBetween('price_adult',[5000000,15000000]);
            elseif (str_contains($b,'15-30')) $query->whereBetween('price_adult',[15000000,30000000]);
            elseif (str_contains($b,'trên 30')) $query->where('price_adult','>',30000000);
        }
        if (!empty($preferences['duration'])) {
            $d = $preferences['duration'];
            if ($d==='1-3') $query->whereBetween('duration_days',[1,3]);
            elseif ($d==='4-6') $query->whereBetween('duration_days',[4,6]);
            elseif ($d==='7-10') $query->whereBetween('duration_days',[7,10]);
            elseif ($d==='trên 10') $query->where('duration_days','>',10);
        }
        if (!empty($preferences['destination'])) {
            $dest = $preferences['destination'];
            $query->where(fn($q) => $q->whereHas('destination',fn($q2) => $q2->where('name','like',"%{$dest}%"))->orWhere('title','like',"%{$dest}%"));
        }
        return $query->orderByDesc('avg_rating')->take(3)->get()->map(fn($t) => [
            'tour_id' => $t->id,
            'reason'  => "Phù hợp tiêu chí — {$t->duration_days} ngày, giá " . number_format($t->price_adult,0,',','.') . "đ, điểm đến " . ($t->destination->name ?? '')
        ])->toArray();
    }

    private function buildToursContext(): string
    {
        $tours = Tour::with(['destination','category'])->where('status','active')->take(20)->get();
        if ($tours->isEmpty()) return 'Chưa có tour.';
        return $tours->map(fn($t) => "ID:{$t->id}|{$t->title}|{$t->destination->name}|{$t->duration_days}N|" . number_format($t->price_adult,0,',','.') . "đ")->implode("\n");
    }
}