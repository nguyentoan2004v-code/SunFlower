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
        // 1. Validate dữ liệu (Đã bỏ 'masp' vì sẽ tự động lấy từ Lô hàng)
        $request->validate([
            'maphieu' => 'required|string|max:10|unique:phieu_huy_hang,maphieu',
            'malo' => 'required|exists:lo_hang,malo',
            'soluong_huy' => 'required|integer|min:1',
            'ngayhuy' => 'required|date',
            'lydo' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // 2. LockForUpdate để tránh đua lệnh (Race condition)
            $loHang = LoHang::where('malo', $request->malo)->lockForUpdate()->firstOrFail();

            if ($loHang->soluong_ton < $request->soluong_huy) {
                return back()->with('error', 'Số lượng hủy vượt quá số lượng tồn của lô hàng này (Tồn: ' . $loHang->soluong_ton . ')')->withInput();
            }

            // 3. Tạo phiếu hủy mới
            $phieuHuy = new PhieuHuyHang();
            $phieuHuy->maphieu = $request->maphieu;
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

    public function index()
    {
        $phieuHuys = PhieuHuyHang::with(['lohang', 'sanpham', 'nhanvien'])
                        ->orderBy('ngayhuy', 'desc')
                        ->get();
        return view('admin.phieuhuyhang.index', compact('phieuHuys'));
    }

    // Giao diện tạo phiếu hủy mới
    public function create()
    {
        // Chỉ lấy những lô hàng CÒN TỒN KHO (> 0) để hiển thị cho nhân viên chọn
        $loHangs = LoHang::with('sanpham')->where('soluong_ton', '>', 0)->get();
        
        // Tạo mã phiếu tự động
        $lastPhieu = PhieuHuyHang::orderBy('maphieu', 'desc')->first();
        $newMaPhieu = $lastPhieu 
            ? 'PH' . str_pad(intval(substr($lastPhieu->maphieu, 2)) + 1, 8, '0', STR_PAD_LEFT) 
            : 'PH00000001';

        return view('admin.phieuhuyhang.create', compact('loHangs', 'newMaPhieu'));
    }
}