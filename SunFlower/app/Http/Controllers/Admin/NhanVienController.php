<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NhanVien;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class NhanVienController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = auth()->guard('nhanvien')->user();
                
                // Chốt chặn chỉ cho Quản lý cửa hàng
                if (!$user->hasRole('Quản lý Cửa hàng')) {
                    abort(403, 'Chỉ Quản lý Cửa hàng mới có quyền thao tác trong module Nhân sự!');
                }
                
                return $next($request);
            }),
        ];
    }
    // Hiển thị danh sách nhân viên kèm vai trò
    public function index()
    {
        // Eager loading vaitros và quanly để tối ưu truy vấn
        $nhanviens = NhanVien::with(['vaitros', 'quanly'])->paginate(10);
        return view('admin.nhanvien.index', compact('nhanviens'));
    }

    // Hiển thị form cấp quyền cho 1 nhân viên cụ thể
    public function editRole($manv)
    {
        $nhanvien = NhanVien::with('vaitros')->findOrFail($manv);
        $vaitros = VaiTro::all(); 
        
        return view('admin.nhanvien.edit_role', compact('nhanvien', 'vaitros'));
    }

    // Xử lý cập nhật quyền vào bảng vaitro_nhanvien
    public function updateRole(Request $request, $manv)
    {
        $request->validate([
            'vaitros' => 'nullable|array',
            'vaitros.*' => 'exists:vaitro,mavt'
        ]);

        $nhanvien = NhanVien::findOrFail($manv);
        
        // sync() tự động thêm, xóa các bản ghi trong bảng trung gian sao cho khớp với mảng $request->vaitros
        $nhanvien->vaitros()->sync($request->vaitros);

        return redirect()->route('admin.nhanvien.index')->with('success', 'Cập nhật phân quyền thành công cho nhân viên: ' . $nhanvien->hoten);
    }

    public function create()
    {
        // Lấy danh sách nhân viên hiện tại để gán làm "Quản lý trực tiếp" (nếu có)
        $quanlys = NhanVien::all();
        return view('admin.nhanvien.create', compact('quanlys'));
    }

    // 2. Xử lý lưu nhân viên mới vào Database
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'hoten' => 'required|string|max:40',
            'email' => 'required|email|unique:nhanvien,email|max:100',
            'ngaysinh' => 'required|date',
            'sdt' => 'required|string|unique:nhanvien,sdt|max:15',
            'luong' => 'required|numeric|min:0',
            'password' => 'required|string|min:6',
            'maquanly' => 'nullable|exists:nhanvien,manv',
        ], [
            'email.unique' => 'Email này đã được sử dụng.',
            'sdt.unique' => 'Số điện thoại này đã được sử dụng.',
        ]);

        // Tự động sinh mã nhân viên (NV + 8 số)
        $lastNV = NhanVien::orderBy('manv', 'desc')->first();
        $nextId = 1;
        if ($lastNV) {
            // Cắt chữ "NV" lấy phần số, cộng thêm 1
            $lastId = (int) substr($lastNV->manv, 2);
            $nextId = $lastId + 1;
        }
        // Ép định dạng 8 chữ số, ví dụ: NV00000003
        $maNV = 'NV' . str_pad($nextId, 8, '0', STR_PAD_LEFT);

        // Lưu vào DB
        NhanVien::create([
            'manv' => $maNV,
            'hoten' => $request->hoten,
            'email' => $request->email,
            'ngaysinh' => $request->ngaysinh,
            'sdt' => $request->sdt,
            'luong' => $request->luong,
            'maquanly' => $request->maquanly,
            // Mã hóa mật khẩu trước khi lưu vào DB
            'password' => Hash::make($request->password), 
        ]);

        return redirect()->route('admin.nhanvien.index')->with('success', 'Đã thêm nhân viên mới thành công: ' . $request->hoten);
    }
    // 3. Hiển thị form Sửa thông tin nhân viên
    public function edit($manv)
    {
        $nhanvien = NhanVien::findOrFail($manv);
        // Lấy danh sách nhân viên để làm Quản lý trực tiếp (loại trừ chính nhân viên đang sửa để không bị tự quản lý mình)
        $quanlys = NhanVien::where('manv', '!=', $manv)->get(); 
        
        return view('admin.nhanvien.edit', compact('nhanvien', 'quanlys'));
    }

    // 4. Xử lý Cập nhật dữ liệu vào Database
    public function update(Request $request, $manv)
    {
        $nhanvien = NhanVien::findOrFail($manv);

        $request->validate([
            'hoten' => 'required|string|max:40',
            // Rule unique cần loại trừ ID của chính nhân viên này để không báo lỗi trùng lặp khi không sửa email/sdt
            'email' => 'required|email|max:100|unique:nhanvien,email,' . $manv . ',manv',
            'ngaysinh' => 'required|date',
            'sdt' => 'required|string|max:15|unique:nhanvien,sdt,' . $manv . ',manv',
            'luong' => 'required|numeric|min:0',
            'password' => 'nullable|string|min:6', // Mật khẩu cho phép null (nếu không nhập thì giữ nguyên mk cũ)
            'maquanly' => 'nullable|exists:nhanvien,manv',
        ]);

        $dataToUpdate = [
            'hoten' => $request->hoten,
            'email' => $request->email,
            'ngaysinh' => $request->ngaysinh,
            'sdt' => $request->sdt,
            'luong' => $request->luong,
            'maquanly' => $request->maquanly,
        ];

        // Nếu Quản lý có gõ mật khẩu mới thì mới tiến hành mã hóa và cập nhật
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $nhanvien->update($dataToUpdate);

        return redirect()->route('admin.nhanvien.index')->with('success', 'Đã cập nhật thông tin nhân viên thành công!');
    }

    // 5. Xử lý Xóa nhân viên
    public function destroy($manv)
    {
        $nhanvien = NhanVien::findOrFail($manv);

        // Kiểm tra xem nhân viên này có đang làm quản lý của ai khác không (hàm capduoi() đã viết ở Model)
        if ($nhanvien->capduoi()->count() > 0) {
            return redirect()->route('admin.nhanvien.index')->with('error', 'Không thể xóa! Nhân viên này đang quản lý các nhân viên khác.');
        }

        $nhanvien->delete();
        return redirect()->route('admin.nhanvien.index')->with('success', 'Đã xóa nhân viên thành công!');
    }
}