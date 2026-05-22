<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\UserBehavior;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    private array $availableModels = [
        'gemini-2.5-flash',
        'gemini-2.0-flash',
        'gemini-flash-latest',
        'gemini-2.0-flash-lite',
        'gemini-2.5-pro',
        'gemini-pro-latest',
    ];

    // Bảng giá ước tính phương tiện (VND)
    private array $transportEstimates = [
        'hà nội-hồ chí minh'    => ['fly' => [1200000, 3500000], 'bus' => [300000, 500000],  'train' => [400000, 900000]],
        'hà nội-đà nẵng'        => ['fly' => [800000,  2500000], 'bus' => [250000, 400000],  'train' => [300000, 700000]],
        'hà nội-hạ long'        => ['fly' => null,               'bus' => [150000, 250000],  'car'   => [200000, 350000]],
        'hồ chí minh-đà lạt'    => ['fly' => [600000,  1800000], 'bus' => [150000, 280000],  'car'   => [250000, 400000]],
        'hồ chí minh-phú quốc'  => ['fly' => [700000,  2000000], 'ferry' => [250000, 400000]],
        'hồ chí minh-nha trang' => ['fly' => [600000,  1800000], 'bus' => [150000, 280000],  'train' => [250000, 600000]],
        'hà nội-sapa'           => ['fly' => null,               'bus' => [120000, 200000],  'train' => [150000, 350000]],
        'hà nội-ninh bình'      => ['fly' => null,               'bus' => [80000,  150000],  'car'   => [100000, 180000]],
    ];

    // Điểm du lịch theo tháng tốt nhất
    private array $seasonalDestinations = [
        1  => ['sapa', 'hà giang', 'mộc châu', 'đà lạt'],
        2  => ['sapa', 'hà giang', 'ninh bình', 'hội an'],
        3  => ['hội an', 'đà nẵng', 'huế', 'phú quốc'],
        4  => ['hạ long', 'ninh bình', 'cát bà', 'côn đảo'],
        5  => ['phú quốc', 'côn đảo', 'nha trang', 'đà nẵng'],
        6  => ['phú quốc', 'côn đảo', 'hạ long', 'cát bà'],
        7  => ['hạ long', 'cát bà', 'sầm sơn', 'đà nẵng'],
        8  => ['hạ long', 'đà nẵng', 'hội an', 'nha trang'],
        9  => ['hội an', 'đà lạt', 'mù cang chải', 'hà giang'],
        10 => ['hội an', 'đà lạt', 'sapa', 'mù cang chải'],
        11 => ['đà lạt', 'hội an', 'hà nội', 'ninh bình'],
        12 => ['sapa', 'mộc châu', 'đà lạt', 'hội an'],
    ];

    public function __construct()
    {
        $this->apiKey = trim(config('services.gemini.key', ''));
    }

    // =========================================================
    // PUBLIC — CHAT
    // =========================================================

    public function chat(array $history, string $userMessage, ?int $userId = null): string
    {
        if ($userId) {
            $this->trackChatBehavior($userId, $userMessage);
        }

        if (!empty($this->apiKey)) {
            try {
                return $this->callGeminiApi($history, $userMessage);
            } catch (\Exception $e) {
                Log::warning('Gemini chat failed: ' . $e->getMessage());
            }
        }
        return $this->smartFallback($userMessage);
    }

    // =========================================================
    // PUBLIC — RECOMMEND
    // =========================================================

    public function recommendTours(array $preferences, ?int $userId = null): array
    {
        $behaviorProfile = null;

        if ($userId) {
            $behaviorProfile   = $this->buildBehaviorProfile($userId);
            $preferences       = $this->enrichWithBehavior($preferences, $behaviorProfile);
        }

        if (!empty($this->apiKey)) {
            try {
                return $this->callGeminiRecommend($preferences, $behaviorProfile);
            } catch (\Exception $e) {
                Log::warning('Gemini recommend failed: ' . $e->getMessage());
            }
        }
        return $this->fallbackRecommend($preferences, $behaviorProfile);
    }

    // =========================================================
    // PUBLIC — BEHAVIOR TRACKING (gọi từ Controller)
    // =========================================================

    /**
     * Ghi nhận lượt xem tour — gọi từ TourController@show
     */
    public function trackTourView(int $userId, int $tourId, string $destination = '', int $viewSeconds = 0): void
    {
        // Cập nhật cache nhanh
        $key  = "behavior:user:{$userId}";
        $data = Cache::get($key, ['viewed_tours' => [], 'destinations' => []]);

        $data['viewed_tours'] = array_slice(
            array_unique(array_merge([$tourId], $data['viewed_tours'])),
            0, 20
        );
        if ($destination) {
            $dest = mb_strtolower($destination);
            $data['destinations'][$dest] = ($data['destinations'][$dest] ?? 0) + 1;
        }
        Cache::put($key, $data, now()->addDays(30));

        // Lưu DB bền vững
        $tour = Tour::with(['destination', 'category'])->find($tourId);
        UserBehavior::create([
            'user_id'       => $userId,
            'event_type'    => UserBehavior::EVENT_TOUR_VIEW,
            'tour_id'       => $tourId,
            'destination'   => $tour?->destination?->name ?? $destination,
            'category'      => $tour?->category?->name,
            'duration_days' => $tour?->duration_days,
            'price_point'   => $tour?->price_adult,
            'view_seconds'  => $viewSeconds,
        ]);
    }

    /**
     * Ghi nhận đặt tour — gọi từ BookingController
     */
    public function trackBooking(int $userId, int $tourId): void
    {
        $tour = Tour::with(['destination', 'category'])->find($tourId);
        UserBehavior::create([
            'user_id'       => $userId,
            'event_type'    => UserBehavior::EVENT_BOOKING,
            'tour_id'       => $tourId,
            'destination'   => $tour?->destination?->name,
            'category'      => $tour?->category?->name,
            'duration_days' => $tour?->duration_days,
            'price_point'   => $tour?->price_adult,
        ]);
    }

    /**
     * Ghi nhận tour yêu thích — gọi từ WishlistController
     */
    public function trackWishlist(int $userId, int $tourId): void
    {
        $tour = Tour::with(['destination', 'category'])->find($tourId);
        UserBehavior::create([
            'user_id'       => $userId,
            'event_type'    => UserBehavior::EVENT_WISHLIST,
            'tour_id'       => $tourId,
            'destination'   => $tour?->destination?->name,
            'category'      => $tour?->category?->name,
            'duration_days' => $tour?->duration_days,
            'price_point'   => $tour?->price_adult,
        ]);
    }

    /**
     * Trả về behavior profile công khai (dùng cho Controller hiển thị UI)
     */
    public function getBehaviorProfile(int $userId): array
    {
        return $this->buildBehaviorProfile($userId);
    }

    /**
     * Xóa cache tour khi admin thêm/sửa
     */
    public function clearToursCache(): void
    {
        Cache::forget('gemini_tours_context');
        Cache::forget('tours_for_chatbot');
    }

    // =========================================================
    // PRIVATE — BEHAVIOR ENGINE
    // =========================================================

    /**
     * Xây dựng behavior profile đầy đủ từ DB (30 ngày gần nhất)
     * Trả về mảng có thể dùng trực tiếp trong prompt AI và UI
     */
    private function buildBehaviorProfile(int $userId): array
    {
        $cacheKey = "behavior_profile:user:{$userId}";
        return Cache::remember($cacheKey, 300, function () use ($userId) {

            $logs = UserBehavior::forUser($userId)->recent(30)->get();

            if ($logs->isEmpty()) {
                return $this->emptyProfile();
            }

            // ── Điểm đến: đếm có trọng số theo loại event ──────────
            $destScores = [];
            $catScores  = [];
            $pricePoints = [];
            $durationPoints = [];
            $viewedTourIds = [];

            $weights = [
                UserBehavior::EVENT_BOOKING      => 10,
                UserBehavior::EVENT_WISHLIST      => 5,
                UserBehavior::EVENT_TOUR_VIEW     => 2,
                UserBehavior::EVENT_CHAT_MENTION  => 1,
                UserBehavior::EVENT_TOUR_SEARCH   => 1,
            ];

            foreach ($logs as $log) {
                $w = $weights[$log->event_type] ?? 1;

                // Bonus: xem lâu hơn 60 giây = +1 điểm
                if ($log->view_seconds > 60) $w += 1;
                // Bonus: xem lâu hơn 3 phút = +2 điểm
                if ($log->view_seconds > 180) $w += 2;

                if ($log->destination) {
                    $dest = mb_strtolower($log->destination);
                    $destScores[$dest] = ($destScores[$dest] ?? 0) + $w;
                }
                if ($log->category) {
                    $cat = mb_strtolower($log->category);
                    $catScores[$cat] = ($catScores[$cat] ?? 0) + $w;
                }
                if ($log->price_point) {
                    $pricePoints[] = $log->price_point;
                }
                if ($log->duration_days) {
                    $durationPoints[] = $log->duration_days;
                }
                if ($log->tour_id && $log->event_type === UserBehavior::EVENT_TOUR_VIEW) {
                    $viewedTourIds[] = $log->tour_id;
                }
            }

            arsort($destScores);
            arsort($catScores);

            $topDestinations  = array_slice(array_keys($destScores), 0, 3);
            $topCategories    = array_slice(array_keys($catScores), 0, 2);
            $viewedTourIds    = array_unique(array_slice($viewedTourIds, 0, 20));

            // Ngân sách ưa thích: trung vị
            $avgPrice = count($pricePoints) > 0 ? (int) (array_sum($pricePoints) / count($pricePoints)) : null;

            // Thời gian ưa thích: trung vị
            $avgDuration = count($durationPoints) > 0 ? (int) round(array_sum($durationPoints) / count($durationPoints)) : null;

            // Engagement level
            $totalEvents  = $logs->count();
            $bookingCount = $logs->where('event_type', UserBehavior::EVENT_BOOKING)->count();
            $engagement   = match(true) {
                $bookingCount >= 2  => 'loyal',     // khách thân thiết
                $bookingCount >= 1  => 'converter',  // đã từng book
                $totalEvents >= 10  => 'explorer',   // khám phá nhiều
                default             => 'new',
            };

            return [
                'top_destinations'  => $topDestinations,
                'top_categories'    => $topCategories,
                'viewed_tour_ids'   => $viewedTourIds,
                'avg_price'         => $avgPrice,
                'avg_duration'      => $avgDuration,
                'total_events'      => $totalEvents,
                'booking_count'     => $bookingCount,
                'engagement'        => $engagement,
                'dest_scores'       => $destScores,
                'cat_scores'        => $catScores,
            ];
        });
    }

    private function emptyProfile(): array
    {
        return [
            'top_destinations' => [],
            'top_categories'   => [],
            'viewed_tour_ids'  => [],
            'avg_price'        => null,
            'avg_duration'     => null,
            'total_events'     => 0,
            'booking_count'    => 0,
            'engagement'       => 'new',
            'dest_scores'      => [],
            'cat_scores'       => [],
        ];
    }

    /**
     * Làm giàu preferences từ behavior profile
     */
    private function enrichWithBehavior(array $preferences, array $profile): array
    {
        // Nếu user chưa chọn điểm đến → lấy từ hành vi
        if (empty($preferences['destination']) && !empty($profile['top_destinations'])) {
            $preferences['destination'] = implode(', ', $profile['top_destinations']);
        }

        // Nếu chưa chọn loại hình → lấy từ hành vi
        if (empty($preferences['type']) && !empty($profile['top_categories'])) {
            $preferences['type'] = implode(', ', $profile['top_categories']);
        }

        // Nếu chưa chọn ngân sách → gợi ý từ avg price
        if (empty($preferences['budget']) && $profile['avg_price']) {
            $p = $profile['avg_price'];
            if ($p < 5000000)       $preferences['budget'] = 'dưới 5 triệu';
            elseif ($p < 15000000)  $preferences['budget'] = '5-15 triệu';
            elseif ($p < 30000000)  $preferences['budget'] = '15-30 triệu';
            else                    $preferences['budget'] = 'trên 30 triệu';
        }

        // Nếu chưa chọn thời gian → gợi ý từ avg duration
        if (empty($preferences['duration']) && $profile['avg_duration']) {
            $d = $profile['avg_duration'];
            if ($d <= 3)        $preferences['duration'] = '1-3';
            elseif ($d <= 6)    $preferences['duration'] = '4-6';
            elseif ($d <= 10)   $preferences['duration'] = '7-10';
            else                $preferences['duration'] = 'trên 10';
        }

        // Thêm context hành vi cho AI
        $preferences['_behavior'] = [
            'recently_viewed'      => implode(',', array_slice($profile['viewed_tour_ids'], 0, 5)),
            'favorite_destinations'=> implode(', ', $profile['top_destinations']),
            'favorite_categories'  => implode(', ', $profile['top_categories']),
            'engagement'           => $profile['engagement'],
            'booking_count'        => $profile['booking_count'],
        ];

        return $preferences;
    }

    // =========================================================
    // PRIVATE — GEMINI API (CHAT)
    // =========================================================

    private function callGeminiApi(array $history, string $userMessage): string
    {
        $toursContext  = $this->buildToursContext();
        $seasonContext = $this->buildSeasonContext();
        $transportNote = $this->buildTransportNote($userMessage);

        $systemPrompt = <<<PROMPT
        Bạn là trợ lý AI TravelNice — chuyên tư vấn tour du lịch Việt Nam và quốc tế.
        Trả lời bằng tiếng Việt, thân thiện, dùng emoji phù hợp.

        **NGUYÊN TẮC TRẢ LỜI (BẮT BUỘC):**
        - Chỉ trả lời TRỰC TIẾP, KHÔNG viết suy nghĩ, KHÔNG giải thích quá trình xử lý.
        - Tổng câu trả lời KHÔNG quá 200 từ. Đếm kỹ trước khi trả lời.
        - KHÔNG lặp lại thông tin user vừa nói.
        - Dùng gạch đầu dòng ngắn, KHÔNG viết đoạn văn dài.
        - Gợi ý tối đa 2 tour: chỉ ghi tên + giá + 1 lý do ngắn.
        - Nếu có bảng so sánh: tối đa 3 dòng.
        - Cuối trả lời: 1 câu hỏi ngắn duy nhất.
        - KHÔNG dùng ký tự "<" hoặc ">" — thay bằng "dưới" / "trên".
        - KHÔNG dùng markdown đậm (**text**) — thay bằng chữ in hoa hoặc emoji.

        **CÁCH XỬ LÝ TỪNG TÌNH HUỐNG:**
        1. Hỏi tour cho gia đình / nhóm: hỏi số người, độ tuổi trẻ em, ngân sách, tháng đi.
        2. Hỏi hành trình từ A đến B: so sánh tour trọn gói vs tự túc (phương tiện + giá).
        3. Hỏi giá nhóm: tính giảm nhóm 2-4 người -5%, 5+ người -10-15%.
        4. Hỏi chung chung: hỏi lại ngân sách, số ngày, tháng đi, thích biển hay núi.

        **DỮ LIỆU TOUR HIỆN CÓ:**
        {$toursContext}

        **GỢI Ý THEO MÙA (tháng hiện tại):**
        {$seasonContext}

        **QUY TẮC DỮ LIỆU:**
        - Chỉ dùng thông tin từ danh sách tour trên, KHÔNG bịa đặt.
        - Ưu tiên tour phù hợp mùa hiện tại.
        - Nếu không có tour phù hợp: nói thật và hướng dẫn liên hệ tư vấn viên.

        {$transportNote}
        PROMPT;

        $contents = [];
        $contents[] = ['role' => 'user',  'parts' => [['text' => $systemPrompt . "\n\nXin chào!"]]];
        $contents[] = ['role' => 'model', 'parts' => [['text' => 'Xin chào! Tôi là trợ lý AI TravelNice. Tôi có thể giúp bạn tìm tour, so sánh chi phí di chuyển, và gợi ý điểm đến phù hợp mùa! 😊']]];

        foreach (array_slice($history, -10) as $msg) {
            $contents[] = [
                'role'  => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['content']]],
            ];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

        return $this->callWithFallback($contents);
    }

    // =========================================================
    // PRIVATE — GEMINI API (RECOMMEND) — FIX BUG + NÂNG CẤP
    // =========================================================

    private function callGeminiRecommend(array $preferences, ?array $behaviorProfile = null): array
    {
        $toursContext  = $this->buildToursContext();
        $seasonContext = $this->buildSeasonContext();

        $behavior = $preferences['_behavior'] ?? [
            'favorite_destinations' => 'Chưa có dữ liệu',
            'favorite_categories'   => 'Chưa có dữ liệu',
            'recently_viewed'       => 'Chưa có dữ liệu',
            'booking_count'         => 0,
            'engagement'            => 'new'
        ];
        $engagementNote = match($behavior['engagement'] ?? 'new') {
            'loyal'     => 'User là khách thân thiết (đã book nhiều lần). Ưu tiên tour cao cấp hơn bình thường.',
            'converter' => 'User đã từng đặt tour. Gợi ý tour khác điểm đến để khám phá thêm.',
            'explorer'  => 'User đang tìm hiểu nhiều. Gợi ý tour có giá trị tốt và đánh giá cao.',
            default     => 'User mới. Gợi ý tour phổ biến, dễ quyết định.',
        };

        $prompt = <<<PROMPT
Dữ liệu tour:
{$toursContext}

Gợi ý mùa hiện tại: {$seasonContext}

Tiêu chí người dùng:
- Ngân sách: {$preferences['budget']}
- Thời gian: {$preferences['duration']}
- Điểm đến mong muốn: {$preferences['destination']}
- Loại hình: {$preferences['type']}

Hành vi người dùng (30 ngày gần nhất):
- Điểm đến yêu thích: {$behavior['favorite_destinations']}
- Loại tour hay xem: {$behavior['favorite_categories']}
- Tour đã xem (ID): {$behavior['recently_viewed']}
- Số lần đặt tour: {$behavior['booking_count']}
- Hồ sơ người dùng: {$engagementNote}

Thứ tự ưu tiên khi gợi ý:
1. Tour phù hợp MÙA hiện tại
2. Tour phù hợp HÀNH VI browsing (điểm đến, loại hình user hay xem)
3. Tour CHƯA từng xem (tránh lặp tour_id trong danh sách đã xem)
4. Tour phù hợp tiêu chí ngân sách & thời gian
5. Tour có avg_rating cao

Trả về JSON thuần (KHÔNG markdown, KHÔNG giải thích, KHÔNG backtick):
[{"tour_id":1,"reason":"lý do ngắn gọn tiếng Việt, nêu rõ tại sao phù hợp với user này","match_score":85}]

Gợi ý đúng 3 tour. match_score từ 60-100.
PROMPT;

        $contents = [
            ['role' => 'user', 'parts' => [['text' => $prompt]]],
        ];

        $raw = $this->callWithFallback($contents, 0.3);

        // Parse JSON — strip markdown fences nếu có
        $text    = preg_replace('/```json|```/i', '', $raw);
        $decoded = json_decode(trim($text), true);

        if (!is_array($decoded) || empty($decoded)) {
            throw new \Exception('Gemini returned invalid JSON for recommend');
        }

        return $decoded;
    }

    // =========================================================
    // PRIVATE — FALLBACK RECOMMEND (không cần API key)
    // =========================================================

    public function fallbackRecommend(array $preferences, ?array $behaviorProfile = null): array
    {
        $query = Tour::with(['destination', 'category'])->where('status', 'active');

        if (!empty($preferences['budget'])) {
            $b = $preferences['budget'];
            if (str_contains($b, 'dưới 5'))   $query->where('price_adult', '<', 5000000);
            elseif (str_contains($b, '5-15'))  $query->whereBetween('price_adult', [5000000, 15000000]);
            elseif (str_contains($b, '15-30')) $query->whereBetween('price_adult', [15000000, 30000000]);
            elseif (str_contains($b, 'trên 30')) $query->where('price_adult', '>', 30000000);
        }

        if (!empty($preferences['duration'])) {
            $d = $preferences['duration'];
            if ($d === '1-3')         $query->whereBetween('duration_days', [1, 3]);
            elseif ($d === '4-6')     $query->whereBetween('duration_days', [4, 6]);
            elseif ($d === '7-10')    $query->whereBetween('duration_days', [7, 10]);
            elseif ($d === 'trên 10') $query->where('duration_days', '>', 10);
        }

        if (!empty($preferences['destination'])) {
            $dest = $preferences['destination'];
            $query->where(fn($q) =>
                $q->whereHas('destination', fn($q2) => $q2->where('name', 'like', "%{$dest}%"))
                  ->orWhere('title', 'like', "%{$dest}%")
            );
        }

        $seasonalDests   = $this->getSeasonalDestinations();
        $viewedTourIds   = $behaviorProfile['viewed_tour_ids'] ?? [];
        $favDestinations = $behaviorProfile['top_destinations'] ?? [];

        $tours  = $query->orderByDesc('avg_rating')->take(15)->get();

        // Scoring engine
        $scored = $tours->map(function ($t) use ($seasonalDests, $viewedTourIds, $favDestinations) {
            $name  = mb_strtolower($t->destination->name ?? '');
            $title = mb_strtolower($t->title);
            $score = (float)($t->avg_rating ?? 3.0);

            // +1.5 nếu phù hợp mùa
            foreach ($seasonalDests as $sd) {
                if (str_contains($name, $sd) || str_contains($title, $sd)) {
                    $score += 1.5;
                    break;
                }
            }

            // +2 nếu thuộc điểm đến yêu thích từ hành vi
            foreach ($favDestinations as $fd) {
                if (str_contains($name, mb_strtolower($fd))) {
                    $score += 2.0;
                    break;
                }
            }

            // -0.5 nếu user đã từng xem (khuyến khích khám phá mới)
            if (in_array($t->id, $viewedTourIds)) {
                $score -= 0.5;
            }

            $t->_score = $score;
            return $t;
        })->sortByDesc('_score')->take(3);

        return $scored->map(function ($t) use ($seasonalDests, $favDestinations) {
            $name     = mb_strtolower($t->destination->name ?? '');
            $isSeason = false;
            $isFav    = false;

            foreach ($seasonalDests as $sd) {
                if (str_contains($name, $sd)) { $isSeason = true; break; }
            }
            foreach ($favDestinations as $fd) {
                if (str_contains($name, mb_strtolower($fd))) { $isFav = true; break; }
            }

            $reason = "Tour {$t->duration_days} ngày, giá " . number_format($t->price_adult, 0, ',', '.') . "đ";
            if ($isSeason && $isFav) $reason .= " — điểm đến bạn yêu thích đang vào mùa đẹp! 🌟";
            elseif ($isSeason)       $reason .= " — lý tưởng tháng này! 🌤️";
            elseif ($isFav)          $reason .= " — phù hợp sở thích của bạn 💜";

            return [
                'tour_id'     => $t->id,
                'reason'      => $reason,
                'match_score' => min(100, (int) round($t->_score * 12)),
            ];
        })->values()->toArray();
    }

    // =========================================================
    // PRIVATE — GEMINI CALL WITH MODEL FALLBACK
    // =========================================================

    /**
     * Gọi Gemini API với vòng lặp fallback qua các model
     */
    private function callWithFallback(array $contents, float $temperature = 0.7): string
    {
        $lastException = null;

        foreach ($this->availableModels as $modelName) {
            try {
                Log::info("Calling Gemini model: {$modelName}");

                $response = Http::timeout(25)->post(
                    "{$this->baseUrl}/{$modelName}:generateContent?key={$this->apiKey}",
                    [
                        'contents'         => $contents,
                        'generationConfig' => ['temperature' => $temperature, 'maxOutputTokens' => 1024],
                        'thinkingConfig'   => ['thinkingBudget' => 0],
                        'safetySettings'   => [
                            ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_NONE'],
                            ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_NONE'],
                            ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                            ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                        ],
                    ]
                );

                if ($response->successful()) {
                    return $response->json('candidates.0.content.parts.0.text')
                        ?? 'Xin lỗi, tôi không thể trả lời lúc này. Vui lòng thử lại!';
                }

                if ($response->status() === 429) {
                    Log::warning("Model {$modelName} quá tải (429). Chuyển sang model khác...");
                    sleep(1);
                    continue;
                }

                Log::warning("Model {$modelName} lỗi ({$response->status()}). Thử model khác...");
                continue;

            } catch (\Exception $e) {
                $lastException = $e;
                Log::error("Exception at model {$modelName}: " . $e->getMessage());
                continue;
            }
        }

        throw new \Exception(
            "Toàn bộ model AI thất bại. Lỗi cuối: "
            . ($lastException ? $lastException->getMessage() : 'Hết quota hoặc timeout')
        );
    }

    // =========================================================
    // PRIVATE — BEHAVIOR TRACKING (chat)
    // =========================================================

    private function trackChatBehavior(int $userId, string $message): void
    {
        $msg   = mb_strtolower($message);
        $dests = ['đà nẵng', 'phú quốc', 'hạ long', 'đà lạt', 'hội an', 'sapa', 'nha trang', 'nhật bản', 'hàn quốc', 'côn đảo', 'ninh bình'];

        foreach ($dests as $dest) {
            if (str_contains($msg, $dest)) {
                // Cache nhanh
                $key  = "behavior:user:{$userId}";
                $data = Cache::get($key, ['viewed_tours' => [], 'destinations' => []]);
                $data['destinations'][$dest] = ($data['destinations'][$dest] ?? 0) + 1;
                Cache::put($key, $data, now()->addDays(30));

                // Lưu DB
                UserBehavior::create([
                    'user_id'     => $userId,
                    'event_type'  => UserBehavior::EVENT_CHAT_MENTION,
                    'destination' => $dest,
                ]);

                // Chỉ track 1 dest/câu
                break;
            }
        }
    }

    // =========================================================
    // PRIVATE — SMART FALLBACK CHAT
    // =========================================================

    private function smartFallback(string $message): string
    {
        $msg   = mb_strtolower($message);
        $tours = $this->getCachedTours();

        if (preg_match('/^(xin chào|chào|hello|hi|hey|alo)/u', $msg)) {
            $seasonal = $this->getSeasonalTip();
            return "Xin chào! 👋 Tôi là trợ lý AI TravelNice.\n\n"
                . "Tôi có thể giúp bạn:\n\n"
                . "• 🗺️ Tìm tour theo điểm đến & ngân sách\n\n"
                . "• 💰 So sánh chi phí tour vs tự đi\n\n"
                . "• ✈️ Gợi ý phương tiện di chuyển\n\n"
                . "• 📅 Tư vấn tour phù hợp mùa\n\n"
                . "💡 GỢI Ý THÁNG NÀY: {$seasonal}\n\n"
                . "Bạn muốn đi đâu? 😊";
        }

        if (preg_match('/(?:từ|đi từ|đi)\s+(.+?)\s+(?:đến|tới|vào|ra|sang|qua|về)\s+(.+?)(?:\s*(?:hết|giá|bao nhiêu|chi phí|tour|tư vấn|,|\?|!|$))/u', $msg, $m)) {
            return $this->buildRouteComparison(trim($m[1]), trim($m[2]), $tours);
        }

        if (preg_match('/(\d+)\s*(?:người|khách|pax|person)/u', $msg, $m)) {
            return $this->buildGroupPricing((int) $m[1], $msg, $tours);
        }

        if (preg_match('/(\d+)\s*(?:triệu|tr)/u', $msg, $m)) {
            $budget  = (int)$m[1] * 1000000;
            $matched = $tours->filter(fn($t) => $t->price_adult <= $budget)->sortByDesc('avg_rating')->take(3);
            if ($matched->isNotEmpty()) {
                $list = $matched->map(fn($t) =>
                    "• {$t->title}\n\n"
                    . "  📍 " . ($t->destination->name ?? '') . " — {$t->duration_days} ngày\n\n"
                    . "  💰 " . number_format($t->price_adult, 0, ',', '.') . "đ/người\n\n"
                )->implode('');
                return "💰 TOUR TRONG TẦM GIÁ " . number_format($budget, 0, ',', '.') . "Đ:\n\n"
                    . $list . "Bạn muốn đi bao nhiêu ngày hoặc khu vực nào ạ?";
            }
            return "Hiện chưa có tour trong tầm giá " . number_format($budget, 0, ',', '.') . "đ. 😔\n\n"
                . "Bạn thử tăng ngân sách thêm một chút, tôi sẽ tìm tour phù hợp hơn!";
        }

        if (preg_match('/giá|bao nhiêu|chi phí|budget|tiền/u', $msg)) {
            $cheap = $tours->sortBy('price_adult')->first();
            $exp   = $tours->sortByDesc('price_adult')->first();
            if ($cheap && $exp) {
                return "💰 BẢNG GIÁ TOUR TRAVELNICE:\n\n"
                    . "• Giá thấp nhất: " . number_format($cheap->price_adult, 0, ',', '.') . "đ — {$cheap->title}\n\n"
                    . "• Giá cao nhất: " . number_format($exp->price_adult, 0, ',', '.') . "đ — {$exp->title}\n\n"
                    . "📌 Nhóm 2-4 người: giảm 5%\n\n"
                    . "📌 Nhóm 5 người trở lên: giảm 10%\n\n"
                    . "Bạn có ngân sách khoảng bao nhiêu để tôi gợi ý chính xác hơn?";
            }
        }

        if (preg_match('/(\d+)\s*ngày/u', $msg, $m)) {
            $days    = (int)$m[1];
            $matched = $tours->filter(fn($t) => $t->duration_days == $days || $t->duration_days == $days - 1)
                             ->sortByDesc('avg_rating')->take(3);
            if ($matched->isNotEmpty()) {
                $list = $matched->map(fn($t) =>
                    "• {$t->title}\n\n"
                    . "  📍 " . ($t->destination->name ?? '') . " — {$t->duration_days} ngày\n\n"
                    . "  💰 " . number_format($t->price_adult, 0, ',', '.') . "đ/người\n\n"
                )->implode('');
                return "📅 TOUR {$days} NGÀY PHÙ HỢP:\n\n" . $list
                    . "Bạn muốn đi tháng mấy để tôi kiểm tra lịch khởi hành?";
            }
        }

        if (preg_match('/tháng\s*(\d+)/u', $msg, $m) || preg_match('/mùa hè|mùa đông|mùa xuân|mùa thu/u', $msg)) {
            $month = isset($m[1]) ? (int)$m[1] : (int)now()->format('n');
            $dests = $this->seasonalDestinations[$month] ?? [];
            if (!empty($dests)) {
                $destLabel = implode(', ', array_map('ucwords', array_slice($dests, 0, 4)));
                $matched   = $tours->filter(function ($t) use ($dests) {
                    $name = mb_strtolower($t->destination->name ?? '');
                    foreach ($dests as $d) {
                        if (str_contains($name, $d)) return true;
                    }
                    return false;
                })->sortByDesc('avg_rating')->take(3);

                $list = $matched->isNotEmpty()
                    ? $matched->map(fn($t) =>
                        "• {$t->title}\n\n"
                        . "  📍 " . ($t->destination->name ?? '') . " — {$t->duration_days} ngày\n\n"
                        . "  💰 " . number_format($t->price_adult, 0, ',', '.') . "đ/người\n\n"
                    )->implode('')
                    : "Tôi sẽ cập nhật tour cho các điểm này sớm!\n\n";

                return "🌤️ THÁNG {$month} NÊN ĐI ĐÂU?\n\n"
                    . "Điểm đến đẹp nhất: {$destLabel}\n\n"
                    . $list
                    . "Bạn thích điểm nào để tôi tư vấn chi tiết hơn?";
            }
        }

        $knownDests = [
            'nhật bản', 'hàn quốc', 'đà nẵng', 'phú quốc', 'hạ long',
            'châu âu', 'thái lan', 'singapore', 'trung quốc', 'nha trang',
            'sapa', 'hội an', 'đà lạt', 'ninh bình', 'mù cang chải',
            'hà giang', 'côn đảo', 'huế', 'mộc châu', 'cát bà',
        ];
        foreach ($knownDests as $dest) {
            if (str_contains($msg, $dest)) {
                $matched = $tours->filter(fn($t) =>
                    str_contains(mb_strtolower($t->destination->name ?? ''), $dest) ||
                    str_contains(mb_strtolower($t->title), $dest)
                );
                if ($matched->isNotEmpty()) {
                    $list = $matched->take(3)->map(fn($t) =>
                        "• {$t->title}\n\n"
                        . "  📍 {$t->destination->name} — {$t->duration_days} ngày\n\n"
                        . "  💰 " . number_format($t->price_adult, 0, ',', '.') . "đ/người"
                        . ($t->price_child ? " | Trẻ em: " . number_format($t->price_child, 0, ',', '.') . "đ" : "") . "\n\n"
                    )->implode('');
                    $seasonNote = $this->isGoodSeason($dest)
                        ? "✅ Đây là thời điểm đẹp để đến " . ucwords($dest) . "!\n\n"
                        : "💡 Lưu ý: Kiểm tra thời tiết trước khi đặt tour.\n\n";
                    return "🗺️ TOUR " . mb_strtoupper($dest) . " HIỆN CÓ:\n\n"
                        . $list . $seasonNote
                        . "Bạn muốn biết thêm chi tiết tour nào?";
                }
                return "Hiện chưa có tour " . ucwords($dest) . " trong hệ thống.\n\n"
                    . "Bạn thử xem các điểm đến khác hoặc để lại yêu cầu, chúng tôi sẽ cập nhật sớm! 😊";
            }
        }

        if (preg_match('/biển|bãi biển|lặn|snorkel|nghỉ dưỡng|resort/u', $msg)) {
            $beachDests = ['phú quốc', 'nha trang', 'đà nẵng', 'hạ long', 'côn đảo', 'hội an'];
            $matched    = $tours->filter(function ($t) use ($beachDests) {
                $name = mb_strtolower($t->destination->name ?? '');
                foreach ($beachDests as $d) {
                    if (str_contains($name, $d)) return true;
                }
                return false;
            })->sortByDesc('avg_rating')->take(3);
            if ($matched->isNotEmpty()) {
                $list = $matched->map(fn($t) =>
                    "• {$t->title}\n\n"
                    . "  📍 " . ($t->destination->name ?? '') . " — {$t->duration_days} ngày\n\n"
                    . "  💰 " . number_format($t->price_adult, 0, ',', '.') . "đ/người\n\n"
                )->implode('');
                return "🏖️ TOUR BIỂN & NGHỈ DƯỠNG:\n\n" . $list
                    . "Bạn muốn đi bao nhiêu ngày hoặc có ngân sách cụ thể không?";
            }
        }

        if (preg_match('/núi|trekking|leo núi|cắm trại|camping|khám phá|mạo hiểm/u', $msg)) {
            $mountainDests = ['sapa', 'hà giang', 'mù cang chải', 'đà lạt', 'mộc châu', 'ninh bình'];
            $matched       = $tours->filter(function ($t) use ($mountainDests) {
                $name = mb_strtolower($t->destination->name ?? '');
                foreach ($mountainDests as $d) {
                    if (str_contains($name, $d)) return true;
                }
                return false;
            })->sortByDesc('avg_rating')->take(3);
            if ($matched->isNotEmpty()) {
                $list = $matched->map(fn($t) =>
                    "• {$t->title}\n\n"
                    . "  📍 " . ($t->destination->name ?? '') . " — {$t->duration_days} ngày\n\n"
                    . "  💰 " . number_format($t->price_adult, 0, ',', '.') . "đ/người\n\n"
                )->implode('');
                return "🏔️ TOUR THIÊN NHIÊN & KHÁM PHÁ:\n\n" . $list
                    . "Bạn muốn đi mấy ngày và xuất phát từ đâu?";
            }
        }

        if (preg_match('/gia đình|trẻ em|family|con nhỏ|bé/u', $msg)) {
            $family = $tours->filter(fn($t) => !empty($t->price_child))->sortByDesc('avg_rating');
            if ($family->isNotEmpty()) {
                $list = $family->take(3)->map(fn($t) =>
                    "• {$t->title} — {$t->duration_days} ngày\n\n"
                    . "  👤 Người lớn: " . number_format($t->price_adult, 0, ',', '.') . "đ\n\n"
                    . "  👧 Trẻ em: " . number_format($t->price_child, 0, ',', '.') . "đ\n\n"
                )->implode('');
                return "👨‍👩‍👧 TOUR GIA ĐÌNH TRAVELNICE:\n\n"
                    . $list
                    . "✅ Giá riêng cho trẻ em, lịch trình nhẹ nhàng\n\n"
                    . "Bé nhà bạn bao nhiêu tuổi? Tôi sẽ tư vấn phù hợp hơn!";
            }
        }

        if (preg_match('/hủy|hoàn tiền|đổi lịch|cancel|refund|chính sách/u', $msg)) {
            return "📋 CHÍNH SÁCH HỦY TOUR TRAVELNICE:\n\n"
                . "• Hủy trước 15 ngày: hoàn 90% tiền\n\n"
                . "• Hủy trước 7 ngày: hoàn 70% tiền\n\n"
                . "• Hủy trước 3 ngày: hoàn 50% tiền\n\n"
                . "• Hủy trong vòng 24 giờ: không hoàn tiền\n\n"
                . "🔄 Đổi lịch: liên hệ trước 48 tiếng — miễn phí!\n\n"
                . "Bạn cần hỗ trợ hủy hoặc đổi tour nào ạ?";
        }

        if (preg_match('/đặt|booking|mua|thanh toán|payment/u', $msg)) {
            return "📋 CÁCH ĐẶT TOUR TRAVELNICE:\n\n"
                . "1️⃣ Chọn tour → bấm Đặt ngay\n\n"
                . "2️⃣ Điền thông tin & chọn ngày khởi hành\n\n"
                . "3️⃣ Chọn thanh toán: 💳 VNPay — ATM/QR/Visa/Master | 📱 MoMo\n\n"
                . "4️⃣ Nhận xác nhận qua email\n\n"
                . "⚡ Xác nhận trong vòng 30 phút!\n\n"
                . "Cần hỗ trợ thêm không? 😊";
        }

        $count = $tours->count();
        return "Tôi có thể giúp bạn tìm tour phù hợp! 😊 TravelNice hiện có {$count} tour.\n\n"
            . "• 🗺️ \"Tour Đà Nẵng có gì?\" — tìm theo điểm đến\n\n"
            . "• ✈️ \"Từ Hà Nội đến Sài Gòn hết bao nhiêu?\" — so sánh chi phí\n\n"
            . "• 👨‍👩‍👧 \"Tour gia đình 3 ngày\" — tour phù hợp gia đình\n\n"
            . "• 💰 \"Tour dưới 5 triệu\" — lọc theo ngân sách\n\n"
            . "• 📅 \"Tháng 7 đi đâu đẹp?\" — gợi ý theo mùa\n\n"
            . "Hoặc vào Gợi ý AI để tìm tour phù hợp nhất! 🎯";
    }

    // =========================================================
    // PRIVATE — ROUTE COMPARISON & GROUP PRICING
    // =========================================================

    private function buildRouteComparison(string $from, string $to, $tours): string
    {
        $fromNorm = $this->normalizePlace($from);
        $toNorm   = $this->normalizePlace($to);

        $relatedTours = $tours->filter(fn($t) =>
            str_contains(mb_strtolower($t->title), $toNorm) ||
            str_contains(mb_strtolower($t->destination->name ?? ''), $toNorm)
        )->take(3);

        $transportKey  = "{$fromNorm}-{$toNorm}";
        $transportKeyR = "{$toNorm}-{$fromNorm}";
        $transport = $this->transportEstimates[$transportKey]
            ?? $this->transportEstimates[$transportKeyR]
            ?? null;

        $response = "🗺️ Hành trình " . ucwords($from) . " → " . ucwords($to) . "\n\n";

        if ($relatedTours->isNotEmpty()) {
            $response .= "📦 Tour trọn gói TravelNice:\n";
            foreach ($relatedTours as $t) {
                $price1 = $t->price_adult;
                $price2 = round($price1 * 0.95);
                $price3 = round($price1 * 0.90);
                $response .= "• {$t->title} — {$t->duration_days} ngày\n\n"
                    . "  👤 1 người: " . number_format($price1, 0, ',', '.') . "đ\n\n"
                    . "  👥 Nhóm 2-4: " . number_format($price2, 0, ',', '.') . "đ/người (-5%)\n\n"
                    . "  👨‍👩‍👧 Nhóm 5+: " . number_format($price3, 0, ',', '.') . "đ/người (-10%)\n\n";
            }
        } else {
            $response .= "📦 Tour trọn gói: Chưa có tuyến trực tiếp — liên hệ để được tư vấn tour ghép!\n\n";
        }

        $response .= "🚗 Di chuyển tự túc (ước tính/người):\n";
        if ($transport) {
            if (!empty($transport['fly']))   $response .= "✈️ Máy bay: " . number_format($transport['fly'][0], 0, ',', '.') . "đ – " . number_format($transport['fly'][1], 0, ',', '.') . "đ\n";
            if (!empty($transport['train'])) $response .= "🚂 Tàu hỏa: " . number_format($transport['train'][0], 0, ',', '.') . "đ – " . number_format($transport['train'][1], 0, ',', '.') . "đ\n";
            if (!empty($transport['bus']))   $response .= "🚌 Xe khách: " . number_format($transport['bus'][0], 0, ',', '.') . "đ – " . number_format($transport['bus'][1], 0, ',', '.') . "đ\n";
            if (!empty($transport['car']))   $response .= "🚗 Xe thuê: " . number_format($transport['car'][0], 0, ',', '.') . "đ – " . number_format($transport['car'][1], 0, ',', '.') . "đ\n";
            if (!empty($transport['ferry'])) $response .= "⛴️ Phà: " . number_format($transport['ferry'][0], 0, ',', '.') . "đ – " . number_format($transport['ferry'][1], 0, ',', '.') . "đ\n";
            $response .= "(Chưa bao gồm khách sạn, ăn uống, vé tham quan)\n\n";
        } else {
            $response .= "(Thông tin phương tiện đang được cập nhật cho tuyến này)\n\n";
        }

        $response .= "⚖️ TOUR TRỌN GÓI hay TỰ ĐI?\n\n"
            . "✅ Chọn TOUR: nhàn, có HDV, không lo xe cộ khách sạn\n\n"
            . "🎒 Chọn TỰ ĐI: linh hoạt, tiết kiệm, tự lên lịch\n\n"
            . "Bạn muốn đặt tour hay cần tư vấn thêm?";

        return $response;
    }

    private function buildGroupPricing(int $groupSize, string $msg, $tours): string
    {
        $discount = match(true) {
            $groupSize >= 10 => 0.15,
            $groupSize >= 5  => 0.10,
            $groupSize >= 2  => 0.05,
            default          => 0,
        };

        $dest     = null;
        $destList = ['đà nẵng', 'phú quốc', 'hạ long', 'đà lạt', 'hội an', 'sapa', 'nha trang'];
        foreach ($destList as $d) {
            if (str_contains($msg, $d)) { $dest = $d; break; }
        }

        $query    = $tours;
        if ($dest) {
            $query = $tours->filter(fn($t) =>
                str_contains(mb_strtolower($t->destination->name ?? ''), $dest) ||
                str_contains(mb_strtolower($t->title), $dest)
            );
        }
        $selected = $query->take(3);
        if ($selected->isEmpty()) {
            $selected = $tours->sortByDesc('avg_rating')->take(3);
        }

        $discountLabel = $discount > 0 ? " (giảm " . ($discount * 100) . "% cho nhóm)" : "";
        $response = "👥 Giá tour cho nhóm {$groupSize} người{$discountLabel}:\n\n";

        foreach ($selected as $t) {
            $priceGroup = round($t->price_adult * (1 - $discount));
            $totalGroup = $priceGroup * $groupSize;
            $response  .= "• {$t->title} ({$t->duration_days} ngày)\n"
                . "  💰 Giá/người: " . number_format($priceGroup, 0, ',', '.') . "đ"
                . ($discount > 0 ? " (gốc " . number_format($t->price_adult, 0, ',', '.') . "đ)" : "") . "\n"
                . "  💵 Tổng nhóm: " . number_format($totalGroup, 0, ',', '.') . "đ\n\n";
        }

        if ($groupSize >= 10) {
            $response .= "🎁 Nhóm từ 10 người — liên hệ hotline để được báo giá đặc biệt!\n";
        }

        return $response;
    }

    // =========================================================
    // PRIVATE — CONTEXT BUILDERS
    // =========================================================

    private function buildToursContext(): string
    {
        return Cache::remember('gemini_tours_context', 300, function () {
            $tours = Tour::with(['destination', 'category'])
                ->where('status', 'active')
                ->orderByDesc('avg_rating')
                ->take(25)
                ->get();

            if ($tours->isEmpty()) return 'Chưa có tour.';

            return $tours->map(fn($t) =>
                "ID:{$t->id}|{$t->title}|{$t->destination->name}|{$t->duration_days}N"
                . "|" . number_format($t->price_adult, 0, ',', '.') . "đ"
                . ($t->price_child ? "|Trẻ:" . number_format($t->price_child, 0, ',', '.') . "đ" : "")
                . "|Rating:" . ($t->avg_rating ?? 'N/A')
                . "|Cat:" . ($t->category->name ?? '')
            )->implode("\n");
        });
    }

    private function buildSeasonContext(): string
    {
        $month = (int) now()->format('n');
        $dests = $this->seasonalDestinations[$month] ?? [];
        return "Tháng " . $month . ": " . implode(', ', $dests);
    }

    private function buildTransportNote(string $message): string
    {
        $msg = mb_strtolower($message);
        if (!preg_match('/(?:từ|đi)\s+(.+?)\s+(?:đến|tới|vào|ra|sang|qua|về)/u', $msg)) {
            return '';
        }
        return "\nLưu ý: Khi trả lời câu hỏi về hành trình, cung cấp giá tour 1 người, nhóm, và ước tính phương tiện nếu tự đi.\n";
    }

    private function getCachedTours()
    {
        return Cache::remember('tours_for_chatbot', 300, fn() =>
            Tour::with(['destination', 'category'])
                ->where('status', 'active')
                ->orderByDesc('avg_rating')
                ->take(30)
                ->get()
        );
    }

    // =========================================================
    // PRIVATE — SEASONAL HELPERS
    // =========================================================

    private function getSeasonalDestinations(): array
    {
        return $this->seasonalDestinations[(int) now()->format('n')] ?? [];
    }

    private function getSeasonalTip(): string
    {
        $dests = $this->getSeasonalDestinations();
        if (empty($dests)) return "Nhiều điểm đến hấp dẫn đang chờ bạn!";
        return ucwords(implode(', ', array_slice($dests, 0, 3))) . " đang vào mùa đẹp nhất!";
    }

    private function isGoodSeason(string $dest): bool
    {
        foreach ($this->getSeasonalDestinations() as $d) {
            if (str_contains($dest, $d) || str_contains($d, $dest)) return true;
        }
        return false;
    }

    private function getViMonthName(): string
    {
        return now()->format('n') . '/' . now()->format('Y');
    }

    private function normalizePlace(string $place): string
    {
        $map = [
            'sài gòn' => 'hồ chí minh', 'saigon' => 'hồ chí minh',
            'tp hcm' => 'hồ chí minh', 'tp.hcm' => 'hồ chí minh',
            'hcm' => 'hồ chí minh', 'tphcm' => 'hồ chí minh',
            'hà nội' => 'hà nội', 'hanoi' => 'hà nội', 'hn' => 'hà nội',
            'đà nẵng' => 'đà nẵng', 'da nang' => 'đà nẵng', 'dn' => 'đà nẵng',
            'phú quốc' => 'phú quốc', 'phu quoc' => 'phú quốc',
            'đà lạt' => 'đà lạt', 'dalat' => 'đà lạt',
            'nha trang' => 'nha trang',
            'hạ long' => 'hạ long', 'ha long' => 'hạ long',
        ];
        $p = mb_strtolower(trim($place));
        return $map[$p] ?? $p;
    }
}