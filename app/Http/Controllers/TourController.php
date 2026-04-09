<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\Destination;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $query = Tour::with(['destination', 'category', 'slots'])
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

        $tours        = $query->paginate(10);
        $categories   = TourCategory::withCount('tours')->get();
        $destinations = Destination::orderBy('name')->get();

        return view('tours.index', compact('tours', 'categories', 'destinations'));
    }

    public function show(Tour $tour)
    {
        $tour->load(['destination', 'category', 'images', 'schedules', 'slots', 'reviews.user']);
        return view('tours.show', compact('tour'));
    }
}