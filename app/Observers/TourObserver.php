<?php
namespace App\Observers;
 
use App\Models\Tour;
use App\Services\GeminiService;
 
class TourObserver
{
    public function __construct(private GeminiService $gemini) {}
 
    public function saved(Tour $tour): void
    {
        $this->gemini->clearToursCache();
    }
 
    public function deleted(Tour $tour): void
    {
        $this->gemini->clearToursCache();
    }
};
