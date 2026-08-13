<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhieuNhapKho;
use App\Models\ChiTietPhieuNhap;
use App\Models\NguyenLieu;
use App\Models\NhaCungCap;
use App\Models\LichSuKho;
use App\Models\LoNguyenLieu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PhieuNhapKhoController extends Controller
{
    public function index(Request $request)
    {
        $query = PhieuNhapKho::with(['nhaCungCap', 'nhanVien'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('trangthai', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('maphieu', 'like', '%' . $request->search . '%');
        }

        $phieunhaps = $query->paginate(10);
        return view('admin.phieunhapkho.index', compact('phieunhaps'));
    }

    public function create()
    {
        $nhaCungCaps = NhaCungCap::all();
        $nguyenlieus = NguyenLieu::orderBy('ten_nl')->get();
        return view('admin.phieunhapkho.create', compact('nhaCungCaps', 'nguyenlieus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_nhacungcap' => 'required|exists:nha_cung_cap,id',
            'id_nguyen_lieus' => 'required|array|min:1',
            'id_nguyen_lieus.*' => 'required|exists:nguyen_lieu,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
            'dongias' => 'required|array|min:1',
            'dongias.*' => 'required|numeric|min:0',
            'hsds' => 'nullable|array',
            'ghichu' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Generate Code: PN-YYYYMMDD-XXX
            $datePrefix = 'PN-' . date('Ymd') . '-';
            $lastPhieu = PhieuNhapKho::where('maphieu', 'like', $datePrefix . '%')
                                    ->orderBy('maphieu', 'desc')
                                    ->first();
            
            $nextNumber = 1;
            if ($lastPhieu) {
                $lastNumber = (int) substr($lastPhieu->maphieu, -3);
                $nextNumber = $lastNumber + 1;
            }
            $code = $datePrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Tạm thời tính tổng tiền
            $totalAmount = 0;

            $phieuNhap = PhieuNhapKho::create([
                'maphieu' => $code,
                'id_nhacungcap' => $request->id_nhacungcap,
                'manv' => Auth::guard('nhanvien')->user()->manv,
                'ghichu' => $request->ghichu,
                'trangthai' => 'Nháp',
                'tongtien' => 0 // Sẽ cập nhật sau
            ]);

            $details = [];
            for ($i = 0; $i < count($request->id_nguyen_lieus); $i++) {
                $materialId = $request->id_nguyen_lieus[$i];
                $quantity = $request->quantities[$i];
                $unitPrice = $request->dongias[$i];
                $subtotal = $quantity * $unitPrice;
                $expiryDate = $request->hsds[$i] ?? null;

                // Tự sinh mã Lô mặc định nếu muốn, hoặc frontend gửi lên
                // LOT-{MaterialID}-{Ymd}
                $lotNumber = 'LOT-' . $materialId . '-' . date('ymd');

                $details[] = [
                    'id_phieu_nhap' => $phieuNhap->id,
                    'id_nguyen_lieu' => $materialId,
                    'soluong' => $quantity,
                    'dongia' => $unitPrice,
                    'thanhtien' => $subtotal,
                    'malo' => $lotNumber,
                    'hsd' => $expiryDate,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $totalAmount += $subtotal;
            }

            ChiTietPhieuNhap::insert($details);

            $phieuNhap->update(['tongtien' => $totalAmount]);

            DB::commit();
            return redirect()->route('admin.phieunhapkho.show', $phieuNhap->id)
                             ->with('success', 'Đã tạo Phiếu Nhập Kho (bản Nháp) thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi tạo phiếu nhập: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $phieuNhap = PhieuNhapKho::with(['chiTiet.nguyenLieu', 'nhaCungCap', 'nhanVien'])->findOrFail($id);
        return view('admin.phieunhapkho.show', compact('phieuNhap'));
    }

    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $phieuNhap = PhieuNhapKho::with('chiTiet')->lockForUpdate()->findOrFail($id);

            if ($phieuNhap->trangthai !== 'Nháp') {
                throw new \Exception('Chỉ có thể duyệt Phiếu nhập ở trạng thái Nháp.');
            }

            foreach ($phieuNhap->chiTiet as $detail) {
                $material = NguyenLieu::lockForUpdate()->findOrFail($detail->id_nguyen_lieu);
                
                // Cập nhật Tồn kho thực tế
                $oldQty = $material->tonkho_thucte;
                $oldPrice = $material->gia_von;
                $newQty = $detail->soluong;
                $newPrice = $detail->dongia;

                $material->tonkho_thucte += $newQty;

                // Tính Giá vốn bình quân (Moving Average Cost)
                if ($oldQty + $newQty > 0) {
                    $newCostPrice = (($oldQty * $oldPrice) + ($newQty * $newPrice)) / ($oldQty + $newQty);
                    $material->gia_von = $newCostPrice;
                }

                $material->save();

                // Sinh ra 1 record Lô Nguyên Liệu (Lot Tracking)
                LoNguyenLieu::create([
                    'id_nguyen_lieu'          => $material->id,
                    'id_phieu_nhap'           => $phieuNhap->id,
                    'id_chitiet_phieu_nhap'   => $detail->id,
                    'malo'           => $detail->malo,
                    'soluong_bandau' => $newQty,
                    'soluong_hientai' => $newQty,
                    'dongia'         => $newPrice,
                    'hsd'            => $detail->hsd,
                    'trangthai'      => 'Còn hàng',
                ]);

                // Ghi Log Nhập kho
                LichSuKho::create([
                    'id_nguyen_lieu' => $material->id,
                    'loai_gd' => 'import',
                    'soluong' => $newQty,
                    'ghichu' => 'Nhập kho từ phiếu ' . $phieuNhap->maphieu,
                    'manv' => Auth::guard('nhanvien')->user()->manv,
                ]);
            }

            $phieuNhap->update(['trangthai' => 'Hoàn thành']);

            DB::commit();
            return redirect()->route('admin.phieunhapkho.show', $id)
                             ->with('success', 'Duyệt Phiếu Nhập Kho thành công! Hàng đã được cộng vào kho.');
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi duyệt phiếu: ' . $e->getMessage());
        }
    }

    public function cancel($id)
    {
        $phieuNhap = PhieuNhapKho::findOrFail($id);
        
        if ($phieuNhap->trangthai !== 'Nháp') {
            return back()->with('error', 'Chỉ có thể hủy phiếu đang ở trạng thái Nháp.');
        }

        $phieuNhap->update(['trangthai' => 'Đã hủy']);

        return redirect()->route('admin.phieunhapkho.show', $id)
                         ->with('success', 'Đã hủy Phiếu Nhập Kho.');
    }
}
