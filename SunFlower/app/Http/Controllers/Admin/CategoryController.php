<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DanhMuc;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // 1. Hiển thị danh sách danh mục
    public function index()
    {
        // Lấy danh sách, danh mục mới tạo lên trước, kèm số lượng sản phẩm
        $categories = DanhMuc::withCount('sanphams')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    // 2. Form thêm mới
    public function create()
    {
        return view('admin.categories.create');
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