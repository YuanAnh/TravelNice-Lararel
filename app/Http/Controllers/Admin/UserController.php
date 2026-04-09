<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('bookings')->latest();

        if ($q = $request->q) {
            $query->where(fn($q2) => $q2->where('name','like',"%$q%")->orWhere('email','like',"%$q%"));
        }
        if ($status = $request->status) {
            $query->where('status', $status);
        }
        if ($role = $request->role) {
            $query->role($role);
        }

        $users = $query->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['bookings.tourSlot.tour', 'reviews']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'   => 'required|string|max:100',
            'phone'  => 'nullable|string|max:20',
            'status' => 'required|in:active,banned',
        ]);

        $user->update($request->only('name', 'phone', 'address', 'status'));

        // Đổi role nếu cần
        if ($request->role && in_array($request->role, ['admin','user'])) {
            $user->syncRoles([$request->role]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Không thể xoá tài khoản đang đăng nhập!');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Đã xoá người dùng!');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => $user->status === 'active' ? 'banned' : 'active']);
        $msg = $user->status === 'active' ? 'Đã mở khoá tài khoản!' : 'Đã khoá tài khoản!';
        return redirect()->back()->with('success', $msg);
    }
}