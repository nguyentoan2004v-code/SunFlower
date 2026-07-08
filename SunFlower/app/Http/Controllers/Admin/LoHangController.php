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
            'check.kho.role',
        ];
    }
    public function store(Request $request)
    {
        // 1. Validate
        $request->validate([
            'masp' => 'required|exists:sanpham,masp',
            'soluong_nhap' => 'required|integer|min:1',
            'ngaynhap' => 'required|date',
            'ngayhethan' => 'required|date|after_or_equal:ngaynhap',
        ]);

        // Cảnh báo nếu ngày hết hạn quá sát ngày nhập (dưới 1 ngày)
        $ngayNhap = \Carbon\Carbon::parse($request->ngaynhap);
        $ngayHetHan = \Carbon\Carbon::parse($request->ngayhethan);
        if ($ngayNhap->diffInDays($ngayHetHan) < 1) {
            return back()->with('error', 'Cảnh báo: Ngày hết hạn phải cách ngày nhập ít nhất 1 ngày đối với hoa tươi!')->withInput();
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
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
            $loHang->ngaynhap = $request->ngaynhap;
            $loHang->ngayhethan = $request->ngayhethan;
            
            $loHang->save();

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('admin.lohang.index')->with('success', 'Nhập lô hoa mới thành công!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
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

        return view('admin.lohang.create', compact('sanPhams'));
    }
}