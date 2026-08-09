@extends('layouts.admin')

@section('title', 'Chi tiết Phiếu Nhập Kho')
@section('page_title', 'CHI TIẾT PHIẾU NHẬP KHO')

@section('content')
<div class="container-fluid mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="m-0 fw-bold text-primary">
                        <i class="fa-solid fa-file-invoice me-2"></i> Phiếu: {{ $phieuNhap->maphieu }}
                    </h5>
                    <div>
                        @if($phieuNhap->trangthai == 'Nháp')
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="fa-solid fa-pen me-1"></i> Đang là Bản Nháp</span>
                        @elseif($phieuNhap->trangthai == 'Hoàn thành')
                            <span class="badge bg-success fs-6 px-3 py-2"><i class="fa-solid fa-check-double me-1"></i> Đã hoàn thành (Đã cộng tồn kho)</span>
                        @else
                            <span class="badge bg-danger fs-6 px-3 py-2"><i class="fa-solid fa-ban me-1"></i> Đã hủy</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted fw-bold mb-3">THÔNG TIN NHÀ CUNG CẤP:</h6>
                            <div><strong>Tên NCC:</strong> {{ $phieuNhap->nhaCungCap->ten_ncc ?? 'Không xác định' }}</div>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <h6 class="text-muted fw-bold mb-3">THÔNG TIN CHỨNG TỪ:</h6>
                            <div><strong>Số phiếu:</strong> <span class="text-primary fw-bold">{{ $phieuNhap->maphieu }}</span></div>
                            <div><strong>Ngày lập:</strong> {{ $phieuNhap->created_at->format('d/m/Y H:i:s') }}</div>
                            <div><strong>Người lập:</strong> {{ $phieuNhap->nhanVien->hoten ?? $phieuNhap->manv }}</div>
                        </div>
                    </div>
                    
                    @if($phieuNhap->ghichu)
                        <div class="alert alert-light border mb-4">
                            <strong>Ghi chú:</strong> {{ $phieuNhap->ghichu }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 5%">STT</th>
                                    <th style="width: 25%">Tên Nguyên liệu</th>
                                    <th style="width: 15%">Mã Lô (LOT)</th>
                                    <th style="width: 10%">Số lượng</th>
                                    <th style="width: 10%">ĐVT</th>
                                    <th style="width: 15%">Đơn giá nhập (đ)</th>
                                    <th style="width: 20%">Thành tiền (đ)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($phieuNhap->chiTiet as $index => $detail)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="fw-medium text-primary">{{ $detail->nguyenLieu->ten_nl ?? 'N/A' }}</td>
                                        <td class="text-center font-monospace small">
                                            {{ $detail->malo }}
                                            @if($detail->hsd)
                                                <br><span class="text-muted" style="font-size: 0.75rem;">(HSD: {{ \Carbon\Carbon::parse($detail->hsd)->format('d/m/Y') }})</span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-bold">{{ number_format($detail->soluong) }}</td>
                                        <td class="text-center">{{ $detail->nguyenLieu->dvt ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($detail->dongia, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-danger">{{ number_format($detail->thanhtien, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold py-3 fs-5">TỔNG TIỀN PHẢI TRẢ:</td>
                                    <td class="text-end fw-bold text-danger py-3 fs-5">{{ number_format($phieuNhap->tongtien, 0, ',', '.') }} đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <div class="card-footer bg-light d-flex justify-content-between py-3">
                    <a href="{{ route('admin.phieunhapkho.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                    </a>
                    
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" onclick="window.print()">
                            <i class="fa-solid fa-print me-1"></i> In Phiếu
                        </button>

                        @if($phieuNhap->trangthai == 'Nháp')
                            <form action="{{ route('admin.phieunhapkho.cancel', $phieuNhap->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn HỦY phiếu nhập này không?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fa-solid fa-ban me-1"></i> Hủy Phiếu
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.phieunhapkho.approve', $phieuNhap->id) }}" method="POST" onsubmit="return confirm('Bạn chuẩn bị CHỐT phiếu nhập này. Tồn kho và giá vốn của các nguyên liệu sẽ được cộng. Hành động này không thể hoàn tác. Tiếp tục?');">
                                @csrf
                                <button type="submit" class="btn btn-success fw-bold">
                                    <i class="fa-solid fa-check-double me-1"></i> Chốt Duyệt (Nhập Kho)
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .card, .card * { visibility: visible; }
        .card { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; }
        .card-footer { display: none !important; }
    }
</style>
@endsection
