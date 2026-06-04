@extends('layouts.admin')

@section('title', 'Quản lý Đánh giá & Phản hồi')
@section('page_title', 'QUẢN LÝ ĐÁNH GIÁ TỪ KHÁCH HÀNG')

@section('content')
<style>
    /* BỔ SUNG DARK MODE */
    [data-bs-theme="dark"] .card { background-color: #212529 !important; border: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-header.bg-white { background-color: #2c3034 !important; border-bottom: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .table { color: #e9ecef !important; border-color: #373b3e !important; }
    [data-bs-theme="dark"] .table-light th { background-color: #1a1d20 !important; color: #adb5bd !important; border-bottom: 2px solid #373b3e !important; }
    [data-bs-theme="dark"] .table td, [data-bs-theme="dark"] .table th { border-color: #373b3e !important; }
    [data-bs-theme="dark"] .table-hover tbody tr:hover td { background-color: rgba(255, 255, 255, 0.05) !important; }
    [data-bs-theme="dark"] .pagination .page-link { background-color: #2c3034 !important; border-color: #373b3e !important; color: #e9ecef !important; }
    [data-bs-theme="dark"] .pagination .page-item.active .page-link { background-color: var(--sunflower-orange, #FF8C00) !important; border-color: var(--sunflower-orange, #FF8C00) !important; color: #ffffff !important; }
    [data-bs-theme="dark"] .bg-light { background-color: #1a1d20 !important; }
    [data-bs-theme="dark"] .modal-content { background-color: #212529 !important; border-color: #373b3e !important; color: #e9ecef !important;}
    [data-bs-theme="dark"] .modal-header { border-bottom-color: #373b3e !important; }
    [data-bs-theme="dark"] .modal-footer { border-top-color: #373b3e !important; }
    [data-bs-theme="dark"] .form-control { background-color: #1a1d20 !important; border-color: #373b3e !important; color: #e9ecef !important; }
</style>

<div class="container-fluid mt-4">

    {{-- HIỂN THỊ THÔNG BÁO --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- THỐNG KÊ SẢN PHẨM BỊ 1 SAO (Nếu có) --}}
    @if($badProducts->count() > 0)
        <div class="card shadow-sm border-0 mb-4 border-start border-danger border-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> Cảnh báo: Các sản phẩm bị đánh giá 1 Sao nhiều nhất
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    @foreach($badProducts as $bad)
                        <div class="bg-danger bg-opacity-10 text-danger px-3 py-2 rounded border border-danger border-opacity-25 shadow-sm">
                            <span class="fw-bold">{{ $bad->sanPham->tensp ?? 'SP không tồn tại' }}</span>
                            <span class="badge bg-danger ms-2">{{ $bad->total_1_star }} lượt 1 sao</span>
                        </div>
                    @endforeach
                </div>
                <small class="text-muted mt-2 d-block fst-italic">* Admin vui lòng kiểm tra lại chất lượng các sản phẩm này.</small>
            </div>
        </div>
    @endif

    {{-- BỘ LỌC TÌM KIẾM --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.danhgia.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Lọc theo số sao</label>
                    <select name="star" class="form-select">
                        <option value="">-- Tất cả mức sao --</option>
                        <option value="5" {{ request('star') == '5' ? 'selected' : '' }}>5 Sao (Tuyệt vời)</option>
                        <option value="4" {{ request('star') == '4' ? 'selected' : '' }}>4 Sao (Rất tốt)</option>
                        <option value="3" {{ request('star') == '3' ? 'selected' : '' }}>3 Sao (Bình thường)</option>
                        <option value="2" {{ request('star') == '2' ? 'selected' : '' }}>2 Sao (Tạm được)</option>
                        <option value="1" {{ request('star') == '1' ? 'selected' : '' }}>1 Sao (Rất tệ)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Trạng thái phản hồi</label>
                    <select name="reply_status" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="unreplied" {{ request('reply_status') == 'unreplied' ? 'selected' : '' }}>Chưa phản hồi</option>
                        <option value="replied" {{ request('reply_status') == 'replied' ? 'selected' : '' }}>Đã phản hồi</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn text-white shadow-sm" style="background-color: var(--sunflower-orange);">
                        <i class="fa-solid fa-filter me-1"></i> Lọc dữ liệu
                    </button>
                    <a href="{{ route('admin.danhgia.index') }}" class="btn btn-secondary shadow-sm ms-2">Xóa lọc</a>
                </div>
            </form>
        </div>
    </div>

    {{-- DANH SÁCH ĐÁNH GIÁ --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                <i class="fa-regular fa-comments me-2"></i> Danh sách Đánh giá
            </h5>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">Khách hàng</th>
                            <th style="width: 20%;">Sản phẩm</th>
                            <th style="width: 35%;">Nội dung đánh giá & Phản hồi</th>
                            <th style="width: 10%; text-align: center;">Trạng thái</th>
                            <th style="width: 20%; text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                {{-- Khách hàng --}}
                                <td>
                                    <div class="fw-bold">{{ $review->khachHang->hoten ?? 'Khách ẩn danh' }}</div>
                                    <small class="text-muted">{{ $review->makh }}</small>
                                </td>
                                
                                {{-- Sản phẩm --}}
                                <td>
                                    <div class="fw-bold line-clamp-2">{{ $review->sanPham->tensp ?? 'Sản phẩm đã xóa' }}</div>
                                    <small class="text-muted text-primary">Mã HĐ: {{ $review->madon }}</small>
                                </td>

                                {{-- Đánh giá & Phản hồi --}}
                                <td>
                                    {{-- Sao --}}
                                    <div class="text-warning mb-1" style="font-size: 0.9rem;">
                                        @for($i=1; $i<=5; $i++)
                                            @if($i <= $review->so_sao)
                                                <i class="fa-solid fa-star"></i>
                                            @else
                                                <i class="fa-regular fa-star" style="color: #dee2e6;"></i>
                                            @endif
                                        @endfor
                                        <span class="text-muted ms-2" style="font-size: 0.75rem;"><i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                    
                                    {{-- Bình luận khách --}}
                                    <div class="mb-2">
                                        @if(empty($review->binh_luan))
                                            <span class="fst-italic text-muted">Khách hàng chỉ đánh giá sao, không để lại bình luận.</span>
                                        @else
                                            <span>{{ $review->binh_luan }}</span>
                                        @endif
                                    </div>

                                    {{-- Phản hồi của Admin (nếu có) --}}
                                    @if(!empty($review->phan_hoi))
                                        <div class="bg-light p-2 rounded border-start border-3 border-success">
                                            <small class="fw-bold text-success"><i class="fa-solid fa-reply me-1"></i> Cửa hàng phản hồi:</small>
                                            <p class="mb-0 mt-1" style="font-size: 0.9rem;">{{ $review->phan_hoi }}</p>
                                        </div>
                                    @endif
                                </td>

                                {{-- Trạng thái Hiển thị/Ẩn --}}
                                <td class="text-center">
                                    @if($review->trang_thai)
                                        <span class="badge bg-success">Đang hiện</span>
                                    @else
                                        <span class="badge bg-secondary">Đã ẩn</span>
                                    @endif
                                </td>

                                {{-- Thao tác --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        {{-- Nút Trả lời (Mở Modal) --}}
                                        <button type="button" class="btn btn-sm text-white shadow-sm {{ empty($review->phan_hoi) ? 'btn-info' : 'btn-secondary' }}" 
                                                data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}" title="Phản hồi">
                                            <i class="fa-solid fa-reply"></i> {{ empty($review->phan_hoi) ? 'Trả lời' : 'Sửa' }}
                                        </button>

                                        {{-- Nút Ẩn / Hiện --}}
                                        <form action="{{ route('admin.danhgia.toggle', $review->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm shadow-sm {{ $review->trang_thai ? 'btn-warning text-dark' : 'btn-success' }}" 
                                                    title="{{ $review->trang_thai ? 'Ẩn bình luận này' : 'Hiện bình luận này' }}">
                                                <i class="fa-solid {{ $review->trang_thai ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>
                                        </form>
                                        
                                        {{-- Nút Xóa --}}
                                        <form action="{{ route('admin.danhgia.destroy', $review->id) }}" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa VĨNH VIỄN đánh giá này? Hành động này không thể hoàn tác.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Xóa vĩnh viễn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- MODAL PHẢN HỒI CHO TỪNG BÌNH LUẬN --}}
                            <div class="modal fade" id="replyModal{{ $review->id }}" tabindex="-1" aria-labelledby="replyModalLabel{{ $review->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title font-weight-bold" id="replyModalLabel{{ $review->id }}" style="color: var(--sunflower-orange);">
                                                <i class="fa-solid fa-reply me-2"></i> Phản hồi Đánh giá
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.danhgia.reply', $review->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3 bg-light p-3 rounded">
                                                    <p class="mb-1 fw-bold">Khách hàng: <span class="fw-normal">{{ $review->khachHang->hoten ?? 'Ẩn danh' }}</span></p>
                                                    <p class="mb-1 fw-bold">Đánh giá: 
                                                        <span class="text-warning">
                                                            @for($i=1; $i<=5; $i++)
                                                                <i class="fa-solid fa-star {{ $i > $review->so_sao ? 'text-secondary' : '' }}"></i>
                                                            @endfor
                                                        </span>
                                                    </p>
                                                    <p class="mb-0 fw-bold">Nội dung: <span class="fw-normal fst-italic">{{ $review->binh_luan ?? '(Không có bình luận)' }}</span></p>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="phan_hoi{{ $review->id }}" class="form-label fw-bold">Nhập câu trả lời của Cửa hàng <span class="text-danger">*</span></label>
                                                    <textarea name="phan_hoi" id="phan_hoi{{ $review->id }}" rows="4" class="form-control" required placeholder="Cảm ơn bạn đã tin tưởng SunFlower...">{{ old('phan_hoi', $review->phan_hoi) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" class="btn text-white" style="background-color: var(--sunflower-orange);">Gửi phản hồi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{-- END MODAL --}}

                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Chưa có đánh giá nào phù hợp với bộ lọc.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($reviews->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection