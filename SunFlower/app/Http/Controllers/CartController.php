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
        $request->validate([
            'id' => 'required',
            'quantity' => 'required|integer|min:1|max:1000'
        ]);

        $cart = session()->get('cart', []);
        if(isset($cart[$request->id])) {
            // Cập nhật số lượng mới
            $cart[$request->id]['quantity'] = (int) $request->quantity;
            session()->put('cart', $cart);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật thành công'
            ]);
        }
    }
    
    public function checkout(Request $request)
    {
        if ($request->isMethod('post')) {
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
        } else {
            // GET request (chuyển hướng quay lại từ lỗi validation đặt hàng hoặc áp dụng voucher)
            $checkoutItems = session()->get('checkout_data', []);
            if (empty($checkoutItems)) {
                return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất một đóa hoa để thanh toán!');
            }
        }
        
        $usedVouchers = [];
        $myVoucherCodes = [];
        
        if (Auth::guard('khachhang')->check()) {
            $makh = Auth::guard('khachhang')->user()->makh;
            
            // Lấy danh sách các mã voucher hệ thống thông thường đã dùng
            $usedVouchers = \App\Models\DonHang::where('makh', $makh)
                ->whereNotNull('mavoucher')
                ->where('trangthai', '!=', 'Đã hủy')
                ->pluck('mavoucher')
                ->toArray();

            // [MỚI] Lấy danh sách mã voucher đổi điểm ĐANG SỞ HỮU TRONG VÍ (chưa dùng)
            $myVoucherCodes = \DB::table('khachhang_voucher')
                ->where('makh', $makh)
                ->where('trang_thai', 0)
                ->pluck('mavoucher')
                ->toArray();
        }

        // [TỐI ƯU] Gộp cả Voucher công khai miễn phí VÀ Voucher cá nhân đã đổi bằng điểm
        $publicVouchers = \App\Models\Voucher::where('trangthai', 1)
            ->where('ngay_bd', '<=', now())
            ->where('ngay_kt', '>=', now())
            ->where(function($query) use ($usedVouchers, $myVoucherCodes) {
                // Trường hợp 1: Mã miễn phí công khai của cửa hàng
                $query->where(function($q) use ($usedVouchers) {
                    $q->where('hien_thi', 'cong_khai')
                      ->where('diem_doi', 0)
                      ->whereRaw('da_sudung < soluong')
                      ->whereNotIn('mavoucher', $usedVouchers);
                });
                
                // Trường hợp 2: Mã đổi điểm đang nằm trong ví cá nhân của người này
                if (!empty($myVoucherCodes)) {
                    $query->orWhereIn('mavoucher', $myVoucherCodes);
                }
            })
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
        $myVoucherCodes = [];
        
        if (Auth::guard('khachhang')->check()) {
            $makh = Auth::guard('khachhang')->user()->makh;
            
            $usedVouchers = \App\Models\DonHang::where('makh', $makh)
                ->whereNotNull('mavoucher')
                ->where('trangthai', '!=', 'Đã hủy')
                ->pluck('mavoucher')
                ->toArray();

            // [MỚI] Lấy danh sách mã voucher đổi điểm ĐANG SỞ HỮU TRONG VÍ (chưa dùng)
            $myVoucherCodes = \DB::table('khachhang_voucher')
                ->where('makh', $makh)
                ->where('trang_thai', 0)
                ->pluck('mavoucher')
                ->toArray();
        }

        // [TỐI ƯU] Gộp cả Voucher công khai miễn phí VÀ Voucher cá nhân đã đổi bằng điểm
        $publicVouchers = \App\Models\Voucher::where('trangthai', 1)
            ->where('ngay_bd', '<=', now())
            ->where('ngay_kt', '>=', now())
            ->where(function($query) use ($usedVouchers, $myVoucherCodes) {
                // Trường hợp 1: Mã miễn phí công khai của cửa hàng
                $query->where(function($q) use ($usedVouchers) {
                    $q->where('hien_thi', 'cong_khai')
                      ->where('diem_doi', 0)
                      ->whereRaw('da_sudung < soluong')
                      ->whereNotIn('mavoucher', $usedVouchers);
                });
                
                // Trường hợp 2: Mã đổi điểm đang nằm trong ví cá nhân của người này
                if (!empty($myVoucherCodes)) {
                    $query->orWhereIn('mavoucher', $myVoucherCodes);
                }
            })
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
            'sdt_nguoinhan.digits'     => 'Số điện thoại không hợp lệ (10 số).',
            'diachi_giaohang.required' => 'Vui lòng nhập địa chỉ giao hàng.',
            'diachi_giaohang.min'      => 'Địa chỉ phải có ít nhất 5 ký tự.',
        ]);

        $checkoutItems = session()->get('checkout_data');

        if (!$checkoutItems || count($checkoutItems) == 0) {
            return redirect()->route('cart.index')->with('error', 'Đơn hàng của bạn đã hết hạn hoặc không có sản phẩm.');
        }

        // 1. Tính tổng tiền hàng gốc
        $tongTienHang = 0;
        foreach ($checkoutItems as $item) {
            $tongTienHang += $item['price'] * $item['quantity'];
        }

        // 2. Lấy số tiền giảm từ Voucher (nếu có)
        $tienGiamVoucher = 0;
        $maVoucherCode = null;
        if (session()->has('voucher')) {
            $tienGiamVoucher = session('voucher')['tien_giam'];
            $maVoucherCode = session('voucher')['mavoucher'];
        }

        // 3. LOGIC MỚI: TÍNH TIỀN GIẢM THEO HẠNG THÀNH VIÊN
        $tienGiamTheoHang = 0;
        if (Auth::guard('khachhang')->check()) {
            // Tải thông tin user kèm hạng thành viên của họ
            $user = Auth::guard('khachhang')->user()->load('hangThanhVien');
            
            if ($user->hangThanhVien && $user->hangThanhVien->phan_tram_giam > 0) {
                // Số tiền giảm = Tổng tiền hàng * (% giảm của hạng / 100)
                $tienGiamTheoHang = $tongTienHang * ($user->hangThanhVien->phan_tram_giam / 100);
            }
        }

        // Tổng số tiền được giảm thực tế = Voucher + Giảm theo Hạng
        $tongTienDuocGiam = $tienGiamVoucher + $tienGiamTheoHang;

        DB::beginTransaction();
        try {
            $donHang = new DonHang();
            
            // Sinh mã đơn hàng (Độ dài 10 ký tự)
            $maDonMoi = 'DH-' . strtoupper(Str::random(7));
            $donHang->madon = $maDonMoi;
            
            // XỬ LÝ LOGIC KHÁCH HÀNG (CÓ/KHÔNG ĐĂNG NHẬP)
            if (Auth::guard('khachhang')->check()) {
                $donHang->makh = Auth::guard('khachhang')->user()->makh; 
            } else {
                $khachTonTai = \App\Models\KhachHang::where('sdt', $request->sdt_nguoinhan)->first();
                if ($khachTonTai) {
                    $donHang->makh = $khachTonTai->makh;
                } else {
                    $khachMoi = new \App\Models\KhachHang();
                    $maKhMoi = 'KVL' . strtoupper(Str::random(7)); 
                    $khachMoi->makh = $maKhMoi;
                    $khachMoi->hoten = $request->ten_nguoinhan;
                    $khachMoi->sdt = $request->sdt_nguoinhan;
                    $khachMoi->diachi = $request->diachi_giaohang;
                    $khachMoi->email = $request->sdt_nguoinhan . '@gmail.com'; 
                    $khachMoi->password = bcrypt(Str::random(32));
                    $khachMoi->save();
                    
                    $donHang->makh = $maKhMoi;
                }
            }
            
            $donHang->sdt_nhan = $request->sdt_nguoinhan;         
            $donHang->diachi_giao = $request->diachi_giaohang;    
            $donHang->ghichu = $request->ghichu;
            
            // Gán dữ liệu tiền bạc đã tính toán ở trên vào đơn hàng
            $donHang->mavoucher = $maVoucherCode;
            $donHang->tiengiam = $tongTienDuocGiam; 
            $donHang->tongtien = max(0, $tongTienHang - $tongTienDuocGiam); // Tổng tiền cuối cùng sau khi trừ hết ưu đãi
            $donHang->trangthai = 'Chờ xác nhận';
            $donHang->ngaydat = now();
            
            // TẠO MỚI: Sinh token bảo mật cho khách vãng lai xem đơn
            $secureToken = Str::random(40);
            $donHang->token = $secureToken;
            
            $donHang->save();

            if ($maVoucherCode) {
                $vc = Voucher::find($maVoucherCode);
                if ($vc) {
                    // Chỉ tăng số lượt sử dụng voucher nếu đây là mã công khai/nhập mã thông thường
                    // Đối với mã đổi điểm, số lượt sử dụng đã được tính lúc đổi điểm ở ProfileController
                    if ($vc->diem_doi == 0) {
                        $vc->increment('da_sudung');
                    }
                    if ($vc->diem_doi > 0 && Auth::guard('khachhang')->check()) {
                        \DB::table('khachhang_voucher')
                            ->where('makh', Auth::guard('khachhang')->user()->makh)
                            ->where('mavoucher', $maVoucherCode)
                            ->where('trang_thai', 0)
                            ->limit(1)
                            ->update(['trang_thai' => 1]);
                    }
                }
            }
            
            // Lưu chi tiết đơn hàng VÀ TRỪ TỒN KHO THEO LÔ
            foreach ($checkoutItems as $id => $item) {
                $chiTiet = new ChiTietDonHang();
                $chiTiet->madon = $maDonMoi; 
                $chiTiet->masp = $id;               
                $chiTiet->soluong = $item['quantity'];
                $chiTiet->giaban = $item['price']; 
                $chiTiet->save();

                $qtyNeeded = $item['quantity'];
                $loHangs = LoHang::where('masp', $id)
                            ->where('soluong_ton', '>', 0)
                            ->where('ngayhethan', '>=', now())
                            ->orderBy('ngayhethan', 'asc')
                            ->get();

                if ($loHangs->sum('soluong_ton') < $qtyNeeded) {
                    throw new \Exception('Sản phẩm ' . $item['name'] . ' không đủ số lượng trong kho!');
                }

                foreach ($loHangs as $loHang) {
                    if ($qtyNeeded <= 0) break;

                    if ($loHang->soluong_ton >= $qtyNeeded) {
                        $loHang->soluong_ton -= $qtyNeeded;
                        $loHang->save();
                        $qtyNeeded = 0;
                    } else {
                        $qtyNeeded -= $loHang->soluong_ton;
                        $loHang->soluong_ton = 0;
                        $loHang->save();
                    }
                }
            }

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
            session()->put('donhang_token', $secureToken);
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
        $token = session('donhang_token');

        // Nếu không có mã đơn (ai đó gõ trực tiếp url /dat-hang-thanh-cong), đẩy về trang chủ
        if (!$maDon) {
            return redirect()->route('home');
        }

        return view('checkout_success', compact('maDon', 'token'));
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

        // [BỔ SUNG] Chặn áp dụng lậu Voucher đổi điểm nếu chưa thực hiện đổi trong ví
        if ($voucher->diem_doi > 0) {
            $makh = Auth::guard('khachhang')->user()->makh;
            $coSohuu = \DB::table('khachhang_voucher')
                         ->where('makh', $makh)
                         ->where('mavoucher', $mavoucher)
                         ->where('trang_thai', 0) // Còn hạn, chưa dùng
                         ->exists();

            if (!$coSohuu) {
                return back()->with('error', 'Mã giảm giá này yêu cầu phải đổi bằng điểm thưởng mới có thể sử dụng!');
            }
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