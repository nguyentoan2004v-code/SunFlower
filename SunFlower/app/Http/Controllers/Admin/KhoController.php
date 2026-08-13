<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NguyenLieu;
use App\Models\LichSuKho;
use App\Models\LoNguyenLieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class KhoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'check.kho.role',
        ];
    }

    /**
     * Form Phiếu nhập kho (Thủ công / Nhanh)
     */
    public function importForm()
    {
        $nguyenlieus = NguyenLieu::orderBy('ten_nl')->get();
        return view('admin.inventory.import', compact('nguyenlieus'));
    }

    /**
     * Xử lý nhập kho — Cộng tonkho_thucte + ghi lichsu_kho
     */
    public function import(Request $request)
    {
        $request->validate([
            'id_nguyen_lieu' => 'required|exists:nguyen_lieu,id',
            'soluong'        => 'required|integer|min:1',
            'gia_von'        => 'nullable|numeric|min:0',
            'ghichu'         => 'nullable|string|max:500',
        ], [
            'id_nguyen_lieu.required' => 'Vui lòng chọn nguyên liệu.',
            'soluong.required'    => 'Vui lòng nhập số lượng.',
            'soluong.min'         => 'Số lượng phải lớn hơn 0.',
        ]);

        DB::beginTransaction();
        try {
            $nguyenlieu = NguyenLieu::lockForUpdate()->findOrFail($request->id_nguyen_lieu);

            // Cộng tồn kho thực tế
            $nguyenlieu->tonkho_thucte += $request->soluong;

            // Cập nhật giá vốn nếu có nhập
            if ($request->filled('gia_von') && $request->gia_von > 0) {
                $nguyenlieu->gia_von = $request->gia_von;
            }

            $nguyenlieu->save();

            // Ghi log biến động kho
            LichSuKho::create([
                'id_nguyen_lieu' => $nguyenlieu->id,
                'loai_gd'        => 'import',
                'soluong'        => $request->soluong,
                'ghichu'         => $request->ghichu ?? 'Nhập kho nguyên liệu: ' . $nguyenlieu->ten_nl,
                'manv'           => Auth::guard('nhanvien')->user()->manv,
            ]);

            DB::commit();
            return redirect()->route('admin.inventory.logs')->with('success', 'Nhập kho thành công! Đã thêm ' . number_format($request->soluong) . ' ' . $nguyenlieu->dvt . ' ' . $nguyenlieu->ten_nl);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi nhập kho: ' . $e->getMessage())->withInput();
        }
    }



    /**
     * Xem lịch sử biến động kho (Inventory Logs)
     */
    public function logs(Request $request)
    {
        $query = LichSuKho::with(['nguyenLieu', 'nhanvien']);

        if ($request->filled('loai_gd')) {
            $query->where('loai_gd', $request->loai_gd);
        }
        if ($request->filled('id_nguyen_lieu')) {
            $query->where('id_nguyen_lieu', $request->id_nguyen_lieu);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $nguyenlieus = NguyenLieu::orderBy('ten_nl')->get();

        return view('admin.inventory.logs', compact('logs', 'nguyenlieus'));
    }
}
