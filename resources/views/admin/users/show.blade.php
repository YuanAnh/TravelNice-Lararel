@extends('admin.layouts.admin')

@section('title', 'Chi tiết người dùng')
@section('page-title', 'Chi tiết người dùng')
@section('breadcrumb', 'Admin / Người dùng / Chi tiết')

@section('content')

<div class="row g-4">
    <div class="col-lg-4">
        <div class="admin-form-card text-center">
            <div style="width:72px;height:72px;border-radius:50%;background:#EEF5FF;color:#0066CC;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;margin:0 auto 12px">
                {{ strtoupper(substr($user->name,0,1)) }}
            </div>
            <div style="font-size:16px;font-weight:700">{{ $user->name }}</div>
            <div style="font-size:13px;color:#9CA3AF">{{ $user->email }}</div>
            <div class="mt-2">
                @if($user->hasRole('admin'))
                <span style="background:#EEF5FF;color:#0066CC;font-size:12px;padding:3px 10px;border-radius:10px;font-weight:600">Admin</span>
                @else
                <span style="background:#F3F4F6;color:#6B7280;font-size:12px;padding:3px 10px;border-radius:10px;font-weight:600">User</span>
                @endif
                @if($user->status === 'active')
                <span class="badge-active ms-1">Hoạt động</span>
                @else
                <span class="badge-inactive ms-1">Bị khoá</span>
                @endif
            </div>
            <hr>
            <div class="row g-2 text-center">
                <div class="col-4">
                    <div style="font-size:20px;font-weight:800;color:#0066CC">{{ $user->bookings->count() }}</div>
                    <div style="font-size:11px;color:#9CA3AF">Đơn đặt</div>
                </div>
                <div class="col-4">
                    <div style="font-size:20px;font-weight:800;color:#16A34A">{{ $user->bookings->where('status','completed')->count() }}</div>
                    <div style="font-size:11px;color:#9CA3AF">Hoàn thành</div>
                </div>
                <div class="col-4">
                    <div style="font-size:20px;font-weight:800;color:#D97706">{{ $user->reviews->count() }}</div>
                    <div style="font-size:11px;color:#9CA3AF">Đánh giá</div>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm flex-fill rounded-pill">
                    <i class="bi bi-pencil me-1"></i>Sửa
                </a>
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="flex-fill">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm w-100 rounded-pill {{ $user->status==='active' ? 'btn-warning':'btn-success' }}">
                        <i class="bi bi-{{ $user->status==='active' ? 'lock':'unlock' }} me-1"></i>
                        {{ $user->status==='active' ? 'Khoá':'Mở khoá' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="admin-form-card">
            <h6>Thông tin cá nhân</h6>
            <div class="mb-2 d-flex gap-2" style="font-size:13px"><i class="bi bi-telephone text-primary"></i>{{ $user->phone ?? 'Chưa cập nhật' }}</div>
            <div class="mb-2 d-flex gap-2" style="font-size:13px"><i class="bi bi-geo-alt text-primary"></i>{{ $user->address ?? 'Chưa cập nhật' }}</div>
            <div class="mb-2 d-flex gap-2" style="font-size:13px"><i class="bi bi-calendar text-primary"></i>Tham gia {{ $user->created_at->format('d/m/Y') }}</div>
            <div class="d-flex gap-2" style="font-size:13px">
                <i class="bi bi-check-circle text-primary"></i>
                Email {{ $user->email_verified_at ? 'đã xác thực' : 'chưa xác thực' }}
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="admin-form-card">
            <h6>Lịch sử đặt tour</h6>
            @forelse($user->bookings()->with('tourSlot.tour')->latest()->get() as $b)
            <div class="d-flex align-items-center justify-content-between p-3 mb-2 rounded-3" style="background:#F9FAFB;font-size:13px">
                <div>
                    <div class="fw-600">#{{ $b->booking_code }} — {{ Str::limit($b->tourSlot->tour->title ?? '—', 35) }}</div>
                    <div style="font-size:12px;color:#9CA3AF">{{ $b->created_at->format('d/m/Y H:i') }} · {{ $b->num_adults }} người lớn</div>
                </div>
                <div class="text-end">
                    <div class="fw-700" style="color:#FF6B00">{{ number_format($b->total_price,0,',','.') }}đ</div>
                    @php $sc=['pending'=>['#FEF3C7','#92400E'],'paid'=>['#DBEAFE','#1E40AF'],'confirmed'=>['#D1FAE5','#065F46'],'cancelled'=>['#FEE2E2','#991B1B'],'completed'=>['#E0E7FF','#3730A3']]; $c=$sc[$b->status]??['#F3F4F6','#6B7280']; @endphp
                    <span style="background:{{ $c[0] }};color:{{ $c[1] }};font-size:11px;padding:2px 8px;border-radius:10px;font-weight:600">{{ ucfirst($b->status) }}</span>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-3" style="font-size:13px">Chưa có đơn đặt nào</div>
            @endforelse
        </div>
    </div>
</div>

@endsection