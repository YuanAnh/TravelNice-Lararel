<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\Destination;
use App\Models\TourCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $query = Tour::with(['destination', 'category'])->latest();

        if ($q = $request->q) {
            $query->where('title', 'like', "%$q%");
        }
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $tours = $query->paginate(15);
        return view('admin.tours.index', compact('tours'));
    }

    public function create()
    {
        $destinations = Destination::orderBy('name')->get();
        $categories   = TourCategory::orderBy('name')->get();
        return view('admin.tours.create', compact('destinations', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:200',
            'description'   => 'nullable|string',
            'duration_days' => 'required|integer|min:1',
            'price_adult'   => 'required|numeric|min:0',
            'price_child'   => 'nullable|numeric|min:0',
            'max_slots'     => 'required|integer|min:1',
            'status'        => 'required|in:active,inactive,draft',
            'thumbnail'     => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['thumbnail', 'schedules', '_token']);
        $data['slug'] = $this->uniqueSlug($request->slug ?: Str::slug($request->title));

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('tours', 'public');
        }

        $tour = Tour::create($data);
        $this->saveSchedules($tour, $request->schedules ?? []);

        return redirect()->route('admin.tours.index')->with('success', 'Tạo tour thành công!');
    }

    public function edit(Tour $tour)
    {
        $tour->load('schedules');
        $destinations = Destination::orderBy('name')->get();
        $categories   = TourCategory::orderBy('name')->get();
        return view('admin.tours.edit', compact('tour', 'destinations', 'categories'));
    }

    public function update(Request $request, Tour $tour)
    {
        $request->validate([
            'title'         => 'required|string|max:200',
            'duration_days' => 'required|integer|min:1',
            'price_adult'   => 'required|numeric|min:0',
            'price_child'   => 'nullable|numeric|min:0',
            'max_slots'     => 'required|integer|min:1',
            'status'        => 'required|in:active,inactive,draft',
            'thumbnail'     => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['thumbnail', 'schedules', '_token', '_method']);
        $data['slug'] = $this->uniqueSlug($request->slug ?: Str::slug($request->title), $tour->id);

        if ($request->hasFile('thumbnail')) {
            if ($tour->thumbnail) Storage::disk('public')->delete($tour->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('tours', 'public');
        }

        $tour->update($data);
        $tour->schedules()->delete();
        $this->saveSchedules($tour, $request->schedules ?? []);

        return redirect()->route('admin.tours.index')->with('success', 'Cập nhật tour thành công!');
    }

    public function destroy(Tour $tour)
    {
        if ($tour->thumbnail) Storage::disk('public')->delete($tour->thumbnail);
        $tour->delete();
        return redirect()->route('admin.tours.index')->with('success', 'Đã xoá tour!');
    }

    private function saveSchedules(Tour $tour, array $schedules): void
    {
        foreach ($schedules as $i => $s) {
            if (empty($s['title']) && empty($s['description'])) continue;
            TourSchedule::create([
                'tour_id'     => $tour->id,
                'day_number'  => $s['day_number'] ?? ($i + 1),
                'title'       => $s['title'] ?? '',
                'description' => $s['description'] ?? '',
            ]);
        }
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $original = $slug;
        $i = 1;
        while (Tour::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }
}