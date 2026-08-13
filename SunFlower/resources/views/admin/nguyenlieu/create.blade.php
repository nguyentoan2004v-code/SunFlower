@extends('layouts.admin')

@section('title', 'Thêm Nguyên liệu')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold"><i class="fa-solid fa-plus me-2" style="color: var(--sunflower-orange);"></i>Thêm Nguyên liệu mới</h2>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('admin.nguyenlieu.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tên nguyên liệu <span class="text-danger">*</span></label>
                    <input type="text" name="ten_nl" class="form-control @error('ten_nl') is-invalid @enderror" value="{{ old('ten_nl') }}" placeholder="VD: Hoa hồng đỏ, Giấy gói kraft..." required>
                    @error('ten_nl') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Đơn vị tính <span class="text-danger">*</span></label>
                    <select name="dvt" class="form-select @error('dvt') is-invalid @enderror" required>
                        <option value="cành" {{ old('dvt') == 'cành' ? 'selected' : '' }}>Cành</option>
                        <option value="tờ" {{ old('dvt') == 'tờ' ? 'selected' : '' }}>Tờ</option>
                        <option value="xốp" {{ old('dvt') == 'xốp' ? 'selected' : '' }}>Xốp</option>
                        <option value="cuộn" {{ old('dvt') == 'cuộn' ? 'selected' : '' }}>Cuộn</option>
                        <option value="cái" {{ old('dvt') == 'cái' ? 'selected' : '' }}>Cái</option>
                        <option value="bó" {{ old('dvt') == 'bó' ? 'selected' : '' }}>Bó</option>
                        <option value="mét" {{ old('dvt') == 'mét' ? 'selected' : '' }}>Mét</option>
                        
                    </select>
                    @error('dvt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Giá vốn (đ)</label>
                    <input type="number" name="gia_von" class="form-control" value="{{ old('gia_von', 0) }}" min="0" step="1000">
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-save me-1"></i> Lưu nguyên liệu</button>
                <a href="{{ route('admin.nguyenlieu.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
