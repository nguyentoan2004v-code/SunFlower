<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SanPham;
use App\Models\NhaCungCap;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LoHangController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            'check.kho.role',
        ];
    }

    /**
     * Danh sách lô hàng — có lọc, tìm kiếm, phân trang, thống kê
     */
    public function index(Request $request)
    {
        $query = LoHang::with(['sanpham', 'nhanvien']);

        // === BỘ LỌC ===

        // Tìm kiếm theo mã lô hoặc tên sản phẩm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('malo', 'like', "%{$search}%")
                  ->orWhereHas('sanpham', function ($q2) use ($search) {
                      $q2->where('tensp', 'like', "%{$search}%");
                  });
            });
        }

        // Lọc theo sản phẩm
        if ($request->filled('masp')) {
            $query->where('masp', $request->masp);
        }

        // Lọc theo khoảng ngày nhập
        if ($request->filled('tu_ngay')) {
            $query->whereDate('ngaynhap', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('ngaynhap', '<=', $request->den_ngay);
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $today = Carbon::today();
            switch ($request->trang_thai) {
                case 'con_hang':
                    $query->where('soluong_ton', '>', 0)->whereDate('ngayhethan', '>', $today);
                    break;
                case 'het_hang':
                    $query->where('soluong_ton', '<=', 0);
                    break;
                case 'sap_het_han':
                    $query->where('soluong_ton', '>', 0)
                          ->whereDate('ngayhethan', '>', $today)
                          ->whereDate('ngayhethan', '<=', $today->copy()->addDays(3));
                    break;
                case 'het_han':
                    $query->whereDate('ngayhethan', '<=', $today);
                    break;
                case 'ton_thap':
                    $query->where('soluong_ton', '>', 0)
                          ->whereRaw('soluong_ton <= soluong_nhap * 0.2');
                    break;
            }
        }

        // === THỐNG KÊ (trên toàn bộ dữ liệu, không phụ thuộc filter) ===
        $today = Carbon::today();
        $stats = [
            'tong_lo'        => LoHang::count(),
            'tong_ton'       => LoHang::sum('soluong_ton'),
            'sap_het_han'    => LoHang::where('soluong_ton', '>', 0)
                                      ->whereDate('ngayhethan', '>', $today)
                                      ->whereDate('ngayhethan', '<=', $today->copy()->addDays(3))
                                      ->count(),
            'het_hang'       => LoHang::where('soluong_ton', '<=', 0)->count(),
            'can_xu_ly'      => LoHang::where('soluong_ton', '>', 0)
                                      ->whereDate('ngayhethan', '<=', $today)
                                      ->count(),
        ];

        // Sắp xếp và phân trang
        $loHangs = $query->orderBy('ngaynhap', 'desc')->paginate(10)->appends($request->query());

        // Danh sách sản phẩm cho bộ lọc
        $sanPhams = SanPham::orderBy('tensp')->get();

        return view('admin.lohang.index', compact('loHangs', 'stats', 'sanPhams'));
    }

    /**
     * Form tạo lô hàng mới
     */
    public function create()
    {
        $sanPhams = SanPham::all();
        $nhaCungCaps = NhaCungCap::orderBy('ten_ncc')->get();
        return view('admin.lohang.create', compact('sanPhams', 'nhaCungCaps'));
    }

    /**
     * Lưu lô hàng mới (đã bổ sung 3 trường: giá nhập, nhà cung cấp, ghi chú)
     */
    public function store(Request $request)
    {
        // 1. Validate
        $request->validate([
            'masp' => 'required|exists:sanpham,masp',
            'soluong_nhap' => 'required|integer|min:1',
            'ngaynhap' => 'required|date',
            'ngayhethan' => 'required|date|after_or_equal:ngaynhap',
            'gia_nhap' => 'nullable|numeric|min:0',
            'nhacungcap' => 'nullable|string|max:255',
            'ghichu' => 'nullable|string|max:1000',
        ]);

        // Cảnh báo nếu ngày hết hạn quá sát ngày nhập (dưới 1 ngày)
        $ngayNhap = Carbon::parse($request->ngaynhap);
        $ngayHetHan = Carbon::parse($request->ngayhethan);
        if ($ngayNhap->diffInDays($ngayHetHan) < 1) {
            return back()->with('error', 'Cảnh báo: Ngày hết hạn phải cách ngày nhập ít nhất 1 ngày đối với hoa tươi!')->withInput();
        }

        DB::beginTransaction();
        try {
            // Tự sinh mã lô (sử dụng lockForUpdate để tránh trùng mã khi concurrent)
            $lastLoHang = LoHang::lockForUpdate()->orderBy('malo', 'desc')->first();
            if (!$lastLoHang) {
                $newMaLo = 'LH00000001';
            } else {
                $lastNumber = intval(substr($lastLoHang->malo, 2));
                $newMaLo = 'LH' . str_pad($lastNumber + 1, 8, '0', STR_PAD_LEFT);
            }

            // 2. Tạo Lô hàng mới
            $loHang = new LoHang();
            $loHang->malo = $newMaLo;
            $loHang->masp = $request->masp;
            $loHang->manv = Auth::guard('nhanvien')->user()->manv;
            $loHang->soluong_nhap = $request->soluong_nhap;
            $loHang->soluong_ton = $request->soluong_nhap; 
            $loHang->gia_nhap = $request->gia_nhap;
            $loHang->ngaynhap = $request->ngaynhap;
            $loHang->ngayhethan = $request->ngayhethan;
            $loHang->nhacungcap = $request->nhacungcap;
            $loHang->ghichu = $request->ghichu;

            // Tự động lưu nhà cung cấp mới vào DB nếu chưa tồn tại
            if ($request->filled('nhacungcap')) {
                NhaCungCap::firstOrCreate(['ten_ncc' => $request->nhacungcap]);
            }
            
            $loHang->save();

            DB::commit();
            return redirect()->route('admin.lohang.index')->with('success', 'Nhập lô hoa mới thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi nhập kho: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xem chi tiết lô hàng (bao gồm lịch sử hủy)
     */
    public function show(string $malo)
    {
        $loHang = LoHang::with(['sanpham', 'nhanvien', 'phieuhuyhangs' => function ($q) {
            $q->with('nhanvien')->orderBy('ngayhuy', 'desc');
        }])->where('malo', $malo)->firstOrFail();

        return view('admin.lohang.show', compact('loHang'));
    }

    /**
     * Xóa mềm lô hàng
     */
    public function destroy(string $malo)
    {
        $loHang = LoHang::where('malo', $malo)->firstOrFail();
        
        // Không cho xóa nếu có phiếu hủy liên quan
        if ($loHang->phieuhuyhangs()->count() > 0) {
            return back()->with('error', 'Không thể xóa lô hàng này vì đã có phiếu hủy liên quan!');
        }

        $loHang->delete();
        return redirect()->route('admin.lohang.index')->with('success', 'Đã xóa lô hàng ' . $malo . ' thành công!');
    }

    /**
     * API: Lấy thông tin sản phẩm cho preview trên form create (AJAX)
     */
    public function getProductInfo(string $masp)
    {
        $sp = SanPham::where('masp', $masp)->first();
        if (!$sp) {
            return response()->json(['error' => 'Không tìm thấy sản phẩm'], 404);
        }

        // Tính tồn kho tổng từ tất cả lô hàng
        $tongTon = LoHang::where('masp', $masp)->sum('soluong_ton');

        // Xử lý URL ảnh
        $hinhAnh = '';
        if (!empty($sp->hinhanh)) {
            $hinhAnh = str_starts_with($sp->hinhanh, 'http') 
                ? $sp->hinhanh 
                : asset('storage/' . ltrim($sp->hinhanh, '/'));
        }

        return response()->json([
            'tensp' => $sp->tensp,
            'giaban' => $sp->giaban,
            'giakm' => $sp->giakm,
            'hinhanh' => $hinhAnh,
            'danhmuc' => $sp->danhmuc ? $sp->danhmuc->tendm : 'Chưa phân loại',
            'tong_ton' => $tongTon,
        ]);
    }
}