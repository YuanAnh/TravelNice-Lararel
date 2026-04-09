@extends('admin.layouts.admin')

@section('title', isset($tour) ? 'Sửa Tour' : 'Thêm Tour mới')
@section('page-title', isset($tour) ? 'Sửa Tour' : 'Thêm Tour mới')
@section('breadcrumb', 'Admin / Tour / ' . (isset($tour) ? 'Sửa' : 'Thêm mới'))

@push('styles')
<style>
.thumbnail-preview { width: 200px; height: 140px; object-fit: cover; border-radius: 10px; border: 2px solid var(--admin-border); }
.slug-preview { font-size: 12px; color: #6B7280; margin-top: 4px; }
</style>
@endpush

@section('content')

<form method="POST"
      action="{{ isset($tour) ? route('admin.tours.update', $tour) : route('admin.tours.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if(isset($tour)) @method('PUT') @endif

    @if($errors->any())
    <div class="alert alert-danger mb-4" style="border-radius:10px;font-size:13px">
        <i class="bi bi-exclamation-circle me-2"></i>
        <strong>Có lỗi xảy ra:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="row g-4">

        {{-- LEFT --}}
        <div class="col-lg-8">

            {{-- Thông tin cơ bản --}}
            <div class="admin-form-card">
                <h6><i class="bi bi-info-circle me-2"></i>Thông tin cơ bản</h6>
                <div class="mb-3">
                    <label class="form-label">Tên tour <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid':'' }}"
                           value="{{ old('title', $tour->title ?? '') }}"
                           placeholder="VD: Tour Nhật Bản Mùa Hoa Anh Đào 7N6Đ"
                           oninput="generateSlug(this.value)" required>
                    <div class="slug-preview">Slug: <span id="slugPreview">{{ old('slug', $tour->slug ?? '') }}</span></div>
                    <input type="hidden" name="slug" id="slugInput" value="{{ old('slug', $tour->slug ?? '') }}">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control {{ $errors->has('description') ? 'is-invalid':'' }}"
                              rows="6" placeholder="Mô tả chi tiết về tour...">{{ old('description', $tour->description ?? '') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-0">
                    <label class="form-label">Chính sách huỷ tour</label>
                    <textarea name="cancel_policy" class="form-control" rows="3"
                              placeholder="VD: Huỷ trước 7 ngày hoàn 100%, huỷ trong 3 ngày hoàn 50%...">{{ old('cancel_policy', $tour->cancel_policy ?? '') }}</textarea>
                </div>
            </div>

            {{-- Lịch trình (dynamic) --}}
            <div class="admin-form-card">
                <h6><i class="bi bi-list-ol me-2"></i>Lịch trình tour</h6>
                <div id="schedules-wrap">
                    @if(isset($tour) && $tour->schedules->count())
                        @foreach($tour->schedules as $i => $sch)
                        <div class="schedule-row border rounded-3 p-3 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-primary">Ngày {{ $i + 1 }}</span>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-auto rounded-pill" onclick="removeSchedule(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <input type="hidden" name="schedules[{{ $i }}][day_number]" value="{{ $i + 1 }}">
                            <div class="mb-2">
                                <input type="text" name="schedules[{{ $i }}][title]" class="form-control form-control-sm"
                                       placeholder="Tiêu đề ngày" value="{{ $sch->title }}">
                            </div>
                            <textarea name="schedules[{{ $i }}][description]" class="form-control form-control-sm"
                                      rows="2" placeholder="Mô tả hoạt động trong ngày">{{ $sch->description }}</textarea>
                        </div>
                        @endforeach
                    @else
                        <div class="schedule-row border rounded-3 p-3 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-primary">Ngày 1</span>
                            </div>
                            <input type="hidden" name="schedules[0][day_number]" value="1">
                            <div class="mb-2">
                                <input type="text" name="schedules[0][title]" class="form-control form-control-sm" placeholder="Tiêu đề ngày">
                            </div>
                            <textarea name="schedules[0][description]" class="form-control form-control-sm" rows="2" placeholder="Mô tả hoạt động"></textarea>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" onclick="addSchedule()">
                    <i class="bi bi-plus me-1"></i> Thêm ngày
                </button>
            </div>

        </div>

        {{-- RIGHT --}}
        <div class="col-lg-4">

            {{-- Publish --}}
            <div class="admin-form-card">
                <h6><i class="bi bi-send me-2"></i>Xuất bản</h6>
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="active"   {{ old('status', $tour->status ?? 'active') === 'active'   ? 'selected':'' }}>✅ Hoạt động</option>
                        <option value="inactive" {{ old('status', $tour->status ?? '') === 'inactive' ? 'selected':'' }}>⏸ Tạm dừng</option>
                        <option value="draft"    {{ old('status', $tour->status ?? '') === 'draft'    ? 'selected':'' }}>📝 Bản nháp</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-check2 me-1"></i>
                        {{ isset($tour) ? 'Cập nhật' : 'Tạo Tour' }}
                    </button>
                    <a href="{{ route('admin.tours.index') }}" class="btn btn-outline-secondary">Huỷ</a>
                </div>
            </div>

            {{-- Phân loại --}}
            <div class="admin-form-card">
                <h6><i class="bi bi-tag me-2"></i>Phân loại</h6>
                <div class="mb-3">
                    <label class="form-label">Điểm đến</label>
                    <select name="destination_id" class="form-select">
                        <option value="">-- Chọn điểm đến --</option>
                        @foreach($destinations as $dest)
                        <option value="{{ $dest->id }}" {{ old('destination_id', $tour->destination_id ?? '') == $dest->id ? 'selected':'' }}>
                            {{ $dest->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $tour->category_id ?? '') == $cat->id ? 'selected':'' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Giá & Thời gian --}}
            <div class="admin-form-card">
                <h6><i class="bi bi-currency-dollar me-2"></i>Giá & Thời gian</h6>
                <div class="mb-3">
                    <label class="form-label">Số ngày <span class="text-danger">*</span></label>
                    <input type="number" name="duration_days" class="form-control"
                           value="{{ old('duration_days', $tour->duration_days ?? 1) }}" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giá người lớn (đ) <span class="text-danger">*</span></label>
                    <input type="number" name="price_adult" class="form-control"
                           value="{{ old('price_adult', $tour->price_adult ?? '') }}"
                           placeholder="VD: 9990000" min="0" step="10000" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giá trẻ em (đ)</label>
                    <input type="number" name="price_child" class="form-control"
                           value="{{ old('price_child', $tour->price_child ?? '') }}"
                           placeholder="VD: 5990000" min="0" step="10000">
                </div>
                <div class="mb-0">
                    <label class="form-label">Số slot tối đa <span class="text-danger">*</span></label>
                    <input type="number" name="max_slots" class="form-control"
                           value="{{ old('max_slots', $tour->max_slots ?? 20) }}" min="1" required>
                </div>
            </div>

            {{-- Ảnh --}}
            <div class="admin-form-card">
                <h6><i class="bi bi-image me-2"></i>Ảnh đại diện</h6>
                @if(isset($tour) && $tour->thumbnail)
                <img src="{{ asset('storage/'.$tour->thumbnail) }}" class="thumbnail-preview mb-3 w-100" id="thumbPreview">
                @else
                <img src="https://placehold.co/200x140/F3F4F6/9CA3AF?text=Preview" class="thumbnail-preview mb-3 w-100" id="thumbPreview">
                @endif
                <input type="file" name="thumbnail" class="form-control form-control-sm" accept="image/*"
                       onchange="previewThumb(this)">
                <div class="form-text">JPG, PNG. Khuyến nghị 800×500px</div>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
let scheduleCount = {{ isset($tour) ? $tour->schedules->count() : 1 }};

function generateSlug(title) {
    const slug = title.toLowerCase()
        .replace(/[àáạảãâầấậẩẫăằắặẳẵ]/g, 'a')
        .replace(/[èéẹẻẽêềếệểễ]/g, 'e')
        .replace(/[ìíịỉĩ]/g, 'i')
        .replace(/[òóọỏõôồốộổỗơờớợởỡ]/g, 'o')
        .replace(/[ùúụủũưừứựửữ]/g, 'u')
        .replace(/[ỳýỵỷỹ]/g, 'y')
        .replace(/đ/g, 'd')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim().replace(/\s+/g, '-')
        .replace(/-+/g, '-');
    document.getElementById('slugPreview').textContent = slug;
    document.getElementById('slugInput').value = slug;
}

function addSchedule() {
    const i = scheduleCount++;
    const wrap = document.getElementById('schedules-wrap');
    const div = document.createElement('div');
    div.className = 'schedule-row border rounded-3 p-3 mb-3';
    div.innerHTML = `
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary">Ngày ${i + 1}</span>
            <button type="button" class="btn btn-sm btn-outline-danger ms-auto rounded-pill" onclick="removeSchedule(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <input type="hidden" name="schedules[${i}][day_number]" value="${i + 1}">
        <div class="mb-2">
            <input type="text" name="schedules[${i}][title]" class="form-control form-control-sm" placeholder="Tiêu đề ngày">
        </div>
        <textarea name="schedules[${i}][description]" class="form-control form-control-sm" rows="2" placeholder="Mô tả hoạt động"></textarea>
    `;
    wrap.appendChild(div);
}

function removeSchedule(btn) {
    btn.closest('.schedule-row').remove();
}

function previewThumb(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('thumbPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush