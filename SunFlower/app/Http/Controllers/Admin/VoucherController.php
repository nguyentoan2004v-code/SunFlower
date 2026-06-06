<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\DanhMuc;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class VoucherController extends Controller implements HasMiddleware
{
    // 1. Phân quyền truy cập (Giống hệt cách bạn làm ở CategoryController)
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = auth()->guard('nhanvien')->user();
                
                // ⚠️ KHÚC MẮC CẦN BẠN XÁC NHẬN: Tôi đang tạm để 'Quản lý Cửa hàng' và 'Quản lý Đơn hàng'. 
                // Nếu bạn có tên Role khác cho người quản lý Voucher thì hãy sửa lại ở đây nhé!
                if (!$user->hasRole('Quản lý Cửa hàng') ) {
                    abort(403, 'Bạn không có quyền thao tác với Mã giảm giá!');
                }
                
                return $next($request);
            }),
        ];
    }

    // 2. Hiển thị danh sách Voucher
    public function index()
    {
        $vouchers = Voucher::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    // 3. Form thêm mới Voucher
    public function create()
    {
        // Lấy danh sách danh mục để Admin chọn nếu Voucher thuộc loại 'danh_muc'
        $danhmucs = DanhMuc::all();
        return view('admin.vouchers.create', compact('danhmucs'));
    }

    // 4. Xử lý lưu Voucher
    public function store(Request $request)
    {
        $request->validate([
            'mavoucher' => 'required|string|max:20|unique:voucher,mavoucher',
            'tenvoucher' => 'required|string|max:100',
            'loai_giam' => 'required|string|in:phan_tram,so_tien',
            'gia_tri_giam' => 'required|numeric|min:0',
            'giam_max' => 'nullable|numeric|min:0',
            'don_min' => 'required|numeric|min:0',
            'soluong' => 'required|integer|min:1',
            'loai_ap_dung' => 'required|string|in:tat_ca,danh_muc',
            'hien_thi' => 'required|string|in:cong_khai,nhap_code',
            'ngay_bd' => 'required|date',
            'ngay_kt' => 'required|date|after_or_equal:ngay_bd',
            'trangthai' => 'required|boolean',
            'danhmuc_ids' => 'nullable|array' // Mảng chứa ID danh mục nếu chọn loại_áp_dụng = danh_muc
        ]);

        // Tạo voucher (bỏ trường danhmuc_ids ra vì nó nằm ở bảng trung gian)
        $voucher = Voucher::create($request->except('danhmuc_ids'));

        // Logic bảng trung gian: Nếu loại áp dụng là danh mục và có chọn danh mục
        if ($request->loai_ap_dung === 'danh_muc' && $request->has('danhmuc_ids')) {
            $voucher->danhmucs()->sync($request->danhmuc_ids);
        }

        return redirect()->route('admin.vouchers.index')->with('success', 'Thêm mã giảm giá thành công!');
    }

    // 5. Form sửa Voucher
    public function edit($mavoucher)
    {
        $voucher = Voucher::with('danhmucs')->findOrFail($mavoucher);
        $danhmucs = DanhMuc::all();
        
        // Lấy danh sách ID danh mục đang được áp dụng để check vào checkbox ở View
        $selectedDanhMuc = $voucher->danhmucs->pluck('madm')->toArray();

        return view('admin.vouchers.edit', compact('voucher', 'danhmucs', 'selectedDanhMuc'));
    }

    // 6. Xử lý cập nhật
    public function update(Request $request, $mavoucher)
    {
        $voucher = Voucher::findOrFail($mavoucher);

        $request->validate([
            'tenvoucher' => 'required|string|max:100',
            'loai_giam' => 'required|string|in:phan_tram,so_tien',
            'gia_tri_giam' => 'required|numeric|min:0',
            'giam_max' => 'nullable|numeric|min:0',
            'don_min' => 'required|numeric|min:0',
            'soluong' => 'required|integer|min:1',
            'loai_ap_dung' => 'required|string|in:tat_ca,danh_muc',
            'hien_thi' => 'required|string|in:cong_khai,nhap_code',
            'ngay_bd' => 'required|date',
            'ngay_kt' => 'required|date|after_or_equal:ngay_bd',
            'trangthai' => 'required|boolean',
            'danhmuc_ids' => 'nullable|array'
        ]);

        $voucher->update($request->except(['mavoucher', 'danhmuc_ids']));

        // Cập nhật lại bảng trung gian
        if ($request->loai_ap_dung === 'danh_muc' && $request->has('danhmuc_ids')) {
            $voucher->danhmucs()->sync($request->danhmuc_ids);
        } else {
            // Nếu đổi sang áp dụng 'tất cả' thì xóa hết liên kết danh mục cũ
            $voucher->danhmucs()->detach();
        }

        return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    // 7. Xóa Voucher
    public function destroy($mavoucher)
    {
        $voucher = Voucher::findOrFail($mavoucher);
        
        // Cảnh báo: Nếu có đơn hàng đã dùng voucher này thì không cho xóa, chỉ nên đổi trạng thái tắt
        if ($voucher->donhangs()->count() > 0) {
            return redirect()->route('admin.vouchers.index')->with('error', 'Không thể xóa! Voucher này đã được sử dụng trong đơn hàng. Vui lòng Tắt trạng thái thay vì xóa.');
        }

        // Bảng voucher_danhmuc sẽ tự động xóa theo (do on cascade ở migration)
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')->with('success', 'Xóa mã giảm giá thành công!');
    }
}