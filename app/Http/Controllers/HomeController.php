<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourCategory;

class HomeController extends Controller
{
    public function index()
    {
        $featuredTours = Tour::with(['destination', 'reviews'])
            ->where('status', 'active')
            ->orderByDesc('avg_rating')
            ->take(8)
            ->get();

        $categories = TourCategory::orderBy('name')->get();

        if (auth()->check()) {
        auth()->user()->load('wishlistedTours');
        }

        return view('home', compact('featuredTours', 'categories'));
    }
}