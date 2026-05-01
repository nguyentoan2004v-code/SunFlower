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
    public function index(Request $request)
    {
        $query = SanPham::with('danhmuc')->withSum('lohangs', 'soluong_ton');

        // Tìm theo tên hoặc mã sản phẩm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tensp', 'LIKE', "%{$search}%")
                ->orWhere('masp', 'LIKE', "%{$search}%");
            });
        }

        // Lọc theo danh mục nếu có chọn
        if ($request->filled('madm')) {
            $query->where('madm', $request->madm);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(8);
        $categories = DanhMuc::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    // 2. Hiển thị form thêm mới
    public function create()
    {
        $categories = DanhMuc::all(); // Lấy danh sách danh mục để đưa vào thẻ <select>

        // LOGIC TỰ ĐỘNG TẠO MÃ SẢN PHẨM (Định dạng: SP + 8 số, tổng 10 ký tự)
        $lastProduct = SanPham::orderBy('masp', 'desc')->first();

        if (!$lastProduct) {
            // Nếu chưa có sản phẩm nào, bắt đầu bằng SP00000001
            $newMaSp = 'SP00000001';
        } else {
            // Cắt lấy phần số (bỏ chữ 'SP' ở đầu), cộng thêm 1
            $lastNumber = intval(substr($lastProduct->masp, 2));
            $newNumber = $lastNumber + 1;
            
            // Ép lại thành chuỗi 8 chữ số có số 0 ở đầu
            $newMaSp = 'SP' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);
        }

        // Truyền $newMaSp ra ngoài View
        return view('admin.products.create', compact('categories', 'newMaSp'));
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
            // Lưu ảnh vào thư mục storage/app/public/image/products
            $path = $request->file('hinhanh')->store('image/products', 'public');
            
            // Lưu đường dẫn vào DB (Khuyên dùng cách này để dễ lấy ảnh sau này)
            // Sẽ lưu dạng: image/products/ten_file.jpg
            $data['hinhanh'] = $path; 
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
                // ltrim dùng để xóa dấu '/' ở đầu chuỗi (nếu có) trước khi xóa file
                $oldPath = ltrim($product->hinhanh, '/'); 
                Storage::disk('public')->delete($oldPath);
            }
            
            // Lưu ảnh mới vào thư mục products
            $path = $request->file('hinhanh')->store('image/products', 'public');
            $data['hinhanh'] = $path;
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
            // Dùng ltrim để cắt bỏ dấu '/' ở đầu chuỗi (nếu có)
            // Đảm bảo đường dẫn truyền vào delete() luôn chuẩn: "image/products/ten_file.png" hoặc "image/ten_file.png"
            $imagePath = ltrim($product->hinhanh, '/');
            Storage::disk('public')->delete($imagePath);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
}