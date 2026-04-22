<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SanPham;
use App\Models\DanhMuc;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 1. Xem danh sách sản phẩm
    public function index()
    {
        // Lấy sản phẩm kèm theo thông tin danh mục, phân trang 10 SP/trang
        $products = SanPham::with('danhmuc')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    // 2. Hiển thị form thêm mới
    public function create()
    {
        $categories = DanhMuc::all(); // Lấy danh sách danh mục để đưa vào thẻ <select>
        return view('admin.products.create', compact('categories'));
    }

    // 3. Xử lý lưu sản phẩm mới
    public function store(Request $request)
    {
        $request->validate([
            'masp' => 'required|string|max:10|unique:sanpham,masp',
            'tensp' => 'required|string|max:50',
            'giaban' => 'required|numeric|min:0',
            'giakm' => 'nullable|numeric|min:0|lt:giaban',
            'madm' => 'required|exists:danhmuc,madm',
            'hinhanh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        // Xử lý upload hình ảnh
        if ($request->hasFile('hinhanh')) {
            // Lưu ảnh vào thư mục storage/app/public/image
            $path = $request->file('hinhanh')->store('image', 'public');
            $data['hinhanh'] = '/' . $path; // Lưu đường dẫn vào DB
        }

        SanPham::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    // 4. Hiển thị form sửa sản phẩm
    public function edit($masp)
    {
        $product = SanPham::findOrFail($masp);
        $categories = DanhMuc::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    // 5. Xử lý cập nhật sản phẩm
    public function update(Request $request, $masp)
    {
        $product = SanPham::findOrFail($masp);

        $request->validate([
            'tensp' => 'required|string|max:50',
            'giaban' => 'required|numeric|min:0',
            'giakm' => 'nullable|numeric|min:0|lt:giaban',
            'madm' => 'required|exists:danhmuc,madm',
            'hinhanh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except(['masp']); // Không cho phép sửa mã sản phẩm

        // Xử lý ảnh mới nếu có
        if ($request->hasFile('hinhanh')) {
            // Xóa ảnh cũ nếu có trong storage
            if ($product->hinhanh && !str_starts_with($product->hinhanh, 'http')) {
                Storage::disk('public')->delete(str_replace('/image/', 'image/', $product->hinhanh));
            }
            // Lưu ảnh mới
            $path = $request->file('hinhanh')->store('image', 'public');
            $data['hinhanh'] = '/' . $path;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    // 6. Xóa sản phẩm
    public function destroy($masp)
    {
        $product = SanPham::findOrFail($masp);
        
        // Kiểm tra xem sản phẩm có nằm trong đơn hàng/lô hàng nào không trước khi xóa
        if ($product->donhangs()->count() > 0) {
            return redirect()->route('admin.products.index')->with('error', 'Không thể xóa sản phẩm đã có trong đơn hàng!');
        }

        // Xóa ảnh vật lý
        if ($product->hinhanh && !str_starts_with($product->hinhanh, 'http')) {
             Storage::disk('public')->delete(str_replace('/image/', 'image/', $product->hinhanh));
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
}