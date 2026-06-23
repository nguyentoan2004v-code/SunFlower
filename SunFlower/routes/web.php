<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\LoHangController;
use App\Http\Controllers\Admin\PhieuHuyHangController;
use App\Http\Controllers\Admin\NhanVienController;
use App\Http\Controllers\Admin\LichLamViecController;
use App\Http\Controllers\DanhGiaController;
use App\Http\Controllers\Admin\KhachHangController;
use App\Http\Controllers\Admin\VoucherController;
// =====================
// USER
// =====================

// =====================
// 1. TRANG CHỦ & SẢN PHẨM
// =====================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/danh-muc/{madm}', [HomeController::class, 'categoryDetail'])->name('category.show');
Route::get('/chi-tiet/{masp}', [HomeController::class, 'productDetail'])->name('product.show');
Route::get('/product/{masp}/image', [ProductController::class, 'showImage'])->name('product.image');
Route::get('/category/{madm}/image', [HomeController::class, 'showCategoryImage'])->name('category.image');
Route::get('/tat-ca-san-pham', [HomeController::class, 'allCategories'])->name('categories.index');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/gioi-thieu', [HomeController::class, 'about'])->name('about');

// =====================
// 2. AUTH (ĐĂNG NHẬP / ĐĂNG KÝ)
// =====================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::get('/dang-ky', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/dang-ky', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Quên mật khẩu (khách hàng)
Route::get('/quen-mat-khau', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/quen-mat-khau', [PasswordResetController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
Route::get('/dat-lai-mat-khau/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset.khachhang');
Route::post('/dat-lai-mat-khau', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// =====================
// 3. PROFILE & ĐƠN HÀNG (YÊU CẦU ĐĂNG NHẬP)
// =====================
Route::middleware('auth:khachhang')->group(function () {

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::post('/profile/exchange-voucher', [ProfileController::class, 'exchangeVoucher'])->name('profile.exchange_voucher');
        
    });

    // Đơn hàng
    Route::get('/lich-su-don-hang', [OrderController::class, 'history'])->name('orders.history');
    Route::post('/danh-gia', [DanhGiaController::class, 'store'])->name('danhgia.store');
    
});
Route::get('/don-hang/{madon}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/don-hang/{madon}/huy', [OrderController::class, 'cancel'])->name('orders.cancel');

// =====================
// 4. GIỎ HÀNG & THANH TOÁN
// =====================
Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::post('/gio-hang/them/{masp}', [CartController::class, 'add'])->name('cart.add');
Route::post('/gio-hang/xoa/{masp}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/gio-hang/update', [CartController::class, 'update'])->name('cart.update');
Route::match(['get', 'post'], '/thanh-toan', [CartController::class, 'checkout'])->name('checkout');
Route::post('/dat-hang', [CartController::class, 'placeOrder'])->name('order.place');
Route::get('/mua-ngay/{masp}', [CartController::class, 'buyNow'])->name('cart.buyNow');
Route::get('/dat-hang-thanh-cong', [CartController::class, 'orderSuccess'])->name('checkout.success');
Route::post('/ap-dung-voucher', [CartController::class, 'applyVoucher'])->name('voucher.apply');
Route::post('/go-voucher', [CartController::class, 'removeVoucher'])->name('voucher.remove');

// =====================
// ADMIN
// =====================


Route::prefix('admin')->name('admin.')->group(function () {
    // Các route không cần login
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');

    // Các route CẦN login admin
    Route::middleware(['admin.auth'])->group(function () {
        
        
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // --- QUẢN LÝ SẢN PHẨM ---
        Route::post('products/generate-description', [AdminProductController::class, 'generateDescription'])->name('products.generate-desc');
        Route::resource('products', AdminProductController::class);
        // --- QUẢN LÝ DANH MỤC ---
        Route::resource('categories', AdminCategoryController::class);
        // --- QUẢN LÝ MÃ GIẢM GIÁ (VOUCHER) ---
        Route::resource('vouchers', VoucherController::class);
        // --- QUẢN LÝ ĐƠN HÀNG ---
        Route::resource('orders', AdminOrderController::class)->except(['create', 'store', 'destroy']);
        // --- Xuất hóa đơn và in hóa đơn ---
        Route::post('/orders/{madon}/export-invoice', [AdminOrderController::class, 'exportInvoice'])->name('orders.export-invoice');
        Route::get('/invoices/{mahd}/print', [AdminOrderController::class, 'printInvoice'])->name('orders.print-invoice');
        // --- QUẢN LÝ LÔ HÀNG VÀ PHIẾU HỦY HÀNG ---
        Route::resource('lohang', LoHangController::class);
        // Chỉ khai báo 1 lần qua resource — views và controller đều dùng admin.phieuhuyhang.*
        Route::resource('phieuhuyhang', PhieuHuyHangController::class);

        // --- QUẢN LÝ NHÂN VIÊN VÀ PHÂN QUYỀN ---
        Route::get('/nhanvien', [NhanVienController::class, 'index'])->name('nhanvien.index');
        Route::get('/nhanvien/{manv}/roles', [NhanVienController::class, 'editRole'])->name('nhanvien.roles');
        Route::put('/nhanvien/{manv}/roles', [NhanVienController::class, 'updateRole'])->name('nhanvien.updateRoles');
        Route::get('/nhanvien/create', [NhanVienController::class, 'create'])->name('nhanvien.create');
        Route::post('/nhanvien', [NhanVienController::class, 'store'])->name('nhanvien.store');
        Route::get('/nhanvien/{nhanvien}/edit', [NhanVienController::class, 'edit'])->name('nhanvien.edit');
        Route::put('/nhanvien/{nhanvien}', [NhanVienController::class, 'update'])->name('nhanvien.update');
        Route::delete('/nhanvien/{nhanvien}', [NhanVienController::class, 'destroy'])->name('nhanvien.destroy');

        
       
        // --- QUẢN LÝ LỊCH LÀM VIỆC VÀ PHÂN CÔNG (THEO MA TRẬN TUẦN) ---
        // 1. Giao diện xem Xếp lịch theo tuần (Dành cho Quản lý)
        Route::get('/lichlamviec', [LichLamViecController::class, 'index'])->name('lichlamviec.index');
        // 2. Xử lý lưu lịch của cả tuần khi bấm nút Lưu (Dành cho Quản lý)
        Route::post('/lichlamviec/save-weekly', [LichLamViecController::class, 'saveWeekly'])->name('lichlamviec.saveWeekly');
        // 3. Giao diện xem lịch cá nhân (Dành cho mọi Nhân viên)
        Route::get('/lich-cua-toi', [LichLamViecController::class, 'mySchedule'])->name('lichlamviec.mySchedule');
        Route::post('/lichlamviec/auto-generate', [App\Http\Controllers\Admin\LichLamViecController::class, 'autoGenerate'])->name('lichlamviec.autoGenerate');
        
        Route::post('/dashboard/refresh-ai', function () {
            \Illuminate\Support\Facades\Cache::forget(
                'ai_advice_' . \Carbon\Carbon::today()->format('Y-m-d')
            );
            return redirect()->route('admin.dashboard')
                ->with('success', 'Đã làm mới gợi ý AI!');
        })->name('dashboard.refresh-ai');
         
            Route::prefix('khachhang')->name('khachhang.')->group(function () {
            Route::get('/', [KhachHangController::class, 'index'])->name('index');
            Route::get('/{makh}/edit', [KhachHangController::class, 'edit'])->name('edit');
            Route::put('/{makh}', [KhachHangController::class, 'update'])->name('update');
            Route::delete('/{makh}', [KhachHangController::class, 'destroy'])->name('destroy');
            Route::post('/{makh}/reset-password', [KhachHangController::class, 'resetPassword'])->name('resetPassword');
            Route::get('/{makh}/history', [KhachHangController::class, 'history'])->name('history');
        });

        Route::prefix('danhgia')->name('danhgia.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\DanhGiaController::class, 'index'])->name('index');
            Route::post('/{id}/reply', [App\Http\Controllers\Admin\DanhGiaController::class, 'reply'])->name('reply');
            Route::post('/{id}/toggle', [App\Http\Controllers\Admin\DanhGiaController::class, 'toggleStatus'])->name('toggle');
            Route::delete('/{id}', [App\Http\Controllers\Admin\DanhGiaController::class, 'destroy'])->name('destroy');
        });
        
    });
});
// ==========================================
// API ROUTES CHO T�NH N�NG �?C BI?T
// ==========================================
Route::post('/chatbot/ask', [\App\Http\Controllers\ChatbotController::class, 'ask'])->name('chatbot.ask');

