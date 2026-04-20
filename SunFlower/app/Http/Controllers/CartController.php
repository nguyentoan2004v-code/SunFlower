<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham; // THÊM DÒNG NÀY: Import Model SanPham

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
}