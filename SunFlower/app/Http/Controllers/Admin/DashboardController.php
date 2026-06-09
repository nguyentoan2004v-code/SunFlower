<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\NhanVien;
use App\Models\LoHang;
use App\Models\ChiTietDonHang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
 
class DashboardController extends Controller
{
    public function index()
    {
        // ============================================================
        // 1. THỐNG KÊ TỔNG QUAN
        // ============================================================
        $donHangMoiCount = DonHang::where('trangthai', 'Chờ xác nhận')->count();
 
        $doanhThuNgay = DonHang::whereDate('ngaydat', Carbon::today())
                                ->where('trangthai', 'Đã hoàn thành')
                                ->sum('tongtien');
 
        $tongSanPham  = SanPham::count();
        $tongNhanVien = NhanVien::count();
 
        // ============================================================
        // 2. 5 ĐƠN HÀNG MỚI NHẤT
        // ============================================================
        $recentOrders = DonHang::with('khachhang')
                                ->orderBy('ngaydat', 'desc')
                                ->take(5)
                                ->get();
 
        // ============================================================
        // 3. BIỂU ĐỒ DOANH THU 7 NGÀY
        // ============================================================
        $revenueLabels = [];
        $revenueData   = [];
        
        $sevenDaysAgo = Carbon::today()->subDays(6);
        $dailyRevenues = DonHang::where('trangthai', 'Đã hoàn thành')
                                ->where('ngaydat', '>=', $sevenDaysAgo->startOfDay())
                                ->selectRaw('DATE(ngaydat) as date, SUM(tongtien) as total')
                                ->groupBy('date')
                                ->pluck('total', 'date');

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenueLabels[] = $date->format('d/m');
            $dateString = $date->format('Y-m-d');
            $revenueData[] = (int) ($dailyRevenues[$dateString] ?? 0);
        }
 
        // ============================================================
        // 4. BIỂU ĐỒ TRÒN CƠ CẤU SẢN PHẨM
        // ============================================================
        $categoryDataRaw = SanPham::join('danhmuc', 'sanpham.madm', '=', 'danhmuc.madm')
            ->selectRaw('danhmuc.tendm, count(sanpham.masp) as total_products')
            ->groupBy('danhmuc.madm', 'danhmuc.tendm')
            ->get();
 
        $catLabels = $categoryDataRaw->pluck('tendm')->toArray();
        $catData   = $categoryDataRaw->pluck('total_products')->toArray();
 
        // ============================================================
        // 5. CẢNH BÁO TỒN KHO THẤP
        // ============================================================
        $lowStockProducts = SanPham::join('lo_hang', 'sanpham.masp', '=', 'lo_hang.masp')
            ->selectRaw('sanpham.masp, sanpham.tensp, sum(lo_hang.soluong_ton) as soluong')
            ->groupBy('sanpham.masp', 'sanpham.tensp')
            ->having('soluong', '<=', 10)
            ->take(3)
            ->get();
 
        // ============================================================
        // 6. TOP SẢN PHẨM BÁN CHẠY 30 NGÀY
        // ============================================================
        $topProducts30Days = ChiTietDonHang::join('donhang', 'chitiet_donhang.madon', '=', 'donhang.madon')
            ->join('sanpham', 'chitiet_donhang.masp', '=', 'sanpham.masp')
            ->where('donhang.ngaydat', '>=', Carbon::today()->subDays(30))
            ->where('donhang.trangthai', 'Đã hoàn thành')
            ->selectRaw('sanpham.masp, sanpham.tensp,
                        SUM(chitiet_donhang.soluong) as tong_ban,
                        SUM(chitiet_donhang.soluong * chitiet_donhang.giaban) as doanh_thu')
            ->groupBy('sanpham.masp', 'sanpham.tensp')
            ->orderByDesc('tong_ban')
            ->take(5)
            ->get();
        // ============================================================
        // 7. PHÁT HIỆN NGÀY LỄ SẮP TỚI
        // ============================================================
        $today      = Carbon::today();
        $next14Days = $today->copy()->addDays(14);
        $upcomingHoliday = null;
 
        $holidays = [
            '02-14' => 'Valentine (Lễ tình nhân)',
            '02-27' => 'Ngày Thầy thuốc Việt Nam',
            '03-08' => 'Quốc tế Phụ nữ',
            '06-01' => 'Quốc tế Thiếu nhi',
            '06-28' => 'Ngày Gia đình Việt Nam',
            '10-20' => 'Phụ nữ Việt Nam',
            '11-19' => 'Quốc tế Nam giới',
            '11-20' => 'Ngày Nhà giáo Việt Nam',
            '12-24' => 'Lễ Giáng sinh (Noel)',
        ];
 
        foreach ($holidays as $date => $name) {
            $holidayDate = Carbon::createFromFormat('Y-m-d', $today->year . '-' . $date);
 
            if ($holidayDate->isPast() && !$holidayDate->isToday()) {
                $holidayDate->addYear();
            }
 
            if ($holidayDate->between($today, $next14Days)) {
                $upcomingHoliday = [
                    'name'     => $name,
                    'date'     => $holidayDate->format('d/m'),
                    'daysLeft' => (int) $today->diffInDays($holidayDate),
                ];
                break;
            }
        }
 
        // ============================================================
        // 8. LẤY TỒN KHO TỪNG SẢN PHẨM (dùng cho AI)
        // ============================================================
        $stockMap = LoHang::selectRaw('masp, SUM(soluong_ton) as tong_ton')
            ->whereIn('masp', $topProducts30Days->pluck('masp'))
            ->groupBy('masp')
            ->pluck('tong_ton', 'masp');
 
        // ============================================================
        // 9. AI GỢI Ý NHẬP KHO – TỰ XÂY DỰNG, KHÔNG CẦN API NGOÀI
        // ============================================================
        $aiAdvice = $this->generateAdvice($topProducts30Days, $stockMap, $upcomingHoliday);
 
        // ============================================================
        // 10. TRẢ VỀ VIEW
        // ============================================================
        return view('admin.dashboard', compact(
            'donHangMoiCount',
            'doanhThuNgay',
            'tongSanPham',
            'tongNhanVien',
            'recentOrders',
            'aiAdvice',
            'revenueLabels',
            'revenueData',
            'catLabels',
            'catData',
            'lowStockProducts'
        ));
    }
 
    // ================================================================
    // AI TỰ XÂY DỰNG – Phân tích data + sinh câu gợi ý tự động
    // Không cần gọi API ngoài, không tốn tiền, không bị rate limit
    // ================================================================
    private function generateAdvice($topProducts, $stockMap, $upcomingHoliday): string
    {
        // Không có dữ liệu bán hàng → fallback
        if ($topProducts->isEmpty()) {
            return "Chưa có đủ dữ liệu bán hàng trong 30 ngày qua để đưa ra gợi ý. "
                 . "Hãy đảm bảo các đơn hàng đã được cập nhật trạng thái hoàn thành.";
        }
 
        // --------------------------------------------------------
        // BƯỚC 1: Phân tích từng sản phẩm
        // --------------------------------------------------------
        $urgent = []; // Tồn kho còn <= 3 ngày → cần nhập gấp
        $normal = []; // Tồn kho còn <= 7 ngày → nên nhập thêm
        $safe   = []; // Tồn kho còn > 7 ngày  → đang ổn
 
        foreach ($topProducts as $item) {
            $tocDoNgay   = $item->tong_ban > 0 ? round($item->tong_ban / 30, 1) : 0;
            $tonKho      = $stockMap[$item->masp] ?? 0;
            $ngayHetHang = ($tocDoNgay > 0) ? round($tonKho / $tocDoNgay) : 999;
 
            $entry = [
                'ten'         => $item->tensp,
                'toc_do'      => $tocDoNgay,
                'ton_kho'     => $tonKho,
                'ngay_het'    => $ngayHetHang,
                'phan_tram'   => 30, // % tăng mặc định, sẽ điều chỉnh theo ngày lễ
            ];
 
            if ($ngayHetHang <= 3) {
                $entry['phan_tram'] = 60;
                $urgent[] = $entry;
            } elseif ($ngayHetHang <= 7) {
                $entry['phan_tram'] = 35;
                $normal[] = $entry;
            } else {
                $safe[] = $entry;
            }
        }
 
        // --------------------------------------------------------
        // BƯỚC 2: Điều chỉnh % theo ngày lễ sắp tới
        // --------------------------------------------------------
        $holidayBonus = 0;
        $holidayText  = "";
 
        if ($upcomingHoliday) {
            $daysLeft = $upcomingHoliday['daysLeft'];
            $name     = $upcomingHoliday['name'];
            $date     = $upcomingHoliday['date'];
 
            if ($daysLeft <= 3) {
                $holidayBonus = 50;
                $holidayText  = "Chỉ còn {$daysLeft} ngày nữa là {$name} ({$date})";
            } elseif ($daysLeft <= 7) {
                $holidayBonus = 30;
                $holidayText  = "Sắp đến {$name} vào ngày {$date} (còn {$daysLeft} ngày)";
            } else {
                $holidayBonus = 15;
                $holidayText  = "Chuẩn bị cho {$name} vào ngày {$date} (còn {$daysLeft} ngày)";
            }
 
            // Cộng thêm % ngày lễ vào tất cả nhóm
            foreach ($urgent as &$u) { $u['phan_tram'] += $holidayBonus; }
            foreach ($normal as &$n) { $n['phan_tram'] += $holidayBonus; }
            foreach ($safe   as &$s) { $s['phan_tram']  = $holidayBonus; }
            unset($u, $n, $s);
        }
 
        // --------------------------------------------------------
        // BƯỚC 3: Sinh câu gợi ý tự nhiên
        // --------------------------------------------------------
        $advice = "";
 
        // Câu 1 – Mở đầu
        if ($holidayText) {
            $advice .= "{$holidayText}, nhu cầu mua hoa dự kiến tăng mạnh. ";
        } else {
            $sp1 = $topProducts->first()->tensp ?? 'các sản phẩm chủ lực';
            $advice .= "Dựa trên xu hướng bán hàng 30 ngày qua, {$sp1} đang dẫn đầu doanh số. ";
        }
 
        // Câu 2 – Cảnh báo khẩn cấp (nếu có)
        if (!empty($urgent)) {
            $tenSP  = implode(', ', array_column($urgent, 'ten'));
            $phanTram = $urgent[0]['phan_tram'];
            $ngayHet  = $urgent[0]['ngay_het'];
            $advice  .= "Cần nhập gấp {$tenSP} tăng ít nhất {$phanTram}% "
                      . "vì tồn kho chỉ còn đủ dùng trong {$ngayHet} ngày. ";
        }
 
        // Câu 3 – Gợi ý thêm (nếu có)
        if (!empty($normal)) {
            $tenSP    = implode(', ', array_column($normal, 'ten'));
            $phanTram = $normal[0]['phan_tram'];
            $advice  .= "Nên nhập thêm {$tenSP} khoảng {$phanTram}% "
                      . "để đảm bảo không thiếu hàng trong tuần tới.";
        } elseif (!empty($safe) && $holidayBonus > 0) {
            // Có ngày lễ nhưng tất cả hàng đều đủ → vẫn nên nhập thêm
            $tenSP    = $safe[0]['ten'];
            $phanTram = $safe[0]['phan_tram'];
            $advice  .= "Dù tồn kho hiện đang ổn, vẫn nên nhập thêm {$tenSP} khoảng {$phanTram}% "
                      . "để phục vụ nhu cầu tăng cao dịp lễ.";
        }
 
        // Fallback – tồn kho ổn, không có lễ
        if (empty($urgent) && empty($normal) && $holidayBonus === 0) {
            $sp1    = $topProducts->first()->tensp ?? 'sản phẩm chủ lực';
            $advice = "Tồn kho hiện tại đang ở mức an toàn cho tất cả sản phẩm. "
                    . "Tiếp tục duy trì nhịp nhập {$sp1} theo tốc độ hiện tại "
                    . "và theo dõi sát nhu cầu để điều chỉnh kịp thời.";
        }
 
        return trim($advice);
    }
}