<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DanhMuc;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller implements HasMiddleware
{
    /**
     * Trích xuất public_id từ URL Cloudinary.
     */
    private function extractCloudinaryPublicId(?string $url): ?string
    {
        if (!$url || !str_contains($url, 'cloudinary.com')) return null;

        $path = parse_url($url, PHP_URL_PATH);
        $parts = explode('/upload/', $path);
        if (count($parts) < 2) return null;

        $afterUpload = preg_replace('/^v\d+\//', '', $parts[1]);

        return pathinfo($afterUpload, PATHINFO_DIRNAME) . '/' . pathinfo($afterUpload, PATHINFO_FILENAME);
    }

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
    public function index(Request $request)
    {
        $query = DanhMuc::withCount('sanphams');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tendm', 'LIKE', "%{$search}%")
                  ->orWhere('madm', 'LIKE', "%{$search}%");
            });
        }

        $categories = $query->orderBy('created_at', 'desc')->paginate(8)->withQueryString();
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
        set_time_limit(300); // Tăng thời gian thực thi lên 5 phút
        $request->validate([
            'madm' => 'required|string|max:10|unique:danhmuc,madm',
            'tendm' => 'required|string|max:100',
            'hinhanh' => 'nullable|image|mimes:jpeg,png,jpg,gif'
        ], [
            'hinhanh.image' => 'Ảnh phải là định dạng hình ảnh hợp lệ.'
        ]);

        $data = $request->all();

        // XỬ LÝ UPLOAD ẢNH LÊN CLOUDINARY
        if ($request->hasFile('hinhanh')) {
            $cloudinary = new Cloudinary(config('cloudinary.url'));
            
            $result = $cloudinary->uploadApi()->upload($request->file('hinhanh')->getRealPath(), [
                'folder' => 'sunflower_categories' // Lưu vào thư mục danh mục riêng cho gọn
            ]);
            
            $data['hinhanh'] = $result['secure_url'];
        }

        DanhMuc::create($data);
        Cache::forget('danhmuc_all');

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
        set_time_limit(300); // Tăng thời gian thực thi lên 5 phút
        $category = DanhMuc::findOrFail($madm);

        $request->validate([
            'tendm' => 'required|string|max:100',
            'hinhanh' => 'nullable|image|mimes:jpeg,png,jpg,gif'
        ], [
            'hinhanh.image' => 'Ảnh phải là định dạng hình ảnh hợp lệ.'
        ]);

        $data = $request->except(['madm']);

        // Nếu KHÔNG có ảnh mới upload lên, phải loại bỏ 'hinhanh' khỏi mảng data để giữ nguyên hình cũ
        if (!$request->hasFile('hinhanh')) {
            unset($data['hinhanh']);
        }

        // XỬ LÝ ẢNH MỚI NẾU CÓ
        if ($request->hasFile('hinhanh')) {
            
            // 1. Dọn dẹp ảnh cũ
            if ($category->hinhanh) {
                if (!str_starts_with($category->hinhanh, 'http')) {
                    // Ảnh local → xóa khỏi ổ cứng
                    Storage::disk('public')->delete('image/' . $category->hinhanh);
                } else {
                    // Ảnh Cloudinary → xóa trên cloud
                    try {
                        $oldPublicId = $this->extractCloudinaryPublicId($category->hinhanh);
                        if ($oldPublicId) {
                            $cloudinaryDel = new Cloudinary(config('cloudinary.url'));
                            $cloudinaryDel->uploadApi()->destroy($oldPublicId);
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Không thể xóa ảnh cũ Cloudinary (Danh mục): ' . $e->getMessage());
                    }
                }
            }
            
            // 2. Upload ảnh mới lên Cloudinary
            $cloudinary = new Cloudinary(config('cloudinary.url'));
            $result = $cloudinary->uploadApi()->upload($request->file('hinhanh')->getRealPath(), [
                'folder' => 'sunflower_categories'
            ]);
            
            $data['hinhanh'] = $result['secure_url'];
        }

        $category->update($data);
        Cache::forget('danhmuc_all');

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
        if ($category->hinhanh) {
            if (!str_starts_with($category->hinhanh, 'http')) {
                // Ảnh local → xóa khỏi ổ cứng
                Storage::disk('public')->delete('image/' . $category->hinhanh);
            } else {
                // Ảnh Cloudinary → xóa trên cloud
                try {
                    $publicId = $this->extractCloudinaryPublicId($category->hinhanh);
                    if ($publicId) {
                        $cloudinary = new Cloudinary(config('cloudinary.url'));
                        $cloudinary->uploadApi()->destroy($publicId);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Không thể xóa ảnh Cloudinary khi xóa DM: ' . $e->getMessage());
                }
            }
        }

        $category->delete();
        Cache::forget('danhmuc_all');

        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công!');
    }
}