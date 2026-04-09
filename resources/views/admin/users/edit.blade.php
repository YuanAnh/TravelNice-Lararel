@extends('admin.layouts.admin')

@section('title', 'Sửa người dùng')
@section('page-title', 'Sửa người dùng')
@section('breadcrumb', 'Admin / Người dùng / Sửa')

@section('content')

<form method="POST" action="{{ route('admin.users.update', $user) }}" style="max-width:600px">
    @csrf @method('PUT')

    @if($errors->any())
    <div class="alert alert-danger mb-4" style="border-radius:10px;font-size:13px">{{ $errors->first() }}</div>
    @endif

    <div class="admin-form-card">
        <h6>Thông tin cơ bản</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Số điện thoại</label>
                <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="active" {{ $user->status==='active' ? 'selected':'' }}>✅ Hoạt động</option>
                    <option value="banned" {{ $user->status==='banned' ? 'selected':'' }}>🚫 Bị khoá</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="user"  {{ $user->hasRole('user')  ? 'selected':'' }}>User</option>
                    <option value="admin" {{ $user->hasRole('admin') ? 'selected':'' }}>Admin</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Địa chỉ</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4 rounded-pill">
            <i class="bi bi-check2 me-1"></i> Lưu thay đổi
        </button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-pill">Huỷ</a>
    </div>
</form>

@endsection