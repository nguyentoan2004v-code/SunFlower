<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SanPham;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LoHangController extends Controller implements HasMiddleware
{

    
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = auth()->guard('nhanvien')->user();
                
                if (!$user->hasRole('Quản lý Cửa hàng') && !$user->hasRole('Quản lý Kho hàng')) {
                    abort(403, 'Bạn không có quyền thao tác với Kho hàng!');
                }
                
                return $next($request);
            }),
        ];
    }
    public function store(Request $request)
    {
        
        // 1. Validate
        $request->validate([
            'malo' => 'required|string|max:10|unique:lo_hang,malo',
            'masp' => 'required|exists:sanpham,masp',
            'soluong_nhap' => 'required|integer|min:1',
            'ngaynhap' => 'required|date',
            'ngayhethan' => 'required|date|after_or_equal:ngaynhap',
        ]);

        try {
            // 2. Tạo Lô hàng mới
            $loHang = new LoHang();
            $loHang->malo = $request->malo;
            $loHang->masp = $request->masp;
            
            $loHang->manv = Auth::guard('nhanvien')->user()->manv;
            
            
            $loHang->soluong_nhap = $request->soluong_nhap;
            // TỒN KHO BAN ĐẦU CHÍNH BẰNG SỐ LƯỢNG NHẬP
            $loHang->soluong_ton = $request->soluong_nhap; 
            $loHang->ngaynhap = $request->ngaynhap;
            $loHang->ngayhethan = $request->ngayhethan;
            
            $loHang->save();

            return redirect()->route('admin.lohang.index')->with('success', 'Nhập lô hoa mới thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi nhập kho: ' . $e->getMessage())->withInput();
        }
    }

    public function index()
    {
        // Lấy danh sách lô hàng, kèm theo thông tin sản phẩm và nhân viên để hiển thị
        $loHangs = LoHang::with(['sanpham', 'nhanvien'])->orderBy('ngaynhap', 'desc')->get();
        return view('admin.lohang.index', compact('loHangs'));
    }

    public function create()
    {
        // Lấy danh sách sản phẩm để đưa vào thẻ <select> cho nhân viên chọn
        $sanPhams = SanPham::all();
        $lastLoHang = LoHang::orderBy('malo', 'desc')->first();

        if (!$lastLoHang) {
            // Nếu kho chưa có lô nào, bắt đầu bằng LH00000001
            $newMaLo = 'LH00000001';
        } else {
            // Cắt lấy phần số (bỏ chữ 'LH' ở đầu), cộng thêm 1
            $lastNumber = intval(substr($lastLoHang->malo, 2));
            $newNumber = $lastNumber + 1;
            
            // Ép lại thành chuỗi 8 chữ số có số 0 ở đầu (VD: 1 -> 00000001)
            $newMaLo = 'LH' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);
        }

        // Truyền $newMaLo ra ngoài View
        return view('admin.lohang.create', compact('sanPhams', 'newMaLo'));
    }
}