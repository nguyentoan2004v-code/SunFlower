<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DanhMuc;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = auth()->guard('nhanvien')->user();
                
                if (!$user->hasRole('Quản lý Cửa hàng') && !$user->hasRole('Quản lý Sản phẩm') && !$user->hasRole('Quản lý Sản phẩm & Danh mục')) {
                    abort(403, 'Bạn không có quyền thao tác với Danh mục!');
                }
                
                return $next($request);
            }),
        ];
    }
    // 1. Hiển thị danh sách danh mục
    public function index()
    {
        // Lấy danh sách, danh mục mới tạo lên trước, kèm số lượng sản phẩm
        $categories = DanhMuc::withCount('sanphams')->orderBy('created_at', 'desc')->paginate(8);
        return view('admin.categories.index', compact('categories'));
    }

    // 2. Form thêm mới
    public function create()
    {
        // LOGIC TỰ ĐỘNG TẠO MÃ DANH MỤC (Định dạng: DM + 8 số, tổng 10 ký tự)
        $lastCategory = DanhMuc::orderBy('madm', 'desc')->first();

        if (!$lastCategory) {
            // Nếu chưa có danh mục nào, bắt đầu bằng DM00000001
            $newMaDM = 'DM00000001';
        } else {
            // Cắt lấy phần số (bỏ chữ 'DM' ở đầu), cộng thêm 1
            $lastNumber = intval(substr($lastCategory->madm, 2));
            $newNumber = $lastNumber + 1;
            
            // Ép lại thành chuỗi 8 chữ số có số 0 ở đầu
            $newMaDM = 'DM' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);
        }

        // Truyền biến $newMaDM ra ngoài View
        return view('admin.categories.create', compact('newMaDM'));
    }

    // 3. Xử lý lưu danh mục
    public function store(Request $request)
    {
        $request->validate([
            'madm' => 'required|string|max:10|unique:danhmuc,madm',
            'tendm' => 'required|string|max:100',
            'hinhanh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        // Xử lý upload ảnh (Chỉ lưu tên file y hệt Sản phẩm)
        if ($request->hasFile('hinhanh')) {
            $file = $request->file('hinhanh');
            $filename = time() . '_dm_' . $file->getClientOriginalName();
            $file->storeAs('image', $filename, 'public'); 
            $data['hinhanh'] = $filename;
        }

        DanhMuc::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    // 4. Form sửa danh mục
    public function edit($madm)
    {
        $category = DanhMuc::findOrFail($madm);
        return view('admin.categories.edit', compact('category'));
    }

    // 5. Xử lý cập nhật
    public function update(Request $request, $madm)
    {
        $category = DanhMuc::findOrFail($madm);

        $request->validate([
            'tendm' => 'required|string|max:100',
            'hinhanh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except(['madm']);

        if ($request->hasFile('hinhanh')) {
            // Xóa ảnh cũ
            if ($category->hinhanh && !str_starts_with($category->hinhanh, 'http')) {
                Storage::disk('public')->delete('image/' . $category->hinhanh);
            }
            // Lưu ảnh mới
            $file = $request->file('hinhanh');
            $filename = time() . '_dm_' . $file->getClientOriginalName();
            $file->storeAs('image', $filename, 'public');
            $data['hinhanh'] = $filename;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    // 6. Xử lý xóa
    public function destroy($madm)
    {
        $category = DanhMuc::findOrFail($madm);

        // Kiểm tra xem danh mục này có đang chứa sản phẩm nào không
        if ($category->sanphams()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Không thể xóa! Danh mục này đang chứa sản phẩm.');
        }

        // Xóa ảnh
        if ($category->hinhanh && !str_starts_with($category->hinhanh, 'http')) {
            Storage::disk('public')->delete('image/' . $category->hinhanh);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công!');
    }
}