@extends('layouts.app')

@section('title', 'Về Chúng Tôi - SunFlower')

@section('content')
<div class="relative min-h-[90vh] flex items-center justify-center py-20 bg-cover bg-center overflow-hidden" style="background-image: url('{{ asset('storage/image/login/nenlogin.png') }}');">
    <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/50 to-black/80 backdrop-blur-[2px]"></div>
    
    <div class="relative z-10 max-w-6xl mx-auto px-4 w-full flex flex-col items-center">
        
        <div class="text-center mb-16 animate-fade-in-down">
            <span class="text-orange-400 font-semibold tracking-widest uppercase text-sm mb-4 block">Thế giới hoa tươi</span>
            <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 drop-shadow-2xl">
                SunFlower
            </h1>
            <p class="text-xl md:text-2xl text-gray-200 italic font-light tracking-wide">"Khơi nguồn cảm xúc từ những đóa hoa"</p>
        </div>
        
        <div class="w-full bg-white/95 backdrop-blur-xl p-8 md:p-16 rounded-[40px] shadow-2xl border border-white/50 text-gray-800">
            
            <div class="max-w-4xl mx-auto text-center mb-24 mt-4">
                <p class="text-xl leading-relaxed text-gray-700 font-medium">
                    "Mỗi loài hoa mang một ngôn ngữ riêng, và mỗi sản phẩm tại SunFlower là một thông điệp được chúng tôi chăm chút để gửi trao đến người bạn thương yêu."
                </p>
                <p class="mt-6 text-gray-600 leading-relaxed">
                    Đến với <span class="text-[#FF6B35] font-bold">SunFlower</span>, bạn không chỉ tìm thấy một cửa hàng bán hoa, mà còn bước vào một thế giới đa sắc màu của thiên nhiên. Từ những cành hoa cắt tươi mới nhất mỗi ngày, những chậu cây xanh mát điểm tô không gian, cho đến những thiết kế nghệ thuật độc bản. Chúng tôi tự hào mang đến cho bạn những sản phẩm hoa tươi chất lượng cao nhất, kết hợp cùng trải nghiệm mua sắm trực tuyến mượt mà và tận tâm.
                </p>
                <div class="w-24 h-1 bg-gradient-to-r from-orange-400 to-orange-600 mx-auto mt-10 rounded-full"></div>
            </div>

            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Cam Kết Chất Lượng</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-24">
                <div class="flex flex-col items-center text-center p-8 rounded-3xl bg-gray-50 hover:bg-orange-50 hover:-translate-y-2 transition-all duration-300 border border-transparent hover:border-orange-100 shadow-sm hover:shadow-md">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-3xl mb-6 text-orange-600 shadow-sm">🌿</div>
                    <h3 class="font-bold text-lg mb-3">Tươi Mới Mỗi Ngày</h3>
                    <p class="text-sm text-gray-600">Hoa được tuyển chọn kỹ lưỡng và bảo quản trong điều kiện tối ưu để giữ vẹn nguyên hương sắc.</p>
                </div>
                <div class="flex flex-col items-center text-center p-8 rounded-3xl bg-gray-50 hover:bg-orange-50 hover:-translate-y-2 transition-all duration-300 border border-transparent hover:border-orange-100 shadow-sm hover:shadow-md">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-3xl mb-6 text-orange-600 shadow-sm">🎨</div>
                    <h3 class="font-bold text-lg mb-3">Thiết Kế Nghệ Thuật</h3>
                    <p class="text-sm text-gray-600">Mỗi bó hoa, lẵng hoa là một tác phẩm được cắm tỉ mỉ bởi đôi bàn tay khéo léo của các nghệ nhân.</p>
                </div>
                <div class="flex flex-col items-center text-center p-8 rounded-3xl bg-gray-50 hover:bg-orange-50 hover:-translate-y-2 transition-all duration-300 border border-transparent hover:border-orange-100 shadow-sm hover:shadow-md">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-3xl mb-6 text-orange-600 shadow-sm">🌸</div>
                    <h3 class="font-bold text-lg mb-3">Đa Dạng Lựa Chọn</h3>
                    <p class="text-sm text-gray-600">Cung cấp hàng trăm loại hoa từ nội địa đến nhập khẩu, phù hợp cho mọi dịp lễ, kỷ niệm.</p>
                </div>
                <div class="flex flex-col items-center text-center p-8 rounded-3xl bg-gray-50 hover:bg-orange-50 hover:-translate-y-2 transition-all duration-300 border border-transparent hover:border-orange-100 shadow-sm hover:shadow-md">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-3xl mb-6 text-orange-600 shadow-sm">🚚</div>
                    <h3 class="font-bold text-lg mb-3">Giao Hàng Tận Tâm</h3>
                    <p class="text-sm text-gray-600">Đảm bảo đóa hoa trao đến tay người nhận luôn trong trạng thái hoàn hảo và đúng hẹn nhất.</p>
                </div>
            </div>

            <div class="mb-24 bg-orange-50/50 p-10 rounded-[30px] border border-orange-100/50">
                <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Khám Phá Các Dòng Sản Phẩm</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10">
                    <div class="flex gap-6 items-start">
                        <div class="text-4xl">💐</div>
                        <div>
                            <h4 class="font-bold text-xl mb-2 text-gray-800">Hoa Sự Kiện & Quà Tặng</h4>
                            <p class="text-gray-600 leading-relaxed text-sm">Các thiết kế hoa bó, hoa lẵng, hoa khai trương sang trọng và tinh tế, giúp bạn gửi gắm trọn vẹn những lời chúc tốt đẹp nhất.</p>
                        </div>
                    </div>
                    <div class="flex gap-6 items-start">
                        <div class="text-4xl">🪴</div>
                        <div>
                            <h4 class="font-bold text-xl mb-2 text-gray-800">Hoa Chậu & Không Gian Xanh</h4>
                            <p class="text-gray-600 leading-relaxed text-sm">Mang thiên nhiên vào ngôi nhà và không gian làm việc của bạn với các chậu Lan Hồ Điệp, hoa Sống Đời, Cẩm Tú Cầu đầy sức sống.</p>
                        </div>
                    </div>
                    <div class="flex gap-6 items-start">
                        <div class="text-4xl">🌹</div>
                        <div>
                            <h4 class="font-bold text-xl mb-2 text-gray-800">Hoa Cắt Cành Tươi Thắm</h4>
                            <p class="text-gray-600 leading-relaxed text-sm">Dành cho những ai yêu thích tự do sáng tạo. Từng cành Hồng, Cúc, Hướng Dương... được thu hoạch đúng độ nở đẹp nhất.</p>
                        </div>
                    </div>
                    <div class="flex gap-6 items-start">
                        <div class="text-4xl">🎁</div>
                        <div>
                            <h4 class="font-bold text-xl mb-2 text-gray-800">Thiết Kế Theo Yêu Cầu</h4>
                            <p class="text-gray-600 leading-relaxed text-sm">SunFlower luôn lắng nghe câu chuyện của bạn để sáng tạo nên những mẫu hoa độc quyền, mang đậm dấu ấn cá nhân.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-12 px-6 border-t border-orange-100 flex flex-col items-center">
                <div class="text-orange-500 mb-6">
                    <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H16.017C14.9124 8 14.017 7.10457 14.017 6V5C14.017 3.89543 14.9124 3 16.017 3H19.017C21.2261 3 23.017 4.79086 23.017 7V15C23.017 18.3137 20.3307 21 17.017 21H14.017ZM1.017 21L1.017 18C1.017 16.8954 1.91243 16 3.017 16H6.017C6.56928 16 7.017 15.5523 7.017 15V9C7.017 8.44772 6.56928 8 6.017 8H3.017C1.91243 8 1.017 7.10457 1.017 6V5C1.017 3.89543 1.91243 3 3.017 3H6.017C8.22614 3 10.017 4.79086 10.017 7V15C10.017 18.3137 7.33071 21 4.017 21H1.017Z"></path>
                    </svg>
                </div>
                <div class="max-w-2xl text-center">
                    <p class="text-2xl md:text-3xl font-serif italic text-gray-800 leading-relaxed mb-6">
                        "Hãy để vẻ đẹp của tự nhiên thay bạn nói lên những điều chưa thể diễn đạt bằng lời."
                    </p>
                    <p class="text-orange-600 font-bold tracking-widest uppercase text-sm">
                        Cùng SunFlower điểm tô cuộc sống mỗi ngày.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translateY(-20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down {
        animation: fade-in-down 1s ease-out;
    }
</style>
@endsection