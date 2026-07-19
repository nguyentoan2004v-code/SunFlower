<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhieuHuyHang;
use App\Models\LoHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PhieuHuyHangController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            'check.kho.role',
        ];
    }
    public function store(Request $request)
    {
        // 1. Validate dữ liệu (Đã bỏ 'masp' vì sẽ tự động lấy từ Lô hàng)
        $request->validate([
            'malo' => 'required|exists:lo_hang,malo',
            'soluong_huy' => 'required|integer|min:1',
            'ngayhuy' => 'required|date',
            'lydo' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Tự sinh mã phiếu hủy (dùng lockForUpdate để tránh trùng mã)
            $lastPhieu = PhieuHuyHang::lockForUpdate()->orderBy('maphieu', 'desc')->first();
            $newMaPhieu = $lastPhieu 
                ? 'PH' . str_pad(intval(substr($lastPhieu->maphieu, 2)) + 1, 8, '0', STR_PAD_LEFT) 
                : 'PH00000001';

            // 2. LockForUpdate để tránh đua lệnh (Race condition) trên Lô Hàng
            $loHang = LoHang::where('malo', $request->malo)->lockForUpdate()->firstOrFail();

            if ($loHang->soluong_ton < $request->soluong_huy) {
                return back()->with('error', 'Số lượng hủy vượt quá số lượng tồn của lô hàng này (Tồn: ' . $loHang->soluong_ton . ')')->withInput();
            }

            // 3. Tạo phiếu hủy mới
            $phieuHuy = new PhieuHuyHang();
            $phieuHuy->maphieu = $newMaPhieu;
            $phieuHuy->malo = $request->malo;
            
            // TỰ ĐỘNG lấy masp từ lô hàng, đảm bảo tính chính xác 100%
            $phieuHuy->masp = $loHang->masp; 
            
            // SỬA LỖI: Gán mã nhân viên vào phiếu hủy (không phải lô hàng)
            $phieuHuy->manv = Auth::guard('nhanvien')->user()->manv;
            
            $phieuHuy->soluong_huy = $request->soluong_huy;
            $phieuHuy->ngayhuy = $request->ngayhuy;
            $phieuHuy->lydo = $request->lydo;
            $phieuHuy->save();

            // 4. Trừ đi số lượng tồn của lô hàng
            $loHang->soluong_ton -= $request->soluong_huy;
            $loHang->save();

            DB::commit();

            return redirect()->route('admin.phieuhuyhang.index')->with('success', 'Đã lập phiếu hủy hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi hủy hàng: ' . $e->getMessage())->withInput();
        }
    }

    public function index(Request $request)
    {
        $query = PhieuHuyHang::with(['lohang', 'sanpham', 'nhanvien']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('maphieu', 'like', "%{$search}%")
                  ->orWhere('malo', 'like', "%{$search}%")
                  ->orWhereHas('sanpham', function($q2) use ($search) {
                      $q2->where('tensp', 'like', "%{$search}%");
                  });
            });
        }

        $phieuHuys = $query->orderBy('ngayhuy', 'desc')->paginate(10)->withQueryString();
        return view('admin.phieuhuyhang.index', compact('phieuHuys'));
    }

    public function create()
    {
        // Chỉ lấy những lô hàng CÒN TỒN KHO (> 0) để hiển thị cho nhân viên chọn
        $loHangs = LoHang::with('sanpham')->where('soluong_ton', '>', 0)->get();

        return view('admin.phieuhuyhang.create', compact('loHangs'));
    }
}