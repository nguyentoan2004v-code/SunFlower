<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SanPham;
use App\Models\DanhMuc;
use App\Models\HinhAnhSanPham;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Cloudinary\Cloudinary;

class ProductController extends Controller implements HasMiddleware
{
    /**
     * Trích xuất public_id từ URL Cloudinary.
     * VD: "https://res.cloudinary.com/.../sunflower_products/abc123.jpg" → "sunflower_products/abc123"
     */
    private function extractCloudinaryPublicId(?string $url): ?string
    {
        if (!$url || !str_contains($url, 'cloudinary.com')) return null;

        $path = parse_url($url, PHP_URL_PATH);
        $parts = explode('/upload/', $path);
        if (count($parts) < 2) return null;

        // Bỏ version prefix (v1234567890/...)
        $afterUpload = preg_replace('/^v\d+\//', '', $parts[1]);

        // Trả về public_id không có extension
        return pathinfo($afterUpload, PATHINFO_DIRNAME) . '/' . pathinfo($afterUpload, PATHINFO_FILENAME);
    }

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = auth()->guard('nhanvien')->user();
                
                if (!$user->hasRole('Quản lý Cửa hàng') && !$user->hasRole('Quản lý Sản phẩm') && !$user->hasRole('Quản lý Sản phẩm & Danh mục')) {
                    abort(403, 'Bạn không có quyền thao tác với Sản phẩm!');
                }
                
                return $next($request);
            }),
        ];
    }
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
        set_time_limit(300); // Tăng thời gian thực thi lên 5 phút
        $request->validate([
            'masp' => 'required|string|max:10|unique:sanpham,masp',
            'tensp' => 'required|string|max:50',
            'giaban' => 'required|numeric|min:0',
            'giakm' => 'nullable|numeric|min:0|lt:giaban',
            'madm' => 'required|exists:danhmuc,madm',
            'hinhanh' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'hinhanh_phu.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'mota' => 'nullable|string',
            'mota_chitiet' => 'nullable|string'
        ], [
            'hinhanh.image' => 'Ảnh chính phải là định dạng hình ảnh hợp lệ.',
            'hinhanh_phu.*.image' => 'Ảnh phụ phải là định dạng hình ảnh hợp lệ.',
        ]);

        $data = $request->except(['hinhanh_phu']);

        // XỬ LÝ UPLOAD HÌNH ẢNH CHÍNH LÊN CLOUDINARY
        if ($request->hasFile('hinhanh')) {
            $cloudinary = new Cloudinary(config('cloudinary.url'));
            $result = $cloudinary->uploadApi()->upload($request->file('hinhanh')->getRealPath(), [
                'folder' => 'sunflower_products'
            ]);
            $data['hinhanh'] = $result['secure_url']; 
        }

        $sanPham = SanPham::create($data);

        // XỬ LÝ UPLOAD ẢNH PHỤ (GALLERY)
        if ($request->hasFile('hinhanh_phu')) {
            $cloudinary = $cloudinary ?? new Cloudinary(config('cloudinary.url'));
            $files = $request->file('hinhanh_phu');
            // Giới hạn tối đa 5 ảnh phụ
            $files = array_slice($files, 0, 5);
            
            foreach ($files as $index => $file) {
                try {
                    $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'folder' => 'sunflower_products/gallery'
                    ]);
                    HinhAnhSanPham::create([
                        'masp' => $sanPham->masp,
                        'duong_dan' => $result['secure_url'],
                        'thu_tu' => $index,
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Lỗi upload ảnh phụ: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    // 4. Hiển thị form sửa sản phẩm
    public function edit($masp)
    {
        $product = SanPham::with('hinhAnhPhu')->findOrFail($masp);
        $categories = DanhMuc::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    // 5. Xử lý cập nhật sản phẩm
    public function update(Request $request, $masp)
    {
        set_time_limit(300); // Tăng thời gian thực thi lên 5 phút để tránh lỗi timeout khi upload ảnh nặng lên Cloudinary
        $product = SanPham::findOrFail($masp);

        $request->validate([
            'tensp' => 'required|string|max:50',
            'giaban' => 'required|numeric|min:0',
            'giakm' => 'nullable|numeric|min:0|lt:giaban',
            'madm' => 'required|exists:danhmuc,madm',
            'hinhanh' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'hinhanh_phu.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'mota' => 'nullable|string',
            'mota_chitiet' => 'nullable|string'
        ], [
            'hinhanh.image' => 'Ảnh chính phải là định dạng hình ảnh hợp lệ.',
            'hinhanh_phu.*.image' => 'Ảnh phụ phải là định dạng hình ảnh hợp lệ.',
        ]);

        $data = $request->except(['masp', 'hinhanh_phu', 'xoa_anh_phu']); // Không cho phép sửa mã sản phẩm

        \Log::info('--- PRODUCT UPDATE REQUEST ---', ['masp' => $masp]);
        \Log::info('Has hinhanh file?', ['has' => $request->hasFile('hinhanh')]);
        \Log::info('Has hinhanh_phu files?', ['has' => $request->hasFile('hinhanh_phu')]);

        // Nếu KHÔNG có ảnh mới upload lên, phải loại bỏ 'hinhanh' khỏi mảng data để giữ nguyên hình cũ
        if (!$request->hasFile('hinhanh')) {
            unset($data['hinhanh']);
        }

        // XỬ LÝ ẢNH CHÍNH MỚI NẾU CÓ
        if ($request->hasFile('hinhanh')) {
            
            // 1. Dọn dẹp ảnh cũ
            if ($product->hinhanh) {
                if (!str_starts_with($product->hinhanh, 'http')) {
                    $oldPath = ltrim($product->hinhanh, '/'); 
                    if(Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                } else {
                    try {
                        $oldPublicId = $this->extractCloudinaryPublicId($product->hinhanh);
                        if ($oldPublicId) {
                            $cloudinaryDel = new Cloudinary(config('cloudinary.url'));
                            $cloudinaryDel->uploadApi()->destroy($oldPublicId);
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Không thể xóa ảnh cũ Cloudinary: ' . $e->getMessage());
                    }
                }
            }
            
            // 2. Upload ảnh mới thẳng lên Cloudinary
            $cloudinary = new Cloudinary(config('cloudinary.url'));
            $result = $cloudinary->uploadApi()->upload($request->file('hinhanh')->getRealPath(), [
                'folder' => 'sunflower_products'
            ]);
            $data['hinhanh'] = $result['secure_url'];
        }

        $product->update($data);

        // XỬ LÝ XÓA ẢNH PHỤ (nếu admin tick chọn xóa)
        if ($request->has('xoa_anh_phu')) {
            $idsToDelete = $request->input('xoa_anh_phu', []);
            $anhCanXoa = HinhAnhSanPham::whereIn('id', $idsToDelete)->where('masp', $masp)->get();
            
            $cloudinary = $cloudinary ?? new Cloudinary(config('cloudinary.url'));
            foreach ($anhCanXoa as $anh) {
                try {
                    $publicId = $this->extractCloudinaryPublicId($anh->duong_dan);
                    if ($publicId) {
                        $cloudinary->uploadApi()->destroy($publicId);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Lỗi xóa ảnh phụ Cloudinary: ' . $e->getMessage());
                }
                $anh->delete();
            }
        }

        // XỬ LÝ THÊM ẢNH PHỤ MỚI
        if ($request->hasFile('hinhanh_phu')) {
            $soAnhHienCo = HinhAnhSanPham::where('masp', $masp)->count();
            $soAnhConDuocThem = max(0, 5 - $soAnhHienCo);
            
            if ($soAnhConDuocThem > 0) {
                $cloudinary = $cloudinary ?? new Cloudinary(config('cloudinary.url'));
                $files = array_slice($request->file('hinhanh_phu'), 0, $soAnhConDuocThem);
                $maxThuTu = HinhAnhSanPham::where('masp', $masp)->max('thu_tu') ?? -1;
                
                foreach ($files as $file) {
                    try {
                        $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                            'folder' => 'sunflower_products/gallery'
                        ]);
                        HinhAnhSanPham::create([
                            'masp' => $masp,
                            'duong_dan' => $result['secure_url'],
                            'thu_tu' => ++$maxThuTu,
                        ]);
                    } catch (\Exception $e) {
                        \Log::warning('Lỗi upload ảnh phụ mới: ' . $e->getMessage());
                    }
                }
            }
        }

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

        // Xóa ảnh
        if ($product->hinhanh) {
            if (!str_starts_with($product->hinhanh, 'http')) {
                // Ảnh local → xóa khỏi ổ cứng
                $imagePath = ltrim($product->hinhanh, '/');
                Storage::disk('public')->delete($imagePath);
            } else {
                // Ảnh Cloudinary → xóa trên cloud
                try {
                    $publicId = $this->extractCloudinaryPublicId($product->hinhanh);
                    if ($publicId) {
                        $cloudinary = new Cloudinary(config('cloudinary.url'));
                        $cloudinary->uploadApi()->destroy($publicId);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Không thể xóa ảnh Cloudinary khi xóa SP: ' . $e->getMessage());
                }
            }
        }

        // Xóa tất cả ảnh phụ trên Cloudinary
        foreach ($product->hinhAnhPhu as $anhPhu) {
            try {
                $publicId = $this->extractCloudinaryPublicId($anhPhu->duong_dan);
                if ($publicId) {
                    $cloudinaryGallery = $cloudinaryGallery ?? new Cloudinary(config('cloudinary.url'));
                    $cloudinaryGallery->uploadApi()->destroy($publicId);
                }
            } catch (\Exception $e) {
                \Log::warning('Lỗi xóa ảnh phụ khi xóa SP: ' . $e->getMessage());
            }
        }
        // Bảng hinhanh_sanpham sẽ tự xóa cascade theo FK

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    // 7. Xử lý AI Sinh mô tả sản phẩm (Sử dụng Gemini API)
    public function generateDescription(Request $request)
    {
        $request->validate([
            'tensp' => 'required|string|max:100'
        ]);

        $keyword = $request->tensp;
        $apiKey = config('services.gemini.key');

        if (empty($apiKey)) {
            return response()->json(['error' => 'Thiếu GEMINI_API_KEY trong file .env'], 500);
        }

        $systemPrompt = <<<EOT
Bạn là một Copywriter chuyên nghiệp của shop hoa SunFlower. Nhiệm vụ của bạn là viết một bài mô tả sản phẩm thật lãng mạn, tinh tế và đầy cảm xúc dựa trên tên sản phẩm được cung cấp.

BẮT BUỘC TUÂN THỦ NGHIÊM NGẶT cấu trúc 5 phần sau (được format sẵn bằng HTML):

1. Mở bài (1-2 đoạn): Miêu tả vẻ đẹp rực rỡ, cảm giác mang lại, ý nghĩa loài hoa.
2. Hình ảnh: Dành 1 dòng chỉ in ra đúng chữ "[anh_hoa]"
3. Dịp tặng: Tiêu đề <strong>Phù hợp cho những dịp như:</strong> theo sau là danh sách <ul>.
4. Thông điệp: Tiêu đề <strong>Thông điệp mà bó hoa mang lại:</strong> theo sau là danh sách <ul>.
5. Kết luận: 1 đoạn văn khẳng định ý nghĩa món quà.

Dưới đây là một VÍ DỤ MẪU BẮT BUỘC PHẢI NOI THEO (bạn hãy thay đổi nội dung cho phù hợp với hoa được yêu cầu nhưng GIỮ NGUYÊN FORMAT HTML này):

<p>Mở bài lãng mạn ở đây...</p>
<p>Mô tả chi tiết hơn về thiết kế...</p>
<p>[anh_hoa]</p>
<p><strong>Phù hợp cho những dịp như:</strong></p>
<ul>
    <li>Dịp 1...</li>
    <li>Dịp 2...</li>
</ul>
<p><strong>Thông điệp mà bó hoa mang lại:</strong></p>
<ul>
    <li>Sự ấm áp và nguồn năng lượng tích cực</li>
    <li>Tình yêu nhẹ nhàng nhưng chân thành</li>
    <li>Lời chúc hạnh phúc và may mắn</li>
    <li>Niềm hy vọng về những khởi đầu mới tốt đẹp</li>
</ul>
<p>Với vẻ đẹp thanh lịch cùng ý nghĩa sâu sắc, bó hoa này sẽ là món quà hoàn hảo giúp bạn thay lời muốn nói, mang đến niềm vui và cảm xúc đặc biệt cho người nhận trong mọi khoảnh khắc đáng nhớ.</p>

TUYỆT ĐỐI KHÔNG giải thích, KHÔNG thêm lời chào, chỉ trả về mã HTML.
EOT;

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
            
            $response = \Illuminate\Support\Facades\Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post($url, [
                    'system_instruction' => [
                        'parts' => [
                            ['text' => $systemPrompt]
                        ]
                    ],
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => "Tên sản phẩm cần viết: {$keyword}"]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 1500
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Trích xuất nội dung từ phản hồi của Gemini
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $content = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Clean up any potential markdown code blocks like ```html
                    $content = preg_replace('/^```html\n?|```$/m', '', $content);
                    $content = trim($content);

                    return response()->json([
                        'success' => true,
                        'description' => $content
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => 'Gemini API trả về kết quả không hợp lệ.'
                    ], 500);
                }
            }

            return response()->json([
                'success' => false,
                'error' => 'Lỗi từ Gemini API: ' . $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Lỗi kết nối: ' . $e->getMessage()
            ], 500);
        }
    }
}