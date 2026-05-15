<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'image'      => 'required|image|max:2048',
            'link_url'       => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $path = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title'      => $request->title,
            'image_path' => $path,
            'link_url'       => $request->link_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => $request->has('is_active'),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm banner thành công!');
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'image'      => 'nullable|image|max:2048',
            'link_url'       => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'title'      => $request->title,
            'link_url'       => $request->link_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image_path);
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);
            AdminLog::log('update', "Cập nhật banner: {$banner->title}", $banner->id, 'Banner');
        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật banner thành công!');
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->image_path);
        AdminLog::log('delete', "Xoá banner: {$banner->title}", $banner->id, 'Banner');
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Đã xoá banner!');
    }

    public function toggleActive(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        return redirect()->back()->with('success', $banner->is_active ? 'Đã bật banner!' : 'Đã tắt banner!');
    }
}