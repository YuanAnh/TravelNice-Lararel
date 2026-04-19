@extends('admin.layouts.admin')
@section('title', 'Log hoạt động Admin')
@section('page-title', 'Log hoạt động Admin')
@section('breadcrumb', 'Admin / Logs')

@section('content')

{{-- Filters --}}
<form method="GET" class="d-flex gap-2 mb-4 flex-wrap">
    <select name="admin_id" class="form-select form-select-sm rounded-pill" style="width:160px;font-size:13px" onchange="this.form.submit()">
        <option value="">Tất cả admin</option>
        @foreach($admins as $admin)
        <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected':'' }}>{{ $admin->name }}</option>
        @endforeach
    </select>

    <select name="action" class="form-select form-select-sm rounded-pill" style="width:160px;font-size:13px" onchange="this.form.submit()">
        <option value="">Tất cả hành động</option>
        @foreach($actions as $action)
        <option value="{{ $action }}" {{ request('action') === $action ? 'selected':'' }}>{{ $action }}</option>
        @endforeach
    </select>

    <input type="date" name="date" class="form-control form-control-sm rounded-pill" style="width:160px;font-size:13px"
           value="{{ request('date') }}" onchange="this.form.submit()">

    @if(request()->hasAny(['admin_id','action','date']))
    <a href="{{ route('admin.logs.index') }}" class="btn btn-sm btn-outline-danger rounded-pill">
        <i class="bi bi-x-circle me-1"></i> Xoá lọc
    </a>
    @endif
</form>

<div class="admin-table-wrap">
    <div class="admin-table-header">
        <div class="admin-table-title"><i class="bi bi-journal-text me-2 text-primary"></i>Nhật ký hoạt động</div>
        <span style="font-size:13px;color:#6B7280;margin-left:auto">{{ $logs->total() }} bản ghi</span>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:140px">Thời gian</th>
                    <th style="width:130px">Admin</th>
                    <th style="width:120px">Hành động</th>
                    <th>Mô tả</th>
                    <th style="width:110px">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="font-size:12px;color:#6B7280">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td>
                        <div style="font-size:13px;font-weight:600">{{ $log->admin->name ?? '—' }}</div>
                    </td>
                    <td>
                        @php
                            $actionColors = [
                                'login'    => ['#D1FAE5','#065F46'],
                                'logout'   => ['#F3F4F6','#6B7280'],
                                'create'   => ['#DBEAFE','#1E40AF'],
                                'update'   => ['#FEF3C7','#92400E'],
                                'delete'   => ['#FEE2E2','#991B1B'],
                                'confirm'  => ['#D1FAE5','#065F46'],
                                'approve'  => ['#E0E7FF','#3730A3'],
                            ];
                            $c = $actionColors[strtolower($log->action)] ?? ['#F3F4F6','#6B7280'];
                        @endphp
                        <span style="background:{{ $c[0] }};color:{{ $c[1] }};font-size:11px;padding:3px 8px;border-radius:10px;font-weight:600">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td style="font-size:13px">{{ $log->description }}</td>
                    <td style="font-size:12px;color:#9CA3AF">{{ $log->ip_address }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-journal-x" style="font-size:36px;display:block;margin-bottom:8px"></i>
                        Chưa có log nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="p-3 border-top">
        {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection