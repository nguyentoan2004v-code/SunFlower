<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DonHang;
use App\Models\KhachHang; // Bổ sung Model này để tự tạo Khách vãng lai
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DonHangController extends Controller
{
    public function checkout(Request $request)
    {
        // 1. Validate Dữ liệu 
        // Bổ sung thêm 'hoten_nguoi_nhan' vì khách vãng lai chưa có tên trong hệ thống
        $request->validate([
            'hoten_nguoi_nhan' => 'required|string', 
            'sdt_nhan' => 'required|string',
            'diachi_giao' => 'required|string',
            'tongtien' => 'required|numeric',
            'cart' => 'required|array', 
        ]);

        // 2. PHÂN LOẠI KHÁCH HÀNG (Logic cốt lõi của Hybrid)
        // Lệnh này sẽ âm thầm lục túi khách xem có Token không, không có thì trả về null
        $user = auth('sanctum')->user(); 
        $makh = null;

        if ($user) {
            // Trường hợp 1: Khách Thành viên (Đã đăng nhập)
            $makh = $user->makh;
            // TỰ ĐỘNG CẬP NHẬT HỒ SƠ NẾU KHÁCH CHƯA CÓ THÔNG TIN
            if (empty($user->diachi)) {
                $user->update(['diachi' => $request->diachi_giao]);
            }
            if (empty($user->sdt)) {
                $user->update(['sdt' => $request->sdt_nhan]);
            }
        } else {
            // Trường hợp 2: Khách Vãng lai (Không đăng nhập)
            // Tìm xem SĐT này đã từng mua chưa? Nếu chưa thì tự động tạo một User Ẩn.
            $khach_vang_lai = KhachHang::firstOrCreate(
                ['sdt' => $request->sdt_nhan], // Tìm theo số điện thoại
                [
                    // Nếu chưa có, tự tạo mới với thông tin mặc định
                    'makh' => 'KVL' . rand(10000, 99999), // KVL = Khách Vãng Lai (Giới hạn độ dài để không dính lỗi cũ)
                    'hoten' => $request->hoten_nguoi_nhan,
                    'email' => $request->sdt_nhan . '@guest.sunflower.vn', // Email giả chống lỗi unique
                    'password' => Hash::make(Str::random(10)), // Mật khẩu ngẫu nhiên không ai biết
                ]
            );
            $makh = $khach_vang_lai->makh;
        }

        // 3. TIẾN HÀNH ĐẶT HÀNG BẰNG TRANSACTION NHƯ CŨ
        DB::beginTransaction();

        try {
            $donhang = DonHang::create([
                'madon' => 'DH' . rand(10000000, 99999999),
                'makh' => $makh, // Dùng mã khách hàng vừa phân loại ở trên
                'ngaydat' => now(),
                'tongtien' => $request->tongtien,
                'diachi_giao' => $request->diachi_giao,
                'sdt_nhan' => $request->sdt_nhan,
                'ghichu' => $request->ghichu,
                'trangthai' => 'Chờ duyệt',
            ]);

            $chiTietData = [];
            foreach ($request->cart as $item) {
                $chiTietData[$item['masp']] = [
                    'soluong' => $item['soluong'],
                    'giaban' => $item['dongia'] 
                ];
            }

            $donhang->sanphams()->attach($chiTietData);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Đặt hoa thành công! Đơn hàng của bạn đang được xử lý.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Thanh toán thất bại: ' . $e->getMessage()
            ], 500);
        }
    }
}
