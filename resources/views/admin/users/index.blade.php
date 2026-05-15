@extends('admin.layouts.admin')

@section('title', 'Quản lý người dùng')
@section('page-title', 'Quản lý người dùng')
@section('breadcrumb', 'Admin / Người dùng')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div style="font-size:13px;color:#6B7280">Tổng cộng <strong>{{ $users->total() }}</strong> người dùng</div>
</div>

<div class="admin-table-wrap">
    <div class="admin-table-header">
        <div class="admin-table-title"><i class="bi bi-people me-2 text-success"></i>Danh sách người dùng</div>

        <form method="GET" class="search-wrap ms-auto">
            <i class="bi bi-search"></i>
            <input type="text" name="q" class="admin-search" placeholder="Tìm tên, email..." value="{{ request('q') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="role" value="{{ request('role') }}">
        </form>

        <select class="form-select form-select-sm rounded-pill" style="width:130px;font-size:13px" onchange="window.location=this.value">
            <option value="{{ route('admin.users.index') }}" {{ !request('status') ? 'selected':'' }}>Tất cả</option>
            <option value="{{ route('admin.users.index',['status'=>'active']) }}" {{ request('status')==='active' ? 'selected':'' }}>Hoạt động</option>
            <option value="{{ route('admin.users.index',['status'=>'banned']) }}" {{ request('status')==='banned' ? 'selected':'' }}>Bị khoá</option>
        </select>

        <select class="form-select form-select-sm rounded-pill" style="width:120px;font-size:13px" onchange="window.location=this.value">
            <option value="{{ route('admin.users.index') }}" {{ !request('role') ? 'selected':'' }}>Tất cả role</option>
            <option value="{{ route('admin.users.index',['role'=>'admin']) }}" {{ request('role')==='admin' ? 'selected':'' }}>Admin</option>
            <option value="{{ route('admin.users.index',['role'=>'user']) }}" {{ request('role')==='user' ? 'selected':'' }}>User</option>
        </select>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Người dùng</th>
                    <th>Điện thoại</th>
                    <th>Role</th>
                    <th>Trạng thái</th>
                    <th>Đơn đặt</th>
                    <th>Ngày tạo</th>
                    <th style="width:110px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="color:#9CA3AF">{{ $user->id }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:36px;height:36px;border-radius:50%;background:#EEF5FF;color:#0066CC;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:600">{{ $user->name }}</div>
                                <div style="font-size:12px;color:#9CA3AF">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td>
                        @if($user->hasRole('admin'))
                        <span style="background:#EEF5FF;color:#0066CC;font-size:11px;padding:3px 8px;border-radius:10px;font-weight:600"><i class="bi bi-shield-check me-1"></i>Admin</span>
                        @else
                        <span style="background:#F3F4F6;color:#6B7280;font-size:11px;padding:3px 8px;border-radius:10px;font-weight:600"><i class="bi bi-person me-1"></i>User</span>
                        @endif
                    </td>
                    <td>
                        @if($user->status === 'active')
                        <span class="badge-active">Hoạt động</span>
                        @else
                        <span class="badge-inactive">Bị khoá</span>
                        @endif
                    </td>
                    <td>{{ $user->bookings_count }}</td>
                    <td style="color:#9CA3AF;font-size:12px">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn-action btn-action-view" title="Xem chi tiết">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn-action btn-action-edit" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($user->status === 'active')
                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-action" style="background:#FEF3C7;color:#D97706" title="Khoá tài khoản">
                                    <i class="bi bi-lock"></i>
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-action" style="background:#D1FAE5;color:#16A34A" title="Mở khoá">
                                    <i class="bi bi-unlock"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-people" style="font-size:36px;display:block;margin-bottom:8px"></i>
                        Không tìm thấy người dùng nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="p-3 border-top">
        {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection