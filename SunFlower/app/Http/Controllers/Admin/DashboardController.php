<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\NhanVien;
use App\Models\KhachHang;
use App\Models\DanhMuc;
use App\Models\NguyenLieu;
use App\Models\LoNguyenLieu;
use App\Models\DanhGia;
use App\Models\ChiTietDonHang;
use App\Models\LichSuKho;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        $kpiData = $this->getKpiData($today, $startOfMonth, $startOfLastMonth, $endOfLastMonth);
        $revenueCharts = $this->getRevenueCharts();
        $orderStatusData = $this->getOrderStatusData();
        $productStats = $this->getProductStats();
        $inventoryStats = $this->getInventoryStats();
        $customerStats = $this->getCustomerStats($startOfMonth, $startOfLastMonth, $endOfLastMonth);
        $recentOrders = $this->getRecentOrders();
        $revenueByCat = $this->getRevenueByCategory();
        $performanceData = $this->getPerformanceData();
        $notifications = $this->getNotifications($today, $kpiData['donHangMoiCount'], $inventoryStats['inventoryStats']['saphet'], $inventoryStats['inventoryStats']['hetHang'], $inventoryStats['inventoryStats']['sapHetHan']);
        $activityTimeline = $this->getActivityTimeline();
        $wasteData = $this->getWasteData();
        
        $topProducts30Days = Cache::remember('dashboard_top_products', 3600, function () {
            return ChiTietDonHang::join('donhang', 'chitiet_donhang.madon', '=', 'donhang.madon')
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
        });

        $upcomingHoliday = $this->getUpcomingHoliday($today);
        
        $aiAdviceCacheKey = 'ai_advice_' . $today->format('Y-m-d');
        $aiAdvice = Cache::remember($aiAdviceCacheKey, 86400, function () use ($topProducts30Days, $upcomingHoliday) {
            $stockMap = [];
            $productsForAI = SanPham::with('nguyenLieus')->whereIn('masp', $topProducts30Days->pluck('masp'))->get();
            foreach ($productsForAI as $product) {
                $stockMap[$product->masp] = $product->available_quantity;
            }
            return $this->generateAdvice($topProducts30Days, $stockMap, $upcomingHoliday);
        });

        $tongNhanVien = Cache::remember('dashboard_tong_nhanvien', 86400, fn() => NhanVien::count());
        $sparklineData = $this->getSparklineData();

        return view('admin.dashboard', array_merge(
            $kpiData,
            $revenueCharts,
            $productStats,
            $inventoryStats,
            $customerStats,
            $revenueByCat,
            $wasteData,
            $sparklineData,
            [
                'orderStatusData' => $orderStatusData,
                'recentOrders' => $recentOrders,
                'performanceData' => $performanceData,
                'notifications' => $notifications['notifications'],
                'totalUnread' => $notifications['totalUnread'],
                'activityTimeline' => $activityTimeline,
                'aiAdvice' => $aiAdvice,
                'tongNhanVien' => $tongNhanVien
            ]
        ));
    }

    private function getKpiData($today, $startOfMonth, $startOfLastMonth, $endOfLastMonth)
    {
        $yesterday = $today->copy()->subDay();

        $doanhThuNgay = DonHang::whereDate('ngaydat', $today)->where('trangthai', 'Đã hoàn thành')->sum('tongtien');
        $doanhThuHomQua = DonHang::whereDate('ngaydat', $yesterday)->where('trangthai', 'Đã hoàn thành')->sum('tongtien');
        $pctDoanhThuNgay = $doanhThuHomQua > 0 ? round((($doanhThuNgay - $doanhThuHomQua) / $doanhThuHomQua) * 100, 1) : ($doanhThuNgay > 0 ? 100 : 0);

        $doanhThuThang = DonHang::where('ngaydat', '>=', $startOfMonth)->where('trangthai', 'Đã hoàn thành')->sum('tongtien');
        $doanhThuThangTruoc = DonHang::whereBetween('ngaydat', [$startOfLastMonth, $endOfLastMonth])->where('trangthai', 'Đã hoàn thành')->sum('tongtien');
        $pctDoanhThuThang = $doanhThuThangTruoc > 0 ? round((($doanhThuThang - $doanhThuThangTruoc) / $doanhThuThangTruoc) * 100, 1) : ($doanhThuThang > 0 ? 100 : 0);

        $donHangNgay = DonHang::whereDate('ngaydat', $today)->count();
        $donHangHomQua = DonHang::whereDate('ngaydat', $yesterday)->count();
        $pctDonHangNgay = $donHangHomQua > 0 ? round((($donHangNgay - $donHangHomQua) / $donHangHomQua) * 100, 1) : ($donHangNgay > 0 ? 100 : 0);

        $donHangThang = DonHang::where('ngaydat', '>=', $startOfMonth)->count();
        $donHangThangTruoc = DonHang::whereBetween('ngaydat', [$startOfLastMonth, $endOfLastMonth])->count();
        $pctDonHangThang = $donHangThangTruoc > 0 ? round((($donHangThang - $donHangThangTruoc) / $donHangThangTruoc) * 100, 1) : ($donHangThang > 0 ? 100 : 0);

        $tongSanPham = SanPham::count();
        $spSapHetHang = NguyenLieu::whereColumn('tonkho_thucte', '<=', 'tonkho_toithieu')->where('tonkho_thucte', '>', 0)->count();
        $spHetHang = NguyenLieu::where('tonkho_thucte', '<=', 0)->count();
        $donHangMoiCount = DonHang::where('trangthai', 'Chờ xác nhận')->count();

        return compact(
            'doanhThuNgay', 'pctDoanhThuNgay',
            'doanhThuThang', 'pctDoanhThuThang',
            'donHangNgay', 'pctDonHangNgay',
            'donHangThang', 'pctDonHangThang',
            'tongSanPham', 'spSapHetHang', 'spHetHang', 'donHangMoiCount'
        );
    }

    private function getRevenueCharts()
    {
        return Cache::remember('dashboard_revenue_charts', 3600, function () {
            // 7 Days
            $revenueLabels7 = []; $revenueData7 = [];
            $dailyRevenues7 = DonHang::where('trangthai', 'Đã hoàn thành')
                ->where('ngaydat', '>=', Carbon::today()->subDays(6)->startOfDay())
                ->selectRaw('DATE(ngaydat) as date, SUM(tongtien) as total')
                ->groupBy('date')->pluck('total', 'date');
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $revenueLabels7[] = $date->format('d/m');
                $revenueData7[] = (int) ($dailyRevenues7[$date->format('Y-m-d')] ?? 0);
            }

            // 30 Days
            $revenueLabels30 = []; $revenueData30 = [];
            $dailyRevenues30 = DonHang::where('trangthai', 'Đã hoàn thành')
                ->where('ngaydat', '>=', Carbon::today()->subDays(29)->startOfDay())
                ->selectRaw('DATE(ngaydat) as date, SUM(tongtien) as total')
                ->groupBy('date')->pluck('total', 'date');
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $revenueLabels30[] = $date->format('d/m');
                $revenueData30[] = (int) ($dailyRevenues30[$date->format('Y-m-d')] ?? 0);
            }

            // 12 Months
            $revenueLabels12 = []; $revenueData12 = [];
            $monthlyRevenues = DonHang::where('trangthai', 'Đã hoàn thành')
                ->where('ngaydat', '>=', Carbon::now()->subMonths(11)->startOfMonth())
                ->selectRaw('YEAR(ngaydat) as y, MONTH(ngaydat) as m, SUM(tongtien) as total')
                ->groupBy('y', 'm')->get()
                ->keyBy(fn ($item) => $item->y . '-' . str_pad($item->m, 2, '0', STR_PAD_LEFT));
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $key = $month->format('Y-m');
                $revenueLabels12[] = 'T' . $month->format('m/Y');
                $revenueData12[] = (int) ($monthlyRevenues[$key]->total ?? 0);
            }

            return compact(
                'revenueLabels7', 'revenueData7',
                'revenueLabels30', 'revenueData30',
                'revenueLabels12', 'revenueData12'
            );
        });
    }

    private function getOrderStatusData()
    {
        return [
            'thanhcong' => DonHang::where('trangthai', 'Đã hoàn thành')->count(),
            'dangxuly' => DonHang::whereIn('trangthai', ['Chờ xác nhận', 'Đã xác nhận'])->count(),
            'danggiao' => DonHang::where('trangthai', 'Đang giao hàng')->count(),
            'dahuy' => DonHang::where('trangthai', 'Đã hủy')->count(),
        ];
    }

    private function getProductStats()
    {
        return Cache::remember('dashboard_product_stats', 3600, function () {
            $topProducts = ChiTietDonHang::join('donhang', 'chitiet_donhang.madon', '=', 'donhang.madon')
                ->join('sanpham', 'chitiet_donhang.masp', '=', 'sanpham.masp')
                ->leftJoin('danhmuc', 'sanpham.madm', '=', 'danhmuc.madm')
                ->where('donhang.trangthai', 'Đã hoàn thành')
                ->selectRaw('sanpham.masp, sanpham.tensp, sanpham.hinhanh, danhmuc.tendm,
                             SUM(chitiet_donhang.soluong) as tong_ban,
                             SUM(chitiet_donhang.soluong * chitiet_donhang.giaban) as doanh_thu')
                ->groupBy('sanpham.masp', 'sanpham.tensp', 'sanpham.hinhanh', 'danhmuc.tendm')
                ->orderByDesc('tong_ban')->take(10)->get();

            $topStockProducts = NguyenLieu::orderByDesc('tonkho_thucte')->take(10)->get()
                ->map(function ($nl) {
                    $nl->gia_tri_ton = $nl->tonkho_thucte * ($nl->gia_von ?? 0);
                    return $nl;
                });

            $lowStockProducts = NguyenLieu::whereColumn('tonkho_thucte', '<=', 'tonkho_toithieu')
                ->where('tonkho_thucte', '>', 0)->orderBy('tonkho_thucte')->take(10)->get();

            return compact('topProducts', 'topStockProducts', 'lowStockProducts');
        });
    }

    private function getInventoryStats()
    {
        $giaTriKho = Cache::remember('dashboard_giatrikho', 3600, function () {
            return NguyenLieu::selectRaw('SUM(tonkho_thucte * COALESCE(gia_von, 0)) as total')->value('total') ?? 0;
        });

        $hoaSapHetHang = NguyenLieu::whereColumn('tonkho_thucte', '<=', 'tonkho_toithieu')->where('tonkho_thucte', '>', 0)->count();
        $hoaHetHang = NguyenLieu::where('tonkho_thucte', '<=', 0)->count();
        $hoaSapHetHan = LoNguyenLieu::where('trangthai', '!=', 'Hết hàng')
            ->whereNotNull('hsd')->where('hsd', '<=', Carbon::now()->addDays(7))->where('hsd', '>', Carbon::now())->count();

        $inventoryStats = [
            'total' => NguyenLieu::count(),
            'giatri' => $giaTriKho,
            'saphet' => $hoaSapHetHang,
            'hetHang' => $hoaHetHang,
            'sapHetHan' => $hoaSapHetHan,
        ];

        return compact('inventoryStats');
    }

    private function getCustomerStats($startOfMonth, $startOfLastMonth, $endOfLastMonth)
    {
        $tongKhachHang = KhachHang::count();
        $tongKhachHangThangTruoc = KhachHang::where('created_at', '<', $startOfMonth)->count();
        $pctKhachHang = $tongKhachHangThangTruoc > 0 ? round((($tongKhachHang - $tongKhachHangThangTruoc) / $tongKhachHangThangTruoc) * 100, 1) : ($tongKhachHang > 0 ? 100 : 0);

        $khachHangMoi = KhachHang::where('created_at', '>=', $startOfMonth)->count();
        $khachHangMoiThangTruoc = KhachHang::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $pctKhachHangMoi = $khachHangMoiThangTruoc > 0 ? round((($khachHangMoi - $khachHangMoiThangTruoc) / $khachHangMoiThangTruoc) * 100, 1) : ($khachHangMoi > 0 ? 100 : 0);

        $khachQuayLai = Cache::remember('dashboard_khach_quay_lai', 3600, fn() => KhachHang::whereHas('donhangs', fn($q) => $q->where('trangthai', 'Đã hoàn thành'), '>=', 2)->count());
        $khachVIP = Cache::remember('dashboard_khach_vip', 3600, fn() => KhachHang::whereNotNull('hang_thanh_vien_id')->count());

        $customerStats = ['total' => $tongKhachHang, 'moi' => $khachHangMoi, 'quaylai' => $khachQuayLai, 'vip' => $khachVIP];

        $topCustomers = Cache::remember('dashboard_top_customers', 3600, function () {
            return KhachHang::withCount(['donhangs as so_don' => fn($q) => $q->where('trangthai', 'Đã hoàn thành')])
                ->withSum(['donhangs as tong_chi_tieu' => fn($q) => $q->where('trangthai', 'Đã hoàn thành')], 'tongtien')
                ->with('hangThanhVien')->orderByDesc('tong_chi_tieu')->take(5)->get();
        });

        return compact('tongKhachHang', 'pctKhachHang', 'khachHangMoi', 'pctKhachHangMoi', 'customerStats', 'topCustomers');
    }

    private function getRecentOrders()
    {
        return DonHang::with('khachhang')->orderBy('ngaydat', 'desc')->take(10)->get();
    }

    private function getRevenueByCategory()
    {
        return Cache::remember('dashboard_revenue_by_cat', 3600, function () {
            $revenueByCat = ChiTietDonHang::join('donhang', 'chitiet_donhang.madon', '=', 'donhang.madon')
                ->join('sanpham', 'chitiet_donhang.masp', '=', 'sanpham.masp')
                ->join('danhmuc', 'sanpham.madm', '=', 'danhmuc.madm')
                ->where('donhang.trangthai', 'Đã hoàn thành')
                ->selectRaw('danhmuc.tendm, SUM(chitiet_donhang.soluong * chitiet_donhang.giaban) as doanh_thu, SUM(chitiet_donhang.soluong) as so_luong')
                ->groupBy('danhmuc.madm', 'danhmuc.tendm')
                ->orderByDesc('doanh_thu')->get();

            return [
                'catBarLabels' => $revenueByCat->pluck('tendm')->toArray(),
                'catBarRevenue' => $revenueByCat->pluck('doanh_thu')->toArray(),
                'catBarQty' => $revenueByCat->pluck('so_luong')->toArray(),
            ];
        });
    }

    private function getPerformanceData()
    {
        return Cache::remember('dashboard_performance_data', 3600, function () {
            $now = Carbon::now();
            $lastMonth = Carbon::now()->subMonth();

            $thisMonthOrders = DonHang::whereMonth('ngaydat', $now->month)->whereYear('ngaydat', $now->year)->count();
            $thisMonthCompleted = DonHang::whereMonth('ngaydat', $now->month)->whereYear('ngaydat', $now->year)->where('trangthai', 'Đã hoàn thành')->count();
            $thisMonthCancelled = DonHang::whereMonth('ngaydat', $now->month)->whereYear('ngaydat', $now->year)->where('trangthai', 'Đã hủy')->count();
            
            $lastMonthOrders = DonHang::whereMonth('ngaydat', $lastMonth->month)->whereYear('ngaydat', $lastMonth->year)->count();
            $lastMonthCompleted = DonHang::whereMonth('ngaydat', $lastMonth->month)->whereYear('ngaydat', $lastMonth->year)->where('trangthai', 'Đã hoàn thành')->count();
            $lastMonthCancelled = DonHang::whereMonth('ngaydat', $lastMonth->month)->whereYear('ngaydat', $lastMonth->year)->where('trangthai', 'Đã hủy')->count();

            $aov = DonHang::where('trangthai', 'Đã hoàn thành')->avg('tongtien') ?? 0;
            $lastMonthAov = DonHang::whereMonth('ngaydat', $lastMonth->month)->whereYear('ngaydat', $lastMonth->year)->where('trangthai', 'Đã hoàn thành')->avg('tongtien') ?? 0;

            $conversionRate = $thisMonthOrders > 0 ? round(($thisMonthCompleted / $thisMonthOrders) * 100, 1) : 0;
            $lastMonthConversionRate = $lastMonthOrders > 0 ? round(($lastMonthCompleted / $lastMonthOrders) * 100, 1) : 0;
            
            $thisMonthWaste = \App\Models\ChiTietPhieuHuy::join('phieu_huy_hang', 'chi_tiet_phieu_huy.id_phieu_huy', '=', 'phieu_huy_hang.id')
                ->where('phieu_huy_hang.trang_thai', 'Đã duyệt')->whereMonth('phieu_huy_hang.created_at', $now->month)->whereYear('phieu_huy_hang.created_at', $now->year)
                ->sum('chi_tiet_phieu_huy.so_luong_huy');
            $thisMonthSold = abs(LichSuKho::whereIn('loai_gd', ['order_complete', 'export'])->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('soluong'));
            $thisMonthConsumed = $thisMonthSold + $thisMonthWaste;
            $wasteRate = $thisMonthConsumed > 0 ? round(($thisMonthWaste / $thisMonthConsumed) * 100, 1) : 0;

            $lastMonthWaste = \App\Models\ChiTietPhieuHuy::join('phieu_huy_hang', 'chi_tiet_phieu_huy.id_phieu_huy', '=', 'phieu_huy_hang.id')
                ->where('phieu_huy_hang.trang_thai', 'Đã duyệt')->whereMonth('phieu_huy_hang.created_at', $lastMonth->month)->whereYear('phieu_huy_hang.created_at', $lastMonth->year)
                ->sum('chi_tiet_phieu_huy.so_luong_huy');
            $lastMonthSold = abs(LichSuKho::whereIn('loai_gd', ['order_complete', 'export'])->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('soluong'));
            $lastMonthConsumed = $lastMonthSold + $lastMonthWaste;
            $lastMonthWasteRate = $lastMonthConsumed > 0 ? round(($lastMonthWaste / $lastMonthConsumed) * 100, 1) : 0;
            
            $thisMonthImport = LichSuKho::where('loai_gd', 'import')->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('soluong');
            $lastMonthImport = LichSuKho::where('loai_gd', 'import')->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('soluong');

            $cancelRate = $thisMonthOrders > 0 ? round(($thisMonthCancelled / $thisMonthOrders) * 100, 1) : 0;
            $lastMonthCancelRate = $lastMonthOrders > 0 ? round(($lastMonthCancelled / $lastMonthOrders) * 100, 1) : 0;

            $tongKhachHang = KhachHang::count();
            $khachQuayLai = KhachHang::whereHas('donhangs', fn($q) => $q->where('trangthai', 'Đã hoàn thành'), '>=', 2)->count();
            $returnCustomerRate = $tongKhachHang > 0 ? round(($khachQuayLai / $tongKhachHang) * 100, 1) : 0;

            return [
                'aov' => $aov,
                'aov_trend' => $lastMonthAov > 0 ? round((($aov - $lastMonthAov) / $lastMonthAov) * 100, 1) : 0,
                'conversion' => $conversionRate,
                'conversion_trend' => round($conversionRate - $lastMonthConversionRate, 1),
                'cancel' => $cancelRate,
                'cancel_trend' => round($cancelRate - $lastMonthCancelRate, 1),
                'waste' => $wasteRate,
                'waste_trend' => round($wasteRate - $lastMonthWasteRate, 1),
                'import' => $thisMonthImport,
                'import_trend' => $lastMonthImport > 0 ? round((($thisMonthImport - $lastMonthImport) / $lastMonthImport) * 100, 1) : 0,
                'returnRate' => $returnCustomerRate,
            ];
        });
    }

    private function getNotifications($today, $donHangMoiCount, $hoaSapHetHang, $hoaHetHang, $hoaSapHetHan)
    {
        $notifications = [
            ['type' => 'order', 'icon' => 'fa-shopping-bag', 'color' => 'primary', 'label' => 'Đơn hàng mới', 'count' => $donHangMoiCount],
            ['type' => 'stock', 'icon' => 'fa-box-open', 'color' => 'warning', 'label' => 'Hàng sắp hết', 'count' => $hoaSapHetHang],
            ['type' => 'outstock', 'icon' => 'fa-times-circle', 'color' => 'danger', 'label' => 'Hết hàng', 'count' => $hoaHetHang],
            ['type' => 'cancel', 'icon' => 'fa-ban', 'color' => 'danger', 'label' => 'Đơn bị hủy hôm nay', 'count' => DonHang::whereDate('ngaydat', $today)->where('trangthai', 'Đã hủy')->count()],
            ['type' => 'customer', 'icon' => 'fa-user-plus', 'color' => 'success', 'label' => 'Khách mới hôm nay', 'count' => KhachHang::whereDate('created_at', $today)->count()],
            ['type' => 'review', 'icon' => 'fa-star', 'color' => 'info', 'label' => 'Đánh giá mới', 'count' => DanhGia::whereDate('created_at', $today)->count()],
            ['type' => 'expiry', 'icon' => 'fa-clock', 'color' => 'warning', 'label' => 'Lô sắp hết hạn', 'count' => $hoaSapHetHan],
        ];

        return [
            'notifications' => $notifications,
            'totalUnread' => collect($notifications)->sum('count')
        ];
    }

    private function getActivityTimeline()
    {
        $activityTimeline = collect();

        $recentOrdersTimeline = DonHang::with('khachhang')->orderBy('ngaydat', 'desc')->take(5)->get()
            ->map(fn($order) => [
                'time' => Carbon::parse($order->ngaydat),
                'icon' => 'fa-shopping-cart', 'color' => 'primary',
                'title' => 'Đơn hàng mới #' . $order->madon,
                'desc' => ($order->khachhang->hoten ?? 'Khách vãng lai') . ' - ' . number_format($order->tongtien, 0, ',', '.') . 'đ',
            ]);
        $activityTimeline = $activityTimeline->merge($recentOrdersTimeline);

        $recentCustomers = KhachHang::orderBy('created_at', 'desc')->take(3)->get()
            ->map(fn($kh) => [
                'time' => Carbon::parse($kh->created_at),
                'icon' => 'fa-user-plus', 'color' => 'success',
                'title' => 'Khách hàng mới đăng ký',
                'desc' => $kh->hoten . ' (' . ($kh->email ?? $kh->sdt) . ')',
            ]);
        $activityTimeline = $activityTimeline->merge($recentCustomers);

        $recentInventory = LichSuKho::with(['nguyenLieu', 'nhanvien'])->orderBy('created_at', 'desc')->take(3)->get()
            ->map(fn($log) => [
                'time' => Carbon::parse($log->created_at),
                'icon' => 'fa-warehouse', 'color' => $log->type_badge,
                'title' => $log->type_label,
                'desc' => ($log->nguyenLieu->ten_nl ?? 'N/A') . ' (' . $log->soluong . ' đv) - ' . ($log->nhanvien->hoten ?? 'Hệ thống'),
            ]);
        $activityTimeline = $activityTimeline->merge($recentInventory);

        return $activityTimeline->sortByDesc('time')->take(10)->values();
    }

    private function getWasteData()
    {
        return Cache::remember('dashboard_waste_data', 3600, function () {
            $thirtyDaysAgoDate = Carbon::today()->subDays(30);
            $tongBan30Ngay = ChiTietDonHang::join('donhang', 'chitiet_donhang.madon', '=', 'donhang.madon')
                ->where('donhang.ngaydat', '>=', $thirtyDaysAgoDate)->where('donhang.trangthai', 'Đã hoàn thành')->sum('chitiet_donhang.soluong');
            $tongHuy30Ngay = LichSuKho::where('loai_gd', 'waste')->where('created_at', '>=', $thirtyDaysAgoDate)->sum('soluong');
            $tongHoaXuat = $tongBan30Ngay + $tongHuy30Ngay;
            $tyLeHaoHut = $tongHoaXuat > 0 ? round(($tongHuy30Ngay / $tongHoaXuat) * 100, 2) : 0;

            return compact('tongBan30Ngay', 'tongHuy30Ngay', 'tyLeHaoHut');
        });
    }

    private function getSparklineData()
    {
        return Cache::remember('dashboard_sparkline_data', 3600, function () {
            $sparkRevenue = []; $sparkOrders = [];
            for ($i = 6; $i >= 0; $i--) {
                $d = Carbon::today()->subDays($i);
                $sparkRevenue[] = (int) DonHang::whereDate('ngaydat', $d)->where('trangthai', 'Đã hoàn thành')->sum('tongtien');
                $sparkOrders[] = DonHang::whereDate('ngaydat', $d)->count();
            }
            return compact('sparkRevenue', 'sparkOrders');
        });
    }

    private function getUpcomingHoliday($today)
    {
        $next14Days = $today->copy()->addDays(14);
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
                return [
                    'name' => $name,
                    'date' => $holidayDate->format('d/m'),
                    'daysLeft' => (int) $today->diffInDays($holidayDate),
                ];
            }
        }
        return null;
    }

    private function generateAdvice($topProducts, $stockMap, $upcomingHoliday): string
    {
        if ($topProducts->isEmpty()) {
            return "Chưa có đủ dữ liệu bán hàng trong 30 ngày qua để đưa ra gợi ý. "
                 . "Hãy đảm bảo các đơn hàng đã được cập nhật trạng thái hoàn thành.";
        }
 
        $urgent = []; $normal = []; $safe   = [];
 
        foreach ($topProducts as $item) {
            $tocDoNgay   = $item->tong_ban > 0 ? round($item->tong_ban / 30, 1) : 0;
            $tonKho      = $stockMap[$item->masp] ?? 0;
            $ngayHetHang = ($tocDoNgay > 0) ? round($tonKho / $tocDoNgay) : 999;
 
            $entry = [
                'ten' => $item->tensp, 'toc_do' => $tocDoNgay,
                'ton_kho' => $tonKho, 'ngay_het' => $ngayHetHang, 'phan_tram' => 30,
            ];
 
            if ($ngayHetHang <= 3) {
                $entry['phan_tram'] = 60; $urgent[] = $entry;
            } elseif ($ngayHetHang <= 7) {
                $entry['phan_tram'] = 35; $normal[] = $entry;
            } else {
                $safe[] = $entry;
            }
        }
 
        $holidayBonus = 0; $holidayText  = "";
 
        if ($upcomingHoliday) {
            $daysLeft = $upcomingHoliday['daysLeft'];
            $name     = $upcomingHoliday['name'];
            $date     = $upcomingHoliday['date'];
 
            if ($daysLeft <= 3) {
                $holidayBonus = 50; $holidayText  = "Chỉ còn {$daysLeft} ngày nữa là {$name} ({$date})";
            } elseif ($daysLeft <= 7) {
                $holidayBonus = 30; $holidayText  = "Sắp đến {$name} vào ngày {$date} (còn {$daysLeft} ngày)";
            } else {
                $holidayBonus = 15; $holidayText  = "Chuẩn bị cho {$name} vào ngày {$date} (còn {$daysLeft} ngày)";
            }
 
            foreach ($urgent as &$u) { $u['phan_tram'] += $holidayBonus; }
            foreach ($normal as &$n) { $n['phan_tram'] += $holidayBonus; }
            foreach ($safe   as &$s) { $s['phan_tram']  = $holidayBonus; }
            unset($u, $n, $s);
        }
 
        $advice = "";
        if ($holidayText) {
            $advice .= "{$holidayText}, nhu cầu mua hoa dự kiến tăng mạnh. ";
        } else {
            $sp1 = $topProducts->first()->tensp ?? 'các sản phẩm chủ lực';
            $advice .= "Dựa trên xu hướng bán hàng 30 ngày qua, {$sp1} đang dẫn đầu doanh số. ";
        }
 
        if (!empty($urgent)) {
            $tenSP  = implode(', ', array_column($urgent, 'ten'));
            $phanTram = $urgent[0]['phan_tram'];
            $ngayHet  = $urgent[0]['ngay_het'];
            $advice  .= "Cần nhập gấp {$tenSP} tăng ít nhất {$phanTram}% vì tồn kho chỉ còn đủ dùng trong {$ngayHet} ngày. ";
        }
 
        if (!empty($normal)) {
            $tenSP    = implode(', ', array_column($normal, 'ten'));
            $phanTram = $normal[0]['phan_tram'];
            $advice  .= "Nên nhập thêm {$tenSP} khoảng {$phanTram}% để đảm bảo không thiếu hàng trong tuần tới.";
        } elseif (!empty($safe) && $holidayBonus > 0) {
            $tenSP    = $safe[0]['ten'];
            $phanTram = $safe[0]['phan_tram'];
            $advice  .= "Dù tồn kho hiện đang ổn, vẫn nên nhập thêm {$tenSP} khoảng {$phanTram}% để phục vụ nhu cầu tăng cao dịp lễ.";
        }
 
        if (empty($urgent) && empty($normal) && $holidayBonus === 0) {
            $sp1    = $topProducts->first()->tensp ?? 'sản phẩm chủ lực';
            $advice = "Tồn kho hiện tại đang ở mức an toàn cho tất cả sản phẩm. Tiếp tục duy trì nhịp nhập {$sp1} theo tốc độ hiện tại và theo dõi sát nhu cầu để điều chỉnh kịp thời.";
        }
 
        return trim($advice);
    }
}