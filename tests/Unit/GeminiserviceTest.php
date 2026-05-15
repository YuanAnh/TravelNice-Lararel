<?php

namespace Tests\Unit;

use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeminiServiceTest extends TestCase
{
    use RefreshDatabase;

    private GeminiService $service;

    protected function setUp(): void
    {
        parent::setUp();
        // Không set API key → chạy fallback
        config(['services.gemini.key' => '']);
        $this->service = new GeminiService();
    }

    public function test_fallback_chat_returns_string(): void
    {
        $reply = $this->service->chat([], 'xin chào');
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    public function test_fallback_chat_greeting_response(): void
    {
        $reply = $this->service->chat([], 'xin chào');
        $this->assertStringContainsString('TravelNice', $reply);
    }

    public function test_fallback_recommend_returns_array(): void
    {
        $result = $this->service->recommendTours([
            'budget'   => '5-15 triệu',
            'duration' => '4-6',
        ]);
        $this->assertIsArray($result);
    }

    public function test_fallback_chat_handles_price_question(): void
    {
        $reply = $this->service->chat([], 'giá tour bao nhiêu');
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    public function test_fallback_chat_handles_booking_question(): void
    {
        $reply = $this->service->chat([], 'cách đặt tour như thế nào');
        $this->assertStringContainsString('Đặt ngay', $reply);
    }
}