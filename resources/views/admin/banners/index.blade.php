@extends('admin.layouts.admin')
@section('title', 'Quản lý Banner')
@section('page-title', 'Quản lý Banner')
@section('breadcrumb', 'Admin / Banner')

@push('styles')
<style>
.banner-card { background:#fff; border:1px solid var(--admin-border); border-radius:10px; overflow:hidden; }
.banner-img { width:100%; height:120px; object-fit:cover; }
.banner-img-placeholder { width:100%; height:120px; background:#F3F4F6; display:flex; align-items:center; justify-content:center; color:#9CA3AF; font-size:32px; }
</style>
@endpush

@section('content')
<div class="row g-4">

    {{-- Form thêm banner --}}
    <div class="col-lg-4">
        <div class="admin-form-card">
            <h6><i class="bi bi-plus-circle me-2"></i>Thêm Banner mới</h6>
            <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
                @csrf
                @if($errors->any())
                <div class="alert alert-danger mb-3" style="font-size:12px;border-radius:8px">{{ $errors->first() }}</div>
                @endif
                <div class="mb-3">
                    <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="VD: Banner khuyến mại hè" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ảnh banner <span class="text-danger">*</span></label>
                    <input type="file" name="image" class="form-control" accept="image/*"
                           onchange="previewImg(this,'addPreview')" required>
                    <img id="addPreview" src="" style="display:none;width:100%;height:80px;object-fit:cover;border-radius:8px;margin-top:8px">
                    <div class="form-text">JPG, PNG. Tỉ lệ 16:5 khuyến nghị (1200×375px)</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Link khi click</label>
                    <input type="url" name="link" class="form-control" placeholder="https://...">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Thứ tự</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-6 d-flex align-items-end pb-1">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="isActive" class="form-check-input" checked>
                            <label for="isActive" class="form-check-label" style="font-size:13px">Hiển thị</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    <i class="bi bi-plus me-1"></i> Thêm Banner
                </button>
            </form>
        </div>
    </div>

    {{-- Danh sách banner --}}
    <div class="col-lg-8">
        <div class="admin-form-card">
            <h6><i class="bi bi-images me-2"></i>Danh sách Banner ({{ $banners->count() }})</h6>

            @if($banners->isEmpty())
            <div class="text-center py-4 text-muted">
                <i class="bi bi-image" style="font-size:36px;display:block;margin-bottom:8px"></i>
                Chưa có banner nào
            </div>
            @else
            <div class="row g-3">
                @foreach($banners as $banner)
                <div class="col-md-6">
                    <div class="banner-card">
                        @if($banner->image_path)
                        <img src="{{ asset('storage/'.$banner->image_path) }}" class="banner-img" alt="{{ $banner->title }}">
                        @else
                        <div class="banner-img-placeholder"><i class="bi bi-image"></i></div>
                        @endif

                        <div class="p-3">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <div>
                                    <div style="font-size:13px;font-weight:700">{{ $banner->title }}</div>
                                    @if($banner->link)
                                    <div style="font-size:11px;color:#9CA3AF">{{ Str::limit($banner->link, 30) }}</div>
                                    @endif
                                </div>
                                <span style="font-size:11px;background:{{ $banner->is_active ? '#D1FAE5' : '#FEE2E2' }};color:{{ $banner->is_active ? '#065F46' : '#991B1B' }};padding:2px 8px;border-radius:10px;font-weight:600;white-space:nowrap">
                                    {{ $banner->is_active ? 'Hiển thị' : 'Ẩn' }}
                                </span>
                            </div>

                            <div class="d-flex gap-1 flex-wrap">
                                {{-- Toggle --}}
                                <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm rounded-pill {{ $banner->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="font-size:12px">
                                        <i class="bi bi-{{ $banner->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        {{ $banner->is_active ? 'Ẩn' : 'Hiện' }}
                                    </button>
                                </form>

                                {{-- Edit button --}}
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" style="font-size:12px"
                                        data-bs-toggle="modal" data-bs-target="#editModal{{ $banner->id }}">
                                    <i class="bi bi-pencil"></i> Sửa
                                </button>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}"
                                      onsubmit="return confirm('Xoá banner này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" style="font-size:12px">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Edit Modal --}}
                    <div class="modal fade" id="editModal{{ $banner->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius:12px">
                                <div class="modal-header">
                                    <h5 class="modal-title" style="font-size:15px">Sửa Banner</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Tiêu đề</label>
                                            <input type="text" name="title" class="form-control" value="{{ $banner->title }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Ảnh mới (để trống nếu không đổi)</label>
                                            <input type="file" name="image" class="form-control" accept="image/*">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Link</label>
                                            <input type="url" name="link" class="form-control" value="{{ $banner->link }}">
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label">Thứ tự</label>
                                                <input type="number" name="sort_order" class="form-control" value="{{ $banner->sort_order }}">
                                            </div>
                                            <div class="col-6 d-flex align-items-end pb-1">
                                                <div class="form-check">
                                                    <input type="checkbox" name="is_active" class="form-check-input" {{ $banner->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label" style="font-size:13px">Hiển thị</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Huỷ</button>
                                        <button type="submit" class="btn btn-primary rounded-pill">Lưu thay đổi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImg(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById(previewId);
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush