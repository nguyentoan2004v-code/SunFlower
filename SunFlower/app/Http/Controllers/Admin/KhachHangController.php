<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KhachHang;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class KhachHangController extends Controller
{
    // Kiểm tra quyền (Chỉ Quản lý Cửa hàng mới được vào)
    private function checkPermission()
    {
        $user = Auth::guard('nhanvien')->user();
        if (!$user->hasRole('Quản lý Cửa hàng')) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }
    }

    // 1. Danh sách khách hàng
    public function index()
    {
        $this->checkPermission();
        $khachhangs = KhachHang::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.khachhang.index', compact('khachhangs'));
    }

    // 2. Form sửa thông tin
    public function edit($makh)
    {
        $this->checkPermission();
        $khachhang = KhachHang::where('makh', $makh)->firstOrFail();
        return view('admin.khachhang.edit', compact('khachhang'));
    }

    // 3. Xử lý cập nhật thông tin
    public function update(Request $request, $makh)
    {
        $this->checkPermission();
        $khachhang = KhachHang::where('makh', $makh)->firstOrFail();

        $request->validate([
            'hoten' => 'required|string|max:40',
            'sdt' => 'required|string|max:15|unique:khachhang,sdt,' . $makh . ',makh',
            'diachi' => 'nullable|string|max:100',
            'ngaysinh' => 'nullable|date',
        ]);

        $khachhang->update([
            'hoten' => $request->hoten,
            'sdt' => $request->sdt,
            'diachi' => $request->diachi,
            'ngaysinh' => $request->ngaysinh,
        ]);

        return redirect()->route('admin.khachhang.index')->with('success', 'Cập nhật thông tin khách hàng thành công!');
    }

    // 4. Xóa khách hàng
    public function destroy($makh)
    {
        $this->checkPermission();
        $khachhang = KhachHang::where('makh', $makh)->firstOrFail();

        // Kiểm tra xem khách hàng có đơn hàng nào không (bảo toàn dữ liệu hóa đơn)
        if ($khachhang->donhangs()->count() > 0) {
            return back()->with('error', 'Không thể xóa khách hàng này vì họ đã có lịch sử đặt hàng.');
        }

        $khachhang->delete();
        return redirect()->route('admin.khachhang.index')->with('success', 'Xóa khách hàng thành công!');
    }

    // 5. Đặt lại mật khẩu (Reset Password)
    public function resetPassword($makh)
    {
        $this->checkPermission();
        $khachhang = KhachHang::where('makh', $makh)->firstOrFail();
        
        $newPassword = 'password123'; // Mật khẩu mặc định khi reset
        $khachhang->update([
            'password' => Hash::make($newPassword)
        ]);

        return back()->with('success', 'Đã đặt lại mật khẩu cho tài khoản ' . $khachhang->hoten . ' thành: ' . $newPassword);
    }

    // 6. Xem lịch sử đơn hàng của khách
    public function history($makh)
    {
        $this->checkPermission();
        $khachhang = KhachHang::where('makh', $makh)->firstOrFail();
        $donhangs = DonHang::where('makh', $makh)->orderBy('ngaydat', 'desc')->paginate(10);
        
        return view('admin.khachhang.history', compact('khachhang', 'donhangs'));
    }
}