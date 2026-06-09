<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhachHang;
use App\Models\HangThanhVien;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Voucher;
use App\Models\LichSuDiem;
use App\Models\KhachHangVoucher;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang Thông tin cá nhân (Profile)
     */
    public function index()
    {
        // Lấy thông tin user hiện tại kèm theo Hạng và các Voucher đã đổi
        $user = Auth::guard('khachhang')->user()->load(['hangThanhVien', 'vouchers']);

        // Tính toán thanh tiến trình lên hạng
        $nextTier = HangThanhVien::where('chi_tieu_toi_thieu', '>', $user->tong_chi_tieu)
                                ->orderBy('chi_tieu_toi_thieu', 'asc')
                                ->first();
        
        $percent = 100; // Mặc định là 100% (nếu đã đạt hạng cao nhất)
        if ($nextTier) {
            $currentTierMoc = $user->hangThanhVien ? $user->hangThanhVien->chi_tieu_toi_thieu : 0;
            $range = $nextTier->chi_tieu_toi_thieu - $currentTierMoc;
            $spentInThisTier = $user->tong_chi_tieu - $currentTierMoc;
            
            if ($range > 0) {
                $percent = ($spentInThisTier / $range) * 100;
            }
        }

        return view('auth.profile', compact('user', 'nextTier', 'percent'));
    }

    /**
     * Xử lý Cập nhật thông tin cơ bản (Tên, Số điện thoại, Địa chỉ)
     */
    public function updateProfile(Request $request)
    {
        // 1. Validate dữ liệu gửi lên
        $request->validate([
            'hoten' => 'required|string|max:255',
            'sdt' => 'required|numeric|digits_between:9,11',
            'diachi' => 'nullable|string|max:255', // Đã thêm địa chỉ
        ], [
            'hoten.required' => 'Vui lòng nhập họ tên.',
            'sdt.required' => 'Vui lòng nhập số điện thoại.',
            'sdt.numeric' => 'Số điện thoại chỉ được chứa chữ số.',
            'sdt.digits_between' => 'Số điện thoại không hợp lệ.',
        ]);

        // 2. Lấy Khách hàng hiện tại
        $user = Auth::guard('khachhang')->user();

        if ($user) {
            // 3. Cập nhật vào Database
            $user->update([
                'hoten' => $request->hoten,
                'sdt' => $request->sdt,
                'diachi' => $request->diachi,
            ]);

            return back()->with('success', 'Đã lưu thay đổi thông tin cá nhân thành công!');
        }

        return back()->with('error', 'Có lỗi xảy ra, không thể cập nhật.');
    }

    /**
     * Xử lý Đổi Mật Khẩu (Giữ lại dự phòng nếu sau này bạn muốn làm thêm chức năng đổi mật khẩu)
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = Auth::guard('khachhang')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    public function exchangeVoucher(Request $request)
    {
        $request->validate([
            'mavoucher' => 'required|exists:voucher,mavoucher',
        ]);

        $user = Auth::guard('khachhang')->user();
        $voucher = Voucher::where('mavoucher', $request->mavoucher)->firstOrFail();

        // 1. Kiểm tra điều kiện
        $alreadyHas = \App\Models\KhachHangVoucher::where('makh', $user->makh)->where('mavoucher', $voucher->mavoucher)->exists();
        if ($alreadyHas) {
            return back()->with('error', 'Bạn đã đổi mã giảm giá này rồi và không thể đổi thêm.');
        }

        if ($user->diem_thuong < $voucher->diem_doi) {
            return back()->with('error', 'Bạn không đủ điểm để đổi voucher này.');
        }

        if ($voucher->soluong <= $voucher->da_sudung) {
            return back()->with('error', 'Voucher này đã hết lượt đổi.');
        }

        // 2. Thực hiện giao dịch
        DB::beginTransaction();
        try {
            // Trừ điểm của khách
            $user->diem_thuong -= $voucher->diem_doi;
            $user->save();

            // Tạo bản ghi trong ví voucher
            KhachHangVoucher::create([
                'makh' => $user->makh,
                'mavoucher' => $voucher->mavoucher,
                'trang_thai' => 0 // Chưa dùng
            ]);

            // Tăng số lượng đã sử dụng của voucher
            $voucher->increment('da_sudung');

            // Ghi log lịch sử
            LichSuDiem::create([
                'makh' => $user->makh,
                'loai_giao_dich' => 'tru_diem',
                'so_diem' => $voucher->diem_doi,
                'mo_ta' => 'Đổi điểm lấy Voucher: ' . $voucher->mavoucher
            ]);

            DB::commit();
            return back()->with('success', 'Đổi voucher thành công! Hãy kiểm tra ví của bạn.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}