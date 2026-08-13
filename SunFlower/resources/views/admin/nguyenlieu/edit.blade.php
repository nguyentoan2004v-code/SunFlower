@extends('layouts.admin')

@section('title', 'Sửa Nguyên liệu')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold"><i class="fa-solid fa-pen-to-square me-2" style="color: var(--sunflower-orange);"></i>Sửa Nguyên liệu: {{ $nguyenlieu->ten_nl }}</h2>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('admin.nguyenlieu.update', $nguyenlieu->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tên nguyên liệu <span class="text-danger">*</span></label>
                    <input type="text" name="ten_nl" class="form-control @error('ten_nl') is-invalid @enderror" value="{{ old('ten_nl', $nguyenlieu->ten_nl) }}" required>
                    @error('ten_nl') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Đơn vị tính <span class="text-danger">*</span></label>
                    <select name="dvt" class="form-select" required>
                        @foreach(['cành', 'tờ', 'xốp', 'cuộn', 'cái', 'bó', 'mét'] as $u)
                            <option value="{{ $u }}" {{ old('dvt', $nguyenlieu->dvt) == $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Giá vốn (đ)</label>
                    <input type="number" name="gia_von" class="form-control" value="{{ old('gia_von', $nguyenlieu->gia_von) }}" min="0" step="1000">
                </div>
            </div>

            {{-- Thông tin tồn kho (chỉ đọc) --}}
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label">Tồn thực tế</label>
                    <input type="text" class="form-control" value="{{ number_format($nguyenlieu->tonkho_thucte) }} {{ $nguyenlieu->dvt }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Đang giữ</label>
                    <input type="text" class="form-control" value="{{ number_format($nguyenlieu->tonkho_datruoc) }} {{ $nguyenlieu->dvt }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Khả dụng</label>
                    <input type="text" class="form-control fw-bold {{ $nguyenlieu->available_stock <= 0 ? 'text-danger' : 'text-success' }}" value="{{ number_format($nguyenlieu->available_stock) }} {{ $nguyenlieu->dvt }}" disabled>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Cập nhật</button>
                <a href="{{ route('admin.nguyenlieu.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
