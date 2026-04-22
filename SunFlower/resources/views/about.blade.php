@extends('layouts.app')

@section('title', 'Giới thiệu - SunFlower')

@section('content')
<div class="relative min-h-[80vh] flex items-center justify-center py-16 bg-cover bg-center rounded-3xl overflow-hidden mt-4" style="background-image: url('{{ asset('storage/image/login/nenlogin.png') }}');">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    
    <div class="relative z-10 max-w-6xl mx-auto px-4 w-full flex flex-col items-center justify-center">
        
        <h1 class="text-4xl font-extrabold text-white mb-8 drop-shadow-md text-center">
            🌻 SunFlower - Hệ thống Quản lý Cửa hàng Bán Hoa
        </h1>
        
        <div class="w-full bg-white/95 backdrop-blur-md p-8 md:p-12 rounded-3xl shadow-2xl border border-white/50 text-gray-800">
            
            <p class="text-lg leading-relaxed mb-10 text-center max-w-4xl mx-auto">
                <strong>SunFlower</strong> là một hệ thống ứng dụng web nguyên khối (Monolithic Application) được thiết kế và phát triển nhằm số hóa toàn diện quy trình vận hành của một cửa hàng kinh doanh hoa tươi. Dự án không chỉ giải quyết bài toán bán hàng cơ bản mà còn mở rộng sâu vào khâu quản lý nhân sự và phân ca lịch làm việc, mang lại một giải pháp quản trị tập trung, hiệu quả và tối ưu doanh thu.
            </p>

            <h2 class="text-2xl font-bold text-[#FF6B35] mb-6 flex items-center gap-2 border-b-2 border-orange-100 pb-2">
                🌟 Các tính năng nổi bật
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <div class="bg-orange-50/50 p-5 rounded-2xl border border-orange-100 hover:shadow-md transition">
                    <h3 class="font-bold text-lg text-orange-600 mb-3">1. Phân hệ Sản phẩm & Kho</h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li><strong class="text-gray-900">Quản lý danh mục:</strong> Phân cấp danh mục hoa rõ ràng, logic.</li>
                        <li><strong class="text-gray-900">Quản lý sản phẩm:</strong> Theo dõi chi tiết thông tin hoa, giá bán và khuyến mãi.</li>
                        <li><strong class="text-gray-900">Ràng buộc an toàn:</strong> Xử lý dữ liệu chặt chẽ, ngăn chặn xóa nhầm danh mục có sản phẩm.</li>
                    </ul>
                </div>
                
                <div class="bg-orange-50/50 p-5 rounded-2xl border border-orange-100 hover:shadow-md transition">
                    <h3 class="font-bold text-lg text-orange-600 mb-3">2. Phân hệ Bán hàng & Giao dịch</h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li><strong class="text-gray-900">Quản lý khách hàng:</strong> Lưu trữ thông tin khách hàng thân thiết.</li>
                        <li><strong class="text-gray-900">Xử lý đơn hàng:</strong> Quản lý giỏ hàng, lưu vết chính xác giá bán tại thời điểm giao dịch.</li>
                        <li><strong class="text-gray-900">Xuất hóa đơn tự động:</strong> Tính toán tổng tiền, thuế với quy tắc 1-1 chặt chẽ với Đơn hàng.</li>
                    </ul>
                </div>

                <div class="bg-orange-50/50 p-5 rounded-2xl border border-orange-100 hover:shadow-md transition">
                    <h3 class="font-bold text-lg text-orange-600 mb-3">3. Phân hệ Nhân sự & Phân ca</h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li><strong class="text-gray-900">Hồ sơ nhân viên:</strong> Cấu trúc tự tham chiếu (Self-referencing) để xác định cấp trên trực tiếp.</li>
                        <li><strong class="text-gray-900">Quản lý lịch làm việc:</strong> Thiết lập danh mục các ca làm việc trong tuần/tháng.</li>
                        <li><strong class="text-gray-900">Phân công ca trực:</strong> Theo dõi trạng thái nhiệm vụ của từng nhân sự theo ca.</li>
                    </ul>
                </div>

                <div class="bg-orange-50/50 p-5 rounded-2xl border border-orange-100 hover:shadow-md transition">
                    <h3 class="font-bold text-lg text-orange-600 mb-3">4. Phân hệ Báo cáo (Dashboard)</h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li><strong class="text-gray-900">Trực quan hóa doanh thu:</strong> Theo dõi dòng tiền theo ngày/tuần/tháng dựa trên Hóa đơn.</li>
                        <li><strong class="text-gray-900">Thống kê sản phẩm:</strong> Phân tích top hoa và danh mục bán chạy nhất.</li>
                        <li><strong class="text-gray-900">Quản trị nhân sự:</strong> Đánh giá hiệu suất làm việc dựa trên lịch sử phân công.</li>
                    </ul>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
                <div>
                    <h2 class="text-xl font-bold text-[#FF6B35] mb-4 border-b-2 border-orange-100 pb-2">🛠 Nền tảng Công nghệ</h2>
                    <ul class="list-none space-y-2 text-sm text-gray-700">
                        <li><span class="text-orange-500 mr-2">▪</span> <strong>Ngôn ngữ Backend:</strong> PHP 8.x</li>
                        <li><span class="text-orange-500 mr-2">▪</span> <strong>Core Framework:</strong> Laravel 12.x (MVC)</li>
                        <li><span class="text-orange-500 mr-2">▪</span> <strong>Cơ sở dữ liệu:</strong> MySQL (10 bảng chuẩn hóa)</li>
                        <li><span class="text-orange-500 mr-2">▪</span> <strong>Giao diện (Frontend):</strong> Blade Template, HTML/CSS/JS thuần</li>
                        <li><span class="text-orange-500 mr-2">▪</span> <strong>Quản lý mã nguồn:</strong> Git & GitHub Workflow</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-[#FF6B35] mb-4 border-b-2 border-orange-100 pb-2">🗄️ Cấu trúc Dữ liệu</h2>
                    <ul class="list-none space-y-2 text-sm text-gray-700">
                        <li><span class="text-blue-500 font-bold mr-1">1 - N</span> <code class="bg-gray-100 px-1 rounded">danhmuc</code> ➔ <code class="bg-gray-100 px-1 rounded">sanpham</code></li>
                        <li><span class="text-blue-500 font-bold mr-1">1 - N</span> <code class="bg-gray-100 px-1 rounded">khachhang</code> ➔ <code class="bg-gray-100 px-1 rounded">donhang</code></li>
                        <li><span class="text-green-500 font-bold mr-1">1 - 1</span> <code class="bg-gray-100 px-1 rounded">donhang</code> ➔ <code class="bg-gray-100 px-1 rounded">hoadon</code></li>
                        <li><span class="text-purple-500 font-bold mr-1">N - N</span> <code class="bg-gray-100 px-1 rounded">donhang</code> & <code class="bg-gray-100 px-1 rounded">sanpham</code> (Qua bảng <code class="bg-gray-100 px-1 rounded">chitietdonhang</code>)</li>
                        <li><span class="text-purple-500 font-bold mr-1">N - N</span> <code class="bg-gray-100 px-1 rounded">nhanvien</code> & <code class="bg-gray-100 px-1 rounded">lichlamviec</code> (Qua bảng <code class="bg-gray-100 px-1 rounded">phancong</code>)</li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-200">
                <h2 class="text-xl font-bold text-center text-[#FF6B35] mb-8">🤝 Đội ngũ Phát triển</h2>
                <div class="flex flex-wrap justify-center gap-12">
                    
                    <div class="flex flex-col items-center group">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center text-3xl mb-3 shadow-sm group-hover:scale-110 transition-transform duration-300">
                            👨‍💻
                        </div>
                        <h3 class="font-bold text-lg text-gray-900">Nguyễn Việt Toàn</h3>
                        <p class="text-sm text-gray-500">Backend Team Leader</p>
                        <p class="text-xs text-orange-500 mt-1">Database & System Logic</p>
                    </div>

                    <div class="flex flex-col items-center group">
                        <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center text-3xl mb-3 shadow-sm group-hover:scale-110 transition-transform duration-300 border-2 border-orange-100">
                            👨‍💻
                        </div>
                        <h3 class="font-bold text-lg text-gray-900">Lê Chí Phong</h3>
                        <p class="text-sm text-gray-500">Frontend Developer</p>
                        <p class="text-xs text-orange-500 mt-1">UI/UX & View Integration</p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection