<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\Destination;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $query = Tour::with(['destination', 'category', 'slots' => function($q){$q-> where('status','open')-> where('departure_date', '>=', now())->orderBy('departure_date');}])
            ->where('status', 'active');

        if ($q = $request->q) {
            $query->where(function ($q2) use ($q) {
                $q2->where('title', 'like', "%$q%")
                   ->orWhereHas('destination', fn($d) => $d->where('name', 'like', "%$q%"));
            });
        }

        if ($cats = $request->category) {
            $query->whereIn('category_id', (array) $cats);
        }

        if ($dests = $request->destination) {
            $query->whereIn('destination_id', (array) $dests);
        }

        if ($durations = $request->duration) {
            $query->where(function ($q2) use ($durations) {
                foreach ((array) $durations as $d) {
                    [$min, $max] = match($d) {
                        '1-3'  => [1, 3],
                        '4-6'  => [4, 6],
                        '7-10' => [7, 10],
                        '11+'  => [11, 999],
                        default => [1, 999],
                    };
                    $q2->orWhereBetween('duration_days', [$min, $max]);
                }
            });
        }

        if ($request->price_min) {
            $query->where('price_adult', '>=', $request->price_min);
        }
        if ($request->price_max && $request->price_max < 100000000) {
            $query->where('price_adult', '<=', $request->price_max);
        }

        match($request->sort ?? 'popular') {
            'price_asc'  => $query->orderBy('price_adult'),
            'price_desc' => $query->orderByDesc('price_adult'),
            'newest'     => $query->latest(),
            'rating'     => $query->orderByDesc('avg_rating'),
            default      => $query->orderByDesc('avg_rating'),
        };

        $cacheKey = 'tours_page_' . md5(serialize($request->all()));

        $tours        = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($query) {
            return $query->paginate(10);
        });
        $categories   = Cache::remember('categorise_with_counts', now()->addDay(), function () {
            return TourCategory::withCount('tours')->get();
        });
        $destinations = Cache::remember('all_destinations', now()->addDay(), function () {
            return Destination::orderBy('name')->get();
        });

        if (auth()->check()) {
            auth()->user()->load('wishlistedTours');
        }

        return view('tours.index', compact('tours', 'categories', 'destinations'));
    }

    public function show(Tour $tour, GeminiService $gemini)
    {
        $tour->load(['destination', 'category', 'images', 'schedules', 'slots', 'reviews.user']);

        if (auth()->check()) {
            auth()->user()->load('wishlistedTours');
            // Track hành vi xem tour để cải thiện gợi ý AI
            $gemini->trackTourView(
                auth()->id(),
                $tour->id,
                $tour->destination->name ?? ''
            );
        }

        return view('tours.show', compact('tour'));
    }
}