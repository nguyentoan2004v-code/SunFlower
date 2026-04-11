# 🌻 SunFlower - Hệ thống Quản lý Cửa hàng Bán Hoa

**SunFlower** là một hệ thống ứng dụng web nguyên khối (Monolithic Application) được thiết kế và phát triển nhằm số hóa toàn diện quy trình vận hành của một cửa hàng kinh doanh hoa tươi. 

Dự án không chỉ giải quyết bài toán bán hàng cơ bản mà còn mở rộng sâu vào khâu quản lý nhân sự và phân ca lịch làm việc, mang lại một giải pháp quản trị tập trung, hiệu quả và tối ưu doanh thu.

---

## 🌟 Các tính năng nổi bật

Hệ thống được chia thành **4 phân hệ (Module) cốt lõi**:

### 1. Phân hệ Sản phẩm & Kho
- **Quản lý danh mục:** Phân cấp danh mục hoa rõ ràng, logic.
- **Quản lý sản phẩm:** Theo dõi chi tiết thông tin hoa, giá bán và các chương trình khuyến mãi định kỳ.
- **Ràng buộc an toàn:** Xử lý dữ liệu chặt chẽ, ngăn chặn việc xóa nhầm danh mục đang chứa sản phẩm.

### 2. Phân hệ Bán hàng & Giao dịch
- **Quản lý khách hàng:** Lưu trữ và quản lý thông tin khách hàng thân thiết.
- **Xử lý đơn hàng:** Quản lý giỏ hàng và chi tiết đơn hàng (lưu vết chính xác giá bán tại thời điểm giao dịch).
- **Xuất hóa đơn tự động:** Tự động hóa tính toán tổng tiền, thuế với quy tắc quan hệ 1-1 chặt chẽ với Đơn hàng.

### 3. Phân hệ Nhân sự & Phân ca
- **Hồ sơ nhân viên:** Quản lý thông tin cá nhân với cấu trúc *tự tham chiếu (Self-referencing)* để xác định chính xác cấp trên/quản lý trực tiếp.
- **Quản lý lịch làm việc:** Thiết lập và quản lý danh mục các ca làm việc trong tuần/tháng.
- **Phân công ca trực:** Phân công chi tiết và theo dõi trạng thái nhiệm vụ của từng nhân sự theo ca.

### 4. Phân hệ Báo cáo & Thống kê (Dashboard)
- **Trực quan hóa doanh thu:** Theo dõi dòng tiền bán hàng theo ngày/tuần/tháng dựa trên dữ liệu xuất Hóa đơn thực tế.
- **Thống kê sản phẩm:** Phân tích top các loại hoa và danh mục bán chạy nhất để tối ưu nhập kho.
- **Quản trị nhân sự:** Đánh giá hiệu suất làm việc của nhân viên dựa trên lịch sử phân công.

---

## 🛠 Nền tảng Công nghệ

Dự án được xây dựng dựa trên các tiêu chuẩn công nghệ web hiện đại và cấu trúc dữ liệu tối ưu:
- **Ngôn ngữ Backend:** PHP 8.x
- **Core Framework:** Laravel 12.x (Mô hình kiến trúc MVC)
- **Cơ sở dữ liệu:** MySQL (Thiết kế chuẩn hóa với 10 bảng dữ liệu liên kết)
- **Giao diện (Frontend):** Laravel Blade Template kết hợp HTML/CSS/JS thuần
- **Quản lý mã nguồn:** Git & GitHub Workflow

---

## 🗄️ Cấu trúc Dữ liệu (Database Schema)

Hệ thống sở hữu bộ cơ sở dữ liệu được thiết kế tối ưu với các ràng buộc toàn vẹn (Foreign Key Constraints) khắt khe:
- `danhmuc` (Danh mục) **1 - N** `sanpham` (Sản phẩm)
- `khachhang` (Khách hàng) **1 - N** `donhang` (Đơn hàng)
- `donhang` (Đơn hàng) **1 - 1** `hoadon` (Hóa đơn)
- `donhang` (Đơn hàng) **N - N** `sanpham` (Sản phẩm) ➔ *Trích xuất bảng trung gian `chitietdonhang`*
- `nhanvien` (Nhân sự) **N - N** `lichlamviec` (Ca làm) ➔ *Trích xuất bảng trung gian `phancong`*

---

## 🤝 Đội ngũ Phát triển

Dự án được thiết kế và phát triển bởi:
- 👨‍💻 **[Nguyễn Việt Toàn]** - Backend Team Leader *(Database & System Logic)*
- 👨‍💻 **[Lê Chí Phong]** - Frontend Developer *(UI/UX & View Integration)*
