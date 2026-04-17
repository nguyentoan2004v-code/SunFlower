<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DonHang;
use App\Models\KhachHang; // Bổ sung Model này để tự tạo Khách vãng lai
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Requests\Api\CheckoutRequest; 

class DonHangController extends Controller
{
    public function checkout(CheckoutRequest $request)
    {
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
                    'makh' => 'KVL' . rand(10000, 99999), // KVL = Khách Vãng Lai
                    'hoten' => $request->hoten_nguoi_nhan,
                    'email' => $request->sdt_nhan . '@guest.sunflower.vn', // Email giả chống lỗi unique
                    'password' => Hash::make(Str::random(10)), // Mật khẩu ngẫu nhiên
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
            // --- BẮT ĐẦU LOGIC TỰ ĐỘNG SINH HÓA ĐƠN ---
            $mahd = 'HD' . rand(10000000, 99999999);
            $thueRate = 0.08; // Thuế VAT 8%
            $tienThue = $request->tongtien * $thueRate;

            $hoadon = HoaDon::create([
                'mahd' => $mahd,
                'madon' => $donhang->madon,
                'ngayxuat' => now(),
                'tongtien' => $request->tongtien + $tienThue,
                'thue' => $tienThue,
                'ptthanhtoan' => 'Tiền mặt' // Mặc định, FE có thể gửi lên sau
            ]);

            // Sao chép dữ liệu từ giỏ hàng sang chi tiết hóa đơn
            foreach ($request->cart as $item) {
                ChiTietHoaDon::create([
                    'mahd' => $mahd,
                    'masp' => $item['masp'],
                    'soluong' => $item['soluong'],
                    'dongia' => $item['dongia']
                ]);
            }
            // --- KẾT THÚC LOGIC ---

            return response()->json([
                'status' => 'success',
                'message' => 'Đặt hoa và xuất hóa đơn thành công!',
                'madon' => $donhang->madon,
                'mahd' => $mahd
            ], 201);

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
    /**
     * Lấy danh sách tất cả đơn hàng của Khách hàng đang đăng nhập
     * Phương thức: GET
     */
    public function myOrders(Request $request)
    {
        // Lấy mã khách hàng từ Thẻ từ (Token)
        $makh = $request->user()->makh;

        // Ma thuật Laravel: Lấy đơn hàng, sắp xếp mới nhất lên đầu,
        // và tự động gom luôn danh sách sản phẩm bên trong mỗi đơn hàng (nhờ hàm with)
        $donhangs = DonHang::with('sanphams')
                           ->where('makh', $makh)
                           ->orderBy('ngaydat', 'desc')
                           ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy lịch sử đơn hàng thành công',
            'data' => $donhangs
        ], 200);
    }

    /**
     * Xem chi tiết 1 đơn hàng cụ thể
     * Phương thức: GET
     */
    public function showOrderDetails(Request $request, $madon)
    {
        $makh = $request->user()->makh;

        // Tìm đơn hàng theo mã đơn VÀ phải là của đúng ông khách này (Bảo mật)
        $donhang = DonHang::with('sanphams')
                          ->where('madon', $madon)
                          ->where('makh', $makh)
                          ->first();

        if (!$donhang) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy đơn hàng hoặc bạn không có quyền xem'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $donhang
        ], 200);
    }
}
