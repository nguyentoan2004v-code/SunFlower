<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiCaller;

class CartController extends Controller
{
    use ApiCaller;

    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function add($masp)
    {
        // Gọi API để lấy thông tin sản phẩm muốn thêm
        $response = $this->callApi("/api/sanphams/{$masp}");
        
        // Lấy phần data thực sự từ API response
        $product = isset($response['data']) ? $response['data'] : null;

        if (!$product || (isset($response['status']) && $response['status'] == 'error')) {
            return back()->with('error', 'Sản phẩm không tồn tại!');
        }

        $cart = session()->get('cart', []);

        // Nếu sản phẩm đã có, tăng số lượng
        if (isset($cart[$masp])) {
            $cart[$masp]['quantity']++;
        } else {
            // Nếu chưa có, thêm mới vào mảng
            $cart[$masp] = [
                "name" => $product['tensp'],
                "quantity" => 1,
                "price" => $product['giaban'],
                "image" => $product['hinhanh']
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }
}