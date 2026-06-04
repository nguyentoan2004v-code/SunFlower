<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DanhGia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Thêm thư viện Auth

class DanhGiaController extends Controller
{
    // Hàm kiểm tra quyền: Chỉ Quản lý Cửa hàng mới được vào
    private function checkPermission()
    {
        $user = Auth::guard('nhanvien')->user();
        if (!$user->hasRole('Quản lý Cửa hàng')) {
            abort(403, 'Bạn không có quyền truy cập trang quản lý đánh giá.');
        }
    }

    public function index(Request $request)
    {
        $this->checkPermission(); // Gọi kiểm tra quyền

        $badProducts = DanhGia::with('sanPham')
            ->select('masp', DB::raw('count(*) as total_1_star'))
            ->where('so_sao', 1)
            ->groupBy('masp')
            ->orderByDesc('total_1_star')
            ->take(5)
            ->get();

        $query = DanhGia::with(['khachHang', 'sanPham'])->orderBy('created_at', 'desc');

        if ($request->has('star') && in_array($request->star, [1, 2, 3, 4, 5])) {
            $query->where('so_sao', $request->star);
        }
        
        if ($request->has('reply_status')) {
            if ($request->reply_status == 'replied') {
                $query->whereNotNull('phan_hoi');
            } elseif ($request->reply_status == 'unreplied') {
                $query->whereNull('phan_hoi');
            }
        }

        $reviews = $query->paginate(10)->withQueryString();

        return view('admin.danhgia.index', compact('reviews', 'badProducts'));
    }

    public function reply(Request $request, $id)
    {
        $this->checkPermission(); // Kiểm tra quyền
        
        $request->validate([
            'phan_hoi' => 'required|string|max:1000'
        ]);

        $review = DanhGia::findOrFail($id);
        $review->update([
            'phan_hoi' => $request->phan_hoi
        ]);

        return back()->with('success', 'Đã gửi phản hồi thành công!');
    }

    public function toggleStatus($id)
    {
        $this->checkPermission(); // Kiểm tra quyền

        $review = DanhGia::findOrFail($id);
        $review->update([
            'trang_thai' => !$review->trang_thai
        ]);

        $msg = $review->trang_thai ? 'Đã HIỂN THỊ lại đánh giá.' : 'Đã ẨN đánh giá vi phạm.';
        return back()->with('success', $msg);
    }

    public function destroy($id)
    {
        $this->checkPermission(); // Kiểm tra quyền

        $review = DanhGia::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Đã xóa vĩnh viễn đánh giá!');
    }
}