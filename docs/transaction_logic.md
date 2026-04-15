# Logic Xác nhận Giao dịch (Confirm Transaction)

Tài liệu này giải thích luồng xử lý từ khi có dòng tiền vào đến khi giao dịch được xác nhận thành công.

## 1. Các trạng thái Giao dịch
- **Sync Status (Kỹ thuật):**
    - `PENDING`: Đang chờ tiền vào.
    - `RECEIVED_SIGNAL`: Đã nhận được tín hiệu Webhook từ SePay khớp mã.
    - `SUCCESS`: Webhook đẩy đi sang bên thứ 3 đã thành công.
    - `FAILED`: Lỗi trong quá trình xử lý hoặc đẩy Webhook.
- **Ops Status (Vận hành):**
    - `PENDING`: Chờ xử lý.
    - `CONFIRMED`: Nhân viên đã xác nhận hoặc hệ thống tự động xác nhận.
    - `REJECTED`: Từ chối giao dịch.
    - `ON_HOLD`: Tạm giữ để kiểm tra thêm.

---

## 2. Luồng xử lý Tự động (Webhook SePay)
Khi SePay gửi Webhook đến endpoint `/api/sepay-webhook`:

1. **Xác thực:** Kiểm tra Token Authorization.
2. **Lưu nhật ký:** Ghi dữ liệu thô vào bảng `sepay_transactions`.
3. **Khớp tài khoản:** Dựa vào `gateway` và `accountNumber` từ SePay để tìm `BankAccount` tương ứng trong hệ thống.
4. **Khớp giao dịch:** 
    - Tìm giao dịch có `sync_status = PENDING`.
    - `bank_account_id` phải khớp.
    - `amount` phải khớp chính xác.
    - **Nội dung:** Tìm mã `transaction_code` (ví dụ: SHOPEE_2026...) xuất hiện trong nội dung chuyển khoản (`content`) của SePay.
5. **Cập nhật:** Nếu khớp, chuyển `sync_status` sang `RECEIVED_SIGNAL`.

---

## 3. Luồng Xác nhận Vận hành (Manual Confirm)
Nhân viên sử dụng giao diện Filament để xác nhận thủ công:

1. Vào chi tiết Giao dịch.
2. Nhấn nút **Xác nhận**.
3. **Trigger:** Hệ thống cập nhật `ops_status` thành `CONFIRMED`.
4. **Webhook Outbound:** Ngay sau khi `ops_status` chuyển thành `CONFIRMED`, một Job ngầm (`SendTransactionWebhooksJob`) sẽ được kích hoạt.
5. **Đẩy dữ liệu:** Hệ thống lấy tất cả Webhook URL đã cấu hình trong `PaymentPrefix` (Nguồn thu) và đẩy thông tin giao dịch sang đó.
6. **Lưu log:** Mọi lần gọi Webhook ra bên ngoài đều được ghi lại trong phần **Lịch sử gọi Webhook** để theo dõi (thành công/thất bại).

---

## 4. Tự động hết hạn (Expiration)
- Khi giao dịch được tạo, hệ thống tính toán `expires_at` dựa trên `setting_ttl` (mặc định 15 phút).
- Một Job lịch trình (`ExpireTransactionJob`) sẽ chạy để chuyển các giao dịch quá hạn sang trạng thái `FAILED` nếu chưa nhận được tiền.
