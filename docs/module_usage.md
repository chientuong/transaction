# Hướng dẫn sử dụng các Module

Tài liệu này hướng dẫn cách sử dụng các tính năng chính trong giao diện quản trị (Filament) của hệ thống Quản lý Giao dịch.

## 1. Module Quản trị Hệ thống
### Quản lý Tài khoản (User)
- **Truy cập:** `Quản trị hệ thống` -> `Quản lý tài khoản`.
- **Tính năng:**
    - Tạo mới, chỉnh sửa thông tin nhân viên.
    - Phân vai trò (Roles) cho nhân viên.
    - **Reset MK:** Nút Reset mật khẩu sẽ tạo mật khẩu ngẫu nhiên và hiển thị trên màn hình.
- **Lưu ý:** Không thể tự đổi vai trò của chính mình để bảo mật.

### Quản lý Vai trò (Role)
- **Truy cập:** `Quản trị hệ thống` -> `Quản lý vai trò`.
- **Tính năng:**
    - Định nghĩa các nhóm quyền: `SUPER_ADMIN`, `ADMIN`, `OPERATOR`.
    - Phân chi tiết các quyền hạn như: Xác nhận giao dịch, Xem báo cáo, Cấu hình hệ thống.

---

## 2. Module Nghiệp vụ Giao dịch
### Giao dịch thanh toán
- **Truy cập:** `Nghiệp vụ giao dịch` -> `Giao dịch thanh toán`.
- **Tính năng:**
    - Theo dõi danh sách tất cả giao dịch từ API hoặc SePay Webhook.
    - **Chi tiết:** Xem thông tin mã QR, nội dung chuyển khoản, và lịch sử gọi Webhook đi.
    - **Thao tác vận hành:** Xác nhận đã nhận tiền, Từ chối, hoặc Tạm giữ giao dịch kèm lý do.

---

## 3. Module Cấu hình Chung
### Tài khoản ngân hàng
- **Truy cập:** `Cấu hình chung` -> `Tài khoản ngân hàng`.
- **Tính năng:**
    - Thêm các số tài khoản nhận tiền (Vietcombank, MB, TPBank, v.v.).
    - Chọn Ngân hàng từ danh sách động được cấu hình trong Hệ thống.
    - **Tạm dừng:** Chức năng thay thế cho Xóa để giữ an toàn dữ liệu lịch sử.

### Nguồn thu (Payment Prefix)
- **Truy cập:** `Cấu hình chung` -> `Danh sách nguồn thu`.
- **Tính năng:**
    - Định nghĩa các tiền tố mã giao dịch (ví dụ: SHOPEE, LAZADA, WEB).
    - Cấu hình Webhook URL để đẩy dữ liệu sang hệ thống bên thứ 3 sau khi giao dịch được xác nhận.

---

## 4. Cài đặt Hệ thống
- **Truy cập:** `Cấu hình hệ thống` -> `Cài đặt hệ thống`.
- **Tính năng:**
    - **Sepay API Key:** Dùng để đồng bộ dữ liệu với SePay.
    - **Hệ thống API Token:** Token dùng cho header `Authorization` khi gọi API từ ngoài vào.
    - **Thời gian giao dịch:** TTL (phút) trước khi giao dịch tự động hết hạn.
    - **Danh sách Ngân hàng:** Cấu hình mã và tên ngân hàng hệ thống hỗ trợ.
    - **Cấu hình Webhook:** Danh sách các đầu nhận dữ liệu khi có giao dịch thành công.
