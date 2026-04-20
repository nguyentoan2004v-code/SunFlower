<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham;
use App\Models\DonHang;         // Import Model Đơn Hàng
use App\Models\ChiTietDonHang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function add($masp)
    {
        // 1. Dùng Model SanPham để lấy dữ liệu trực tiếp từ Database
        $product = SanPham::find($masp);

        // Kiểm tra nếu không tìm thấy sản phẩm
        if (!$product) {
            return back()->with('error', 'Sản phẩm không tồn tại!');
        }

        // 2. Lấy giỏ hàng từ session
        $cart = session()->get('cart', []);


        $gia_thuc_te = (!empty($product->giakm) && $product->giakm > 0) 
                        ? $product['giakm'] 
                        : $product['giaban'];

        // Nếu sản phẩm đã có, tăng số lượng
        if (isset($cart[$masp])) {
            $cart[$masp]['quantity']++; 
        } else {
            // Nếu chưa có, thêm mới vào mảng
            $cart[$masp] = [
                "name" => $product['tensp'],
                "quantity" => 1,
                "price" => $gia_thuc_te,         // Lấy giá thực tế (đã giảm)
                "old_price" => $product['giaban'], // Lưu thêm giá gốc (nếu sau này bạn muốn hiện gạch chéo trong giỏ hàng)
                "image" => $product['hinhanh']
            ];
        }

        // 4. Lưu lại vào session
        session()->put('cart', $cart);
        
        // Quay lại trang trước đó để khách hàng có thể mua tiếp
        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng!');
    }
    public function remove($masp)
    {
        // 1. Lấy giỏ hàng hiện tại từ session
        $cart = session()->get('cart', []);

        // 2. Kiểm tra nếu sản phẩm tồn tại trong giỏ thì xóa nó đi
        if (isset($cart[$masp])) {
            unset($cart[$masp]); // Hàm unset dùng để xóa 1 phần tử trong mảng
            
            // 3. Lưu mảng giỏ hàng mới (đã xóa) đè lên session cũ
            session()->put('cart', $cart);
            
            return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
        }

        // Nếu không tìm thấy sản phẩm trong giỏ
        return redirect()->back()->with('error', 'Không tìm thấy sản phẩm để xóa!');
    }
    public function update(Request $request)
        {
    $cart = session()->get('cart', []);
    if(isset($cart[$request->id])) {
        // Cập nhật số lượng mới
        $cart[$request->id]['quantity'] = $request->quantity;
        session()->put('cart', $cart);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thành công'
        ]);
    }
        }
    public function checkout(Request $request)
        {
    $selectedIds = $request->input('selected_items', []);
    if (empty($selectedIds)) {
        return back()->with('error', 'Vui lòng chọn ít nhất một đóa hoa để thanh toán!');
    }

    $cart = session()->get('cart');
    // Chỉ lọc ra những sản phẩm nằm trong danh sách được chọn
    $checkoutItems = array_intersect_key($cart, array_flip($selectedIds));
    
    // Lưu tạm danh sách mua này vào session riêng để sang trang thanh toán
    session()->put('checkout_data', $checkoutItems);

    return view('checkout', compact('checkoutItems'));
    }


    public function buyNow($masp)
    {
        $product = \App\Models\SanPham::find($masp);

        if (!$product) {
            return back()->with('error', 'Sản phẩm không tồn tại!');
        }

        // Kiểm tra giá thực tế (có KM hay không)
        $gia_thuc_te = (!empty($product->giakm) && $product->giakm > 0) 
                        ? $product->giakm 
                        : $product->giaban;

        // Tạo mảng dữ liệu thanh toán ĐỘC LẬP (chỉ chứa 1 món này)
        $checkoutItems = [
            $masp => [
                "name" => $product->tensp,
                "quantity" => 1,
                "price" => $gia_thuc_te,
                "image" => $product->hinhanh
            ]
        ];

        // Lưu vào session để trang Checkout đọc
        session()->put('checkout_data', $checkoutItems);

        // Chuyển thẳng tới giao diện thanh toán
        return view('checkout', compact('checkoutItems'));
    }

    public function placeOrder(Request $request)
    {
        // 1. Lấy danh sách sản phẩm khách đang muốn thanh toán
        $checkoutItems = session()->get('checkout_data');

        if (!$checkoutItems || count($checkoutItems) == 0) {
            return redirect()->route('cart.index')->with('error', 'Đơn hàng của bạn đã hết hạn hoặc không có sản phẩm.');
        }

        // Tính tổng tiền
        $tongTien = 0;
        foreach ($checkoutItems as $item) {
            $tongTien += $item['price'] * $item['quantity'];
        }

        // Bắt đầu Transaction để đảm bảo an toàn dữ liệu
        DB::beginTransaction();
        try {
            // 2. Tạo đơn hàng mới trong bảng donhang
            $donHang = new DonHang();
            
            // Nếu khách hàng đã đăng nhập, lưu mã khách hàng
            if (Auth::guard('khachhang')->check()) {
                // Đổi 'makh' thành tên cột khóa chính bảng khách hàng của bạn
                $donHang->makh = Auth::guard('khachhang')->user()->makh; 
            }
            
            $donHang->ten_nguoinhan = $request->ten_nguoinhan;
            $donHang->sdt_nguoinhan = $request->sdt_nguoinhan;
            $donHang->diachi_giaohang = $request->diachi_giaohang;
            $donHang->ghichu = $request->ghichu;
            $donHang->phuongthuc_thanhtoan = $request->phuongthuc_thanhtoan;
            $donHang->tongtien = $tongTien;
            $donHang->trangthai = 'Chờ xác nhận'; // Trạng thái mặc định
            $donHang->ngaydat = now();
            $donHang->save();

            // Lấy mã đơn hàng vừa tạo xong
            // Đổi 'madon' thành tên cột khóa chính bảng donhang của bạn
            $maDonHangVuaTao = $donHang->madon; 

            // 3. Lưu chi tiết từng sản phẩm vào bảng chitiet_donhang
            foreach ($checkoutItems as $id => $item) {
                $chiTiet = new ChiTietDonHang();
                $chiTiet->madon = $maDonHangVuaTao; // Nối với ID đơn hàng ở trên
                $chiTiet->masp = $id;               // Mã sản phẩm
                $chiTiet->soluong = $item['quantity'];
                $chiTiet->dongia = $item['price'];
                $chiTiet->thanhtien = $item['price'] * $item['quantity'];
                $chiTiet->save();
            }

            // 4. Dọn dẹp Giỏ hàng
            // Xóa các món đã mua khỏi mảng 'cart' chính
            $cart = session()->get('cart', []);
            foreach ($checkoutItems as $id => $item) {
                if (isset($cart[$id])) {
                    unset($cart[$id]);
                }
            }
            session()->put('cart', $cart);

            // Xóa session thanh toán tạm
            session()->forget('checkout_data');

            // Xác nhận lưu DB thành công
            DB::commit();

            // Chuyển hướng về trang chủ và báo thành công
            return redirect()->route('home')->with('success', '🎉 Đặt hoa thành công! SunFlower sẽ sớm liên hệ với bạn.');

        } catch (\Exception $e) {
            // Nếu có lỗi (vd: sai tên cột), hủy toàn bộ thao tác thêm DB ở trên
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage());
        }
    }
}