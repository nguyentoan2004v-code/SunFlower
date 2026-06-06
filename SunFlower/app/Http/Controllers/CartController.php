<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham;
use App\Models\DonHang;         // Import Model Đơn Hàng
use App\Models\ChiTietDonHang;
use App\Models\LoHang;          // Thêm Model LoHang
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; 
use App\Models\Voucher;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, $masp)
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

        // Lấy số lượng từ Request URL (Mặc định là 1 nếu không có)
        $quantity = (int) $request->input('quantity', 1);
        if ($quantity < 1) $quantity = 1;

        // Nếu sản phẩm đã có, tăng số lượng
        if (isset($cart[$masp])) {
            $cart[$masp]['quantity'] += $quantity; 
        } else {
            // Nếu chưa có, thêm mới vào mảng
            $cart[$masp] = [
                "name" => $product['tensp'],
                "quantity" => $quantity,
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
        $quantities = $request->input('quantities', []);
        $cart = session()->get('cart', []);

        foreach ($quantities as $id => $qty) {
            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = (int)$qty;
            }
        }
        session()->put('cart', $cart);
        $selectedIds = $request->input('selected_items', []);
        if (empty($selectedIds)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một đóa hoa để thanh toán!');
        }

        $cart = session()->get('cart');
        // Chỉ lọc ra những sản phẩm nằm trong danh sách được chọn
        $checkoutItems = array_intersect_key($cart, array_flip($selectedIds));
        
        // Lưu tạm danh sách mua này vào session riêng để sang trang thanh toán
        session()->put('checkout_data', $checkoutItems);
        
        $usedVouchers = [];
        if (Auth::guard('khachhang')->check()) {
            $makh = Auth::guard('khachhang')->user()->makh;
            $usedVouchers = \App\Models\DonHang::where('makh', $makh)
                ->whereNotNull('mavoucher')
                ->where('trangthai', '!=', 'Đã hủy')
                ->pluck('mavoucher')
                ->toArray();
        }

        $publicVouchers = \App\Models\Voucher::where('trangthai', 1)
            ->where('hien_thi', 'cong_khai')
            ->where('ngay_bd', '<=', now())
            ->where('ngay_kt', '>=', now())
            ->whereRaw('da_sudung < soluong')
            ->whereNotIn('mavoucher', $usedVouchers)
            ->get();

        return view('checkout', compact('checkoutItems', 'publicVouchers'));
    }


    public function buyNow(Request $request, $masp)
    {
        $product = \App\Models\SanPham::find($masp);

        if (!$product) {
            return back()->with('error', 'Sản phẩm không tồn tại!');
        }

        // Kiểm tra giá thực tế (có KM hay không)
        $gia_thuc_te = (!empty($product->giakm) && $product->giakm > 0) 
                        ? $product->giakm 
                        : $product->giaban;

        // Lấy số lượng từ Request URL
        $quantity = (int) $request->query('quantity', 1);
        if ($quantity < 1) $quantity = 1;

        // Tạo mảng dữ liệu thanh toán ĐỘC LẬP (chỉ chứa 1 món này)
        $checkoutItems = [
            $masp => [
                "name" => $product->tensp,
                "quantity" => $quantity,
                "price" => $gia_thuc_te,
                "image" => $product->hinhanh
            ]
        ];

        // Lưu vào session để trang Checkout đọc
        session()->put('checkout_data', $checkoutItems);

        $usedVouchers = [];
        if (Auth::guard('khachhang')->check()) {
            $makh = Auth::guard('khachhang')->user()->makh;
            $usedVouchers = \App\Models\DonHang::where('makh', $makh)
                ->whereNotNull('mavoucher')
                ->where('trangthai', '!=', 'Đã hủy')
                ->pluck('mavoucher')
                ->toArray();
        }

        $publicVouchers = \App\Models\Voucher::where('trangthai', 1)
            ->where('hien_thi', 'cong_khai')
            ->where('ngay_bd', '<=', now())
            ->where('ngay_kt', '>=', now())
            ->whereRaw('da_sudung < soluong')
            ->whereNotIn('mavoucher', $usedVouchers)
            ->get();
        // Chuyển thẳng tới giao diện thanh toán
        return view('checkout', compact('checkoutItems', 'publicVouchers'));
    }

    public function placeOrder(Request $request)
    {

        $request->validate([
        'ten_nguoinhan'   => 'required|string|min:2|max:100',
        'sdt_nguoinhan'   => 'required|digits:10',
        'diachi_giaohang' => 'required|string|min:5|max:255',
        'ghichu'          => 'nullable|string|max:500',
        ], [
        'ten_nguoinhan.required'   => 'Vui lòng nhập tên người nhận.',
        'ten_nguoinhan.min'        => 'Tên người nhận phải có ít nhất 2 ký tự.',
        'sdt_nguoinhan.required'   => 'Vui lòng nhập số điện thoại.',
        'sdt_nguoinhan.digits' => 'Số điện thoại không hợp lệ (10 số).',
        'diachi_giaohang.required' => 'Vui lòng nhập địa chỉ giao hàng.',
        'diachi_giaohang.min'      => 'Địa chỉ phải có ít nhất 5 ký tự.',
        ]);
        $checkoutItems = session()->get('checkout_data');

        if (!$checkoutItems || count($checkoutItems) == 0) {
            return redirect()->route('cart.index')->with('error', 'Đơn hàng của bạn đã hết hạn hoặc không có sản phẩm.');
        }

        $tongTien = 0;
        foreach ($checkoutItems as $item) {
            $tongTien += $item['price'] * $item['quantity'];
        }
        $tienGiam = 0;
        $maVoucherCode = null;
        if (session()->has('voucher')) {
            $tienGiam = session('voucher')['tien_giam'];
            $maVoucherCode = session('voucher')['mavoucher'];
        }

        DB::beginTransaction();
        try {
            $donHang = new DonHang();
            
            // 1. Sinh mã đơn hàng (Độ dài 10 ký tự)
            $maDonMoi = 'DH-' . strtoupper(Str::random(7));
            $donHang->madon = $maDonMoi;
            
            // ==========================================
            // 2. XỬ LÝ LOGIC KHÁCH HÀNG (CÓ/KHÔNG ĐĂNG NHẬP)
            // ==========================================
            if (Auth::guard('khachhang')->check()) {
                // TRƯỜNG HỢP 1: Khách đã đăng nhập
                $donHang->makh = Auth::guard('khachhang')->user()->makh; 
            } else {
                // TRƯỜNG HỢP 2: Khách vãng lai (Chưa đăng nhập)
                // Tìm xem SĐT này đã từng mua hàng bao giờ chưa
                $khachTonTai = \App\Models\KhachHang::where('sdt', $request->sdt_nguoinhan)->first();
                
                if ($khachTonTai) {
                    // Nếu SĐT đã tồn tại, lấy mã khách hàng cũ gắn vào đơn này
                    $donHang->makh = $khachTonTai->makh;
                } else {
                    // Nếu SĐT hoàn toàn mới, tự động tạo hồ sơ khách hàng mới
                    $khachMoi = new \App\Models\KhachHang();
                    
                    // Tạo mã KH ngẫu nhiên 10 ký tự (Ví dụ: KVL-A1B2C3D)
                    $maKhMoi = 'KVL' . strtoupper(Str::random(7)); 
                    
                    $khachMoi->makh = $maKhMoi;
                    $khachMoi->hoten = $request->ten_nguoinhan; // Form gửi lên là ten_nguoinhan, lưu vào cột hoten
                    $khachMoi->sdt = $request->sdt_nguoinhan;
                    $khachMoi->diachi = $request->diachi_giaohang;
                    
                    $khachMoi->email = $request-> sdt_nguoinhan . '@gmail.com'; 
                    $khachMoi->password = bcrypt('123456');
                    
                    
                    $khachMoi->save(); // Lưu khách hàng mới vào DB
                    
                    // Gắn mã khách hàng vừa tạo cho đơn hàng
                    $donHang->makh = $maKhMoi;
                }
            }
            // ==========================================
            
            // 3. Khớp cột bảng donhang
            $donHang->sdt_nhan = $request->sdt_nguoinhan;         
            $donHang->diachi_giao = $request->diachi_giaohang;    
            $donHang->ghichu = $request->ghichu;
            
            $donHang->mavoucher = $maVoucherCode;
            $donHang->tiengiam = $tienGiam;
            $donHang->tongtien = max(0, $tongTien - $tienGiam);
            $donHang->trangthai = 'Chờ xác nhận';
            $donHang->ngaydat = now();
            
            $donHang->save(); // Lưu đơn hàng thành công!
            if ($maVoucherCode) {
                $vc = Voucher::find($maVoucherCode);
                if ($vc) {
                    $vc->increment('da_sudung');
                }
            }
            
            // ==========================================
            // 4. Lưu chi tiết đơn hàng VÀ TRỪ TỒN KHO THEO LÔ (ĐÃ SỬA)
            // ==========================================
            foreach ($checkoutItems as $id => $item) {
                // A. Lưu chi tiết đơn hàng
                $chiTiet = new ChiTietDonHang();
                $chiTiet->madon = $maDonMoi; 
                $chiTiet->masp = $id;               
                $chiTiet->soluong = $item['quantity'];
                $chiTiet->giaban = $item['price']; 
                $chiTiet->save();

                // B. LOGIC TRỪ TỒN KHO TRONG BẢNG LO_HANG (FIFO)
                $qtyNeeded = $item['quantity']; // Số lượng khách mua cần trừ

                // Lấy các lô hàng của sản phẩm này đang còn tồn kho (> 0)
                // Ưu tiên lô sắp hết hạn ra bán trước (ngayhethan ASC)
                $loHangs = LoHang::where('masp', $id)
                            ->where('soluong_ton', '>', 0)
                            ->orderBy('ngayhethan', 'asc')
                            ->get();

                // Tùy chọn: Chặn nếu tổng tồn kho không đủ (Có thể bỏ nếu muốn bán âm)
                if ($loHangs->sum('soluong_ton') < $qtyNeeded) {
                    throw new \Exception('Sản phẩm ' . $item['name'] . ' không đủ số lượng trong kho!');
                }

                foreach ($loHangs as $loHang) {
                    if ($qtyNeeded <= 0) {
                        break; // Đã trừ đủ số lượng khách mua, thoát vòng lặp
                    }

                    if ($loHang->soluong_ton >= $qtyNeeded) {
                        // Nếu số tồn của lô này ĐỦ để trừ
                        $loHang->soluong_ton -= $qtyNeeded;
                        $loHang->save();
                        $qtyNeeded = 0; // Đã trừ xong
                    } else {
                        // Nếu số tồn của lô này KHÔNG ĐỦ
                        $qtyNeeded -= $loHang->soluong_ton;
                        $loHang->soluong_ton = 0;
                        $loHang->save();
                    }
                }
            }
            // ==========================================

            // Dọn dẹp session
            $cart = session()->get('cart', []);
            foreach ($checkoutItems as $id => $item) {
                if (isset($cart[$id])) unset($cart[$id]);
            }
            session()->put('cart', $cart);
            session()->forget('checkout_data');
            session()->forget('voucher');

            $viewedOrders = session()->get('viewed_orders', []);
            $viewedOrders[] = $maDonMoi;
            session()->put('viewed_orders', $viewedOrders);
            
            DB::commit();

            return redirect()->route('checkout.success')->with('madon_moi', $maDonMoi);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function orderSuccess()
    {
        // Lấy mã đơn từ session (do placeOrder truyền sang)
        $maDon = session('madon_moi');

        // Nếu không có mã đơn (ai đó gõ trực tiếp url /dat-hang-thanh-cong), đẩy về trang chủ
        if (!$maDon) {
            return redirect()->route('home');
        }

        return view('checkout_success', compact('maDon'));
    }
    public function applyVoucher(Request $request)
    {
        // 1. Kiểm tra đăng nhập (Bắt buộc)
        if (!Auth::guard('khachhang')->check()) {
            return back()->with('error', 'Vui lòng đăng nhập để sử dụng mã giảm giá!');
        }

        $mavoucher = strtoupper($request->mavoucher);
        $voucher = Voucher::with('danhmucs')->where('mavoucher', $mavoucher)->where('trangthai', 1)->first();

        // 2. Kiểm tra mã tồn tại và thời hạn
        if (!$voucher || now() < $voucher->ngay_bd || now() > $voucher->ngay_kt) {
            return back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn!');
        }

        // 3. Kiểm tra số lượng
        if ($voucher->soluong > 0 && $voucher->da_sudung >= $voucher->soluong) {
            return back()->with('error', 'Mã giảm giá đã hết lượt sử dụng!');
        }

        // 4. Kiểm tra xem người này đã dùng mã này chưa (Chặn 1 người dùng 1 mã nhiều lần)
        $makh = Auth::guard('khachhang')->user()->makh;
        $daDung = DonHang::where('makh', $makh)
                         ->where('mavoucher', $mavoucher)
                         ->where('trangthai', '!=', 'Đã hủy')
                         ->exists();
        if ($daDung) {
            return back()->with('error', 'Bạn đã sử dụng mã giảm giá này rồi (Mỗi khách chỉ được dùng 1 lần)!');
        }

        // Lấy dữ liệu sản phẩm CHUẨN BỊ THANH TOÁN
        $checkoutItems = session()->get('checkout_data', []);
        if (empty($checkoutItems)) {
            return back()->with('error', 'Không có sản phẩm nào để áp dụng!');
        }

        // 5. Tính toán tổng tiền hợp lệ (dựa trên Danh mục hoặc Tất cả)
        $tongTienHopLe = 0;
        if ($voucher->loai_ap_dung === 'tat_ca') {
            foreach ($checkoutItems as $item) {
                $tongTienHopLe += $item['price'] * $item['quantity'];
            }
        } else {
            // Loại danh mục: Quét từng sản phẩm xem có khớp danh mục cho phép không
            $danhMucIds = $voucher->danhmucs->pluck('madm')->toArray();
            foreach ($checkoutItems as $id => $item) {
                $sp = SanPham::find($id);
                if ($sp && in_array($sp->madm, $danhMucIds)) {
                    $tongTienHopLe += $item['price'] * $item['quantity'];
                }
            }
        }

        // 6. Kiểm tra các điều kiện cuối cùng
        if ($tongTienHopLe == 0) {
             return back()->with('error', 'Sản phẩm bạn mua không thuộc danh mục được áp dụng mã này!');
        }
        if ($tongTienHopLe < $voucher->don_min) {
            return back()->with('error', 'Các sản phẩm hợp lệ chưa đạt giá trị tối thiểu (' . number_format($voucher->don_min, 0, ',', '.') . 'đ) để dùng mã này!');
        }

        // 7. Tiến hành tính số tiền thực tế được giảm
        $tienGiam = 0;
        if ($voucher->loai_giam === 'so_tien') {
            $tienGiam = $voucher->gia_tri_giam;
        } else {
            $tienGiam = $tongTienHopLe * ($voucher->gia_tri_giam / 100);
            if ($voucher->giam_max && $tienGiam > $voucher->giam_max) {
                $tienGiam = $voucher->giam_max;
            }
        }
        
        // Tránh lỗi giảm lố làm tổng đơn bị âm
        $tongThanhToan = 0;
        foreach ($checkoutItems as $item) {
            $tongThanhToan += $item['price'] * $item['quantity'];
        }
        if($tienGiam > $tongThanhToan) {
            $tienGiam = $tongThanhToan;
        }

        // Lưu mã và số tiền giảm vào Session để chuẩn bị xuất ra View
        session()->put('voucher', [
            'mavoucher' => $voucher->mavoucher,
            'tien_giam' => $tienGiam
        ]);

        return back()->with('success', 'Đã áp dụng mã giảm giá thành công!');
    }

    public function removeVoucher()
    {
        session()->forget('voucher');
        return back()->with('success', 'Đã gỡ mã giảm giá!');
    }
}