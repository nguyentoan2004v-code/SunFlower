<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhieuHuyHang;
use App\Models\LoHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhieuHuyHangController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'maphieu' => 'required|string|max:10|unique:phieu_huy_hang,maphieu',
            'malo' => 'required|exists:lo_hang,malo',
            'masp' => 'required|exists:sanpham,masp',
            'soluong_huy' => 'required|integer|min:1',
            'ngayhuy' => 'required|date',
            'lydo' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // 2. Kiểm tra tồn kho của lô hàng đó có đủ để hủy không?
            // LockForUpdate() để ngăn chặn việc nhiều người cùng hủy một lô hàng cùng lúc
            $loHang = LoHang::where('malo', $request->malo)->lockForUpdate()->firstOrFail();

            if ($loHang->soluong_ton < $request->soluong_huy) {
                // Nếu số lượng muốn hủy vượt quá số lượng còn lại trong lô -> Báo lỗi
                return back()->with('error', 'Số lượng hủy vượt quá số lượng tồn của lô hàng này (Tồn: ' . $loHang->soluong_ton . ')')->withInput();
            }

            // 3. Tạo phiếu hủy mới
            $phieuHuy = new PhieuHuyHang();
            $phieuHuy->maphieu = $request->maphieu;
            $phieuHuy->malo = $request->malo;
            $phieuHuy->masp = $request->masp;
            
            // Gán mã nhân viên đang đăng nhập lập phiếu hủy
            $phieuHuy->manv = Auth::user()->manv; 
            
            $phieuHuy->soluong_huy = $request->soluong_huy;
            $phieuHuy->ngayhuy = $request->ngayhuy;
            $phieuHuy->lydo = $request->lydo;
            $phieuHuy->save();

            // 4. Trừ đi số lượng tồn của lô hàng đó
            $loHang->soluong_ton -= $request->soluong_huy;
            $loHang->save();

            DB::commit();

            return redirect()->route('admin.phieuhuyhang.index')->with('success', 'Đã lập phiếu hủy hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi hủy hàng: ' . $e->getMessage())->withInput();
        }
    }
}