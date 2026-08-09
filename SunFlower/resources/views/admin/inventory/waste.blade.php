@extends('layouts.admin')

@section('title', 'Xuất hủy Nguyên liệu')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold"><i class="fa-solid fa-file-circle-xmark me-2 text-danger"></i>Phiếu Xuất Hủy Nguyên liệu</h2>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('admin.inventory.waste') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Chọn nguyên liệu <span class="text-danger">*</span></label>
                    <select name="id_nguyen_lieu" class="form-select @error('id_nguyen_lieu') is-invalid @enderror" required>
                        <option value="">-- Chọn nguyên liệu --</option>
                        @foreach($nguyenlieus as $nl)
                            @php $khaDung = max(0, $nl->tonkho_thucte - $nl->tonkho_datruoc); @endphp
                            <option value="{{ $nl->id }}" {{ old('id_nguyen_lieu') == $nl->id ? 'selected' : '' }}>
                                {{ $nl->ten_nl }} (Khả dụng: {{ number_format($khaDung) }} {{ $nl->dvt }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_nguyen_lieu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Số lượng hủy <span class="text-danger">*</span></label>
                    <input loai_gd="number" name="soluong" class="form-control @error('soluong') is-invalid @enderror" value="{{ old('soluong') }}" min="1" required>
                    @error('soluong') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Lý do hủy <span class="text-danger">*</span></label>
                    <textarea name="ghichu" class="form-control @error('ghichu') is-invalid @enderror" rows="2" placeholder="VD: Hoa héo, hết hạn sử dụng, hỏng trong vận chuyển..." required>{{ old('ghichu') }}</textarea>
                    @error('ghichu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button loai_gd="submit" class="btn btn-danger" onclick="return confirm('Bạn chắc chắn muốn hủy nguyên liệu này? Thao tác không thể hoàn tác.')"><i class="fa-solid fa-trash me-1"></i> Xác nhận Hủy</button>
                <a href="{{ route('admin.inventory.logs') }}" class="btn btn-outline-secondary">Xem lịch sử kho</a>
            </div>
        </form>
    </div>
</div>
@endsection
