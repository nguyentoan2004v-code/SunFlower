<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NguyenLieu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class NguyenLieuController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'check.kho.role',
        ];
    }

    /**
     * Danh sách nguyên liệu + tìm kiếm
     */
    public function index(Request $request)
    {
        $query = NguyenLieu::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ten_nl', 'like', "%{$search}%")
                  ->orWhere('dvt', 'like', "%{$search}%");
            });
        }

        if ($request->filled('trang_thai')) {
            switch ($request->trang_thai) {
                case 'het_hang':
                    $query->whereRaw('tonkho_thucte - tonkho_datruoc <= 0');
                    break;
                case 'sap_het':
                    $query->whereRaw('tonkho_thucte - tonkho_datruoc > 0')
                          ->whereRaw('tonkho_thucte - tonkho_datruoc <= tonkho_toithieu');
                    break;
                case 'con_hang':
                    $query->whereRaw('tonkho_thucte - tonkho_datruoc > tonkho_toithieu');
                    break;
            }
        }

        $nguyenlieus = $query->orderBy('ten_nl', 'asc')->paginate(15)->withQueryString();

        // Thống kê tổng quan
        $stats = [
            'tong_nguyen_lieu' => NguyenLieu::count(),
            'het_hang'         => NguyenLieu::whereRaw('tonkho_thucte - tonkho_datruoc <= 0')->count(),
            'sap_het'          => NguyenLieu::whereRaw('tonkho_thucte - tonkho_datruoc > 0')
                                         ->whereRaw('tonkho_thucte - tonkho_datruoc <= tonkho_toithieu')->count(),
        ];

        return view('admin.nguyenlieu.index', compact('nguyenlieus', 'stats'));
    }

    /**
     * Form thêm nguyên liệu
     */
    public function create()
    {
        return view('admin.nguyenlieu.create');
    }

    /**
     * Lưu nguyên liệu mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten_nl'       => 'required|string|max:100|unique:nguyen_lieu,ten_nl',
            'dvt'          => 'required|string|max:20',
            'gia_von'      => 'nullable|numeric|min:0',
        ], [
            'ten_nl.required' => 'Vui lòng nhập tên nguyên liệu.',
            'ten_nl.unique'   => 'Tên nguyên liệu này đã tồn tại.',
            'dvt.required'    => 'Vui lòng nhập đơn vị tính.',
        ]);

        NguyenLieu::create([
            'ten_nl'          => $request->ten_nl,
            'dvt'             => $request->dvt,
            'tonkho_thucte'   => 0,
            'tonkho_datruoc'  => 0,
            'tonkho_toithieu' => 0,
            'gia_von'         => $request->gia_von ?? 0,
        ]);

        return redirect()->route('admin.nguyenlieu.index')->with('success', 'Đã thêm nguyên liệu "' . $request->ten_nl . '" thành công!');
    }

    /**
     * Form sửa nguyên liệu
     */
    public function edit($id)
    {
        $nguyenlieu = NguyenLieu::findOrFail($id);
        return view('admin.nguyenlieu.edit', compact('nguyenlieu'));
    }

    /**
     * Cập nhật nguyên liệu
     */
    public function update(Request $request, $id)
    {
        $nguyenlieu = NguyenLieu::findOrFail($id);

        $request->validate([
            'ten_nl'       => 'required|string|max:100|unique:nguyen_lieu,ten_nl,' . $id,
            'dvt'          => 'required|string|max:20',
            'gia_von'      => 'nullable|numeric|min:0',
        ]);

        $nguyenlieu->update([
            'ten_nl'       => $request->ten_nl,
            'dvt'          => $request->dvt,
            'gia_von'      => $request->gia_von ?? 0,
        ]);

        return redirect()->route('admin.nguyenlieu.index')->with('success', 'Đã cập nhật nguyên liệu thành công!');
    }

    /**
     * Xóa nguyên liệu (restrict nếu đang trong BOM hoặc đơn hàng)
     */
    public function destroy($id)
    {
        $nguyenlieu = NguyenLieu::findOrFail($id);

        if ($nguyenlieu->sanphams()->count() > 0) {
            return back()->with('error', 'Không thể xóa nguyên liệu đang được sử dụng trong công thức sản phẩm!');
        }

        if ($nguyenlieu->chiTietDonHangNguyenLieus()->count() > 0) {
            return back()->with('error', 'Không thể xóa nguyên liệu đang liên kết với đơn hàng!');
        }

        $nguyenlieu->delete();
        return redirect()->route('admin.nguyenlieu.index')->with('success', 'Đã xóa nguyên liệu thành công!');
    }

    /**
     * API: Lấy thông tin nguyên liệu (dùng cho AJAX trong BOM Builder)
     */
    public function getInfo($id)
    {
        $nguyenlieu = NguyenLieu::find($id);
        if (!$nguyenlieu) {
            return response()->json(['error' => 'Không tìm thấy nguyên liệu'], 404);
        }

        return response()->json([
            'id'              => $nguyenlieu->id,
            'ten_nl'          => $nguyenlieu->ten_nl,
            'dvt'             => $nguyenlieu->dvt,
            'available_stock' => $nguyenlieu->available_stock,
            'gia_von'         => $nguyenlieu->gia_von,
        ]);
    }
}
