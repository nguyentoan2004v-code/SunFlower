# 🌻 SunFlower - Hệ thống Quản lý Cửa hàng Bán Hoa

**SunFlower** là một hệ thống ứng dụng web nguyên khối (Monolithic Application) được thiết kế và phát triển nhằm số hóa toàn diện quy trình vận hành của một cửa hàng kinh doanh hoa tươi. 

Dự án không chỉ giải quyết bài toán bán hàng cơ bản mà còn mở rộng sâu vào khâu quản lý nhân sự và phân ca lịch làm việc, mang lại một giải pháp quản trị tập trung và hiệu quả.

---

## 🌟 Các tính năng nổi bật

Hệ thống được chia thành 4 phân hệ (Module) cốt lõi:

### 1. Phân hệ Sản phẩm & Kho
- Quản lý phân cấp danh mục hoa.
- Theo dõi chi tiết thông tin sản phẩm: Giá bán, giá khuyến mãi định kỳ.
- Ràng buộc dữ liệu chặt chẽ tránh xóa nhầm danh mục đang có sản phẩm.

### 2. Phân hệ Bán hàng & Giao dịch
- Quản lý thông tin khách hàng thân thiết.
- Xử lý giỏ hàng và Đơn hàng chi tiết (Lưu vết giá bán tại thời điểm mua).
- Tự động hóa tính toán Hóa đơn (Tổng tiền, Thuế) với quy tắc quan hệ 1-1 chặt chẽ với Đơn hàng.

### 3. Phân hệ Nhân sự & Phân ca
- Quản lý hồ sơ nhân viên (có tính năng tự tham chiếu để xác định cấp trên/quản lý trực tiếp).
- Thiết lập và quản lý danh mục Ca làm việc.
- Phân công công việc chi tiết cho từng nhân sự theo ca.

---

## 🛠 Nền tảng Công nghệ

Dự án được xây dựng dựa trên các tiêu chuẩn công nghệ web hiện đại và cấu trúc dữ liệu tối ưu:

- **Ngôn ngữ Backend:** PHP 8.x
- **Core Framework:** Laravel 12.x (Mô hình MVC)
- **Cơ sở dữ liệu:** MySQL (Thiết kế chuẩn hóa với 10 bảng dữ liệu liên kết)
- **Giao diện (Frontend):** Laravel Blade Template kết hợp HTML/CSS/JS thuần
- **Quản lý mã nguồn:** Git & GitHub Workflow

---

## 🗄️ Cấu trúc Dữ liệu (Database Schema)
Hệ thống sở hữu bộ cơ sở dữ liệu được thiết kế tối ưu với các ràng buộc toàn vẹn (Foreign Key Constraints) khắt khe:
- `danhmuc` (Danh mục) 1 - N `sanpham` (Sản phẩm)
- `khachhang` (Khách hàng) 1 - N `donhang` (Đơn hàng)
- `donhang` (Đơn hàng) 1 - 1 `hoadon` (Hóa đơn)
- `donhang` (Đơn hàng) N - N `sanpham` (Sản phẩm) -> Trích xuất bảng trung gian `chitietdonhang`
- `nhanvien` (Nhân sự) N - N `lichlamviec` (Ca làm) -> Trích xuất bảng trung gian `phancong`

---

**Dự án được thiết kế và phát triển bởi:**
👨‍💻 **[Nguyễn Việt Toàn]** - Backend Team Leader (Database & System Logic)  
👨‍💻 **[Lê Chí Phong]** - Frontend Developer (UI/UX & View Integration)