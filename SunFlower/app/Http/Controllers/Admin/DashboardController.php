<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\NhanVien;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Thống kê số lượng
        $donHangMoiCount = DonHang::where('trangthai', 'Chờ xác nhận')->count();
        
        // Doanh thu ngày hôm nay (giả định cột tongtien và ngaydat)
        $doanhThuNgay = DonHang::whereDate('ngaydat', Carbon::today())
                                ->where('trangthai', 'Đã hoàn thành')
                                ->sum('tongtien');

        $tongSanPham = SanPham::count();
        
        $tongNhanVien = NhanVien::count();

        // 2. Lấy 5 đơn hàng mới nhất để hiện ở bảng
        $recentOrders = DonHang::with('khachhang') // Eager load quan hệ khách hàng
                                ->orderBy('ngaydat', 'desc')
                                ->take(5)
                                ->get();

        return view('admin.dashboard', compact(
            'donHangMoiCount', 
            'doanhThuNgay', 
            'tongSanPham', 
            'tongNhanVien',
            'recentOrders'
        ));
    }
}