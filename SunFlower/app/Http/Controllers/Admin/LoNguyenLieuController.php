<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoNguyenLieu;
use App\Models\NguyenLieu;
use App\Models\LichSuKho;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoNguyenLieuController extends Controller
{
    public function index(Request $request)
    {
        $query = LoNguyenLieu::with(['nguyenLieu', 'phieuNhap']);

        if ($request->filled('id_nguyen_lieu')) {
            $query->where('id_nguyen_lieu', $request->id_nguyen_lieu);
        }

        if ($request->filled('status')) {
            if ($request->status == 'Còn hàng') {
                $query->where('soluong_hientai', '>', 0);
            } elseif ($request->status == 'Hết hàng') {
                $query->where('soluong_hientai', '<=', 0);
            }
        } else {
            // Mặc định chỉ hiện các lô còn hàng
            $query->where('soluong_hientai', '>', 0);
        }

        $lots = $query->orderByRaw('ISNULL(hsd), hsd ASC') // Hết hạn lên đầu
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);

        $nguyenlieus = NguyenLieu::orderBy('ten_nl')->get();

        return view('admin.longuyenlieu.index', compact('lots', 'nguyenlieus'));
    }

    // Gia hạn HSD
    public function extendExpiry(Request $request, $id)
    {
        $request->validate([
            'new_hsd' => 'required|date'
        ]);

        $lot = LoNguyenLieu::findOrFail($id);
        $oldDate = $lot->hsd;
        $lot->hsd = $request->new_hsd;
        $lot->save();

        return back()->with('success', "Đã gia hạn lô {$lot->malo} thành công từ " . 
            ($oldDate ? Carbon::parse($oldDate)->format('d/m/Y') : 'Không có') . 
            " đến " . Carbon::parse($lot->hsd)->format('d/m/Y'));
    }

    // Xuất hủy đích danh
    public function wasteLot(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $lot = LoNguyenLieu::lockForUpdate()->findOrFail($id);
            $nguyenlieu = NguyenLieu::lockForUpdate()->findOrFail($lot->id_nguyen_lieu);

            if ($request->quantity > $lot->soluong_hientai) {
                return back()->with('error', "Số lượng hủy ({$request->quantity}) vượt quá tồn kho của Lô ({$lot->soluong_hientai}).");
            }

            // Trừ lô
            $lot->soluong_hientai -= $request->quantity;
            if ($lot->soluong_hientai == 0) {
                $lot->trangthai = 'Hết hàng';
            }
            $lot->save();

            // Trừ tổng tồn kho vật lý
            $nguyenlieu->tonkho_thucte -= $request->quantity;
            $nguyenlieu->save();

            // Ghi Log
            LichSuKho::create([
                'id_nguyen_lieu' => $nguyenlieu->id,
                'loai_gd' => 'waste',
                'soluong' => -$request->quantity,
                'ghichu' => $request->reason . " [{$lot->malo} (-{$request->quantity})]",
                'manv' => Auth::guard('nhanvien')->user()->manv ?? null,
            ]);

            DB::commit();
            return back()->with('success', "Đã xuất hủy đích danh {$request->quantity} sản phẩm từ Lô {$lot->malo}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi xuất hủy: ' . $e->getMessage());
        }
    }



    // Truy vết lô hàng
    public function trace($id)
    {
        $lot = LoNguyenLieu::with(['nguyenLieu', 'phieuNhap.nhanVien'])->findOrFail($id);

        // Tìm các log có nhắc tới mã lô này trong Ghi chú
        $logs = LichSuKho::with('nhanvien')
            ->where('id_nguyen_lieu', $lot->id_nguyen_lieu)
            ->where('ghichu', 'like', "%{$lot->malo}%")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.longuyenlieu.trace', compact('lot', 'logs'));
    }
}
