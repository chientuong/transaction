# Tài liệu Hướng dẫn API

Tất cả các API yêu cầu xác thực và trả về dữ liệu định dạng JSON.

## 1. Xác thực (Authentication)
Sử dụng header `Authorization` với Token hệ thống (lấy từ trang Cài đặt hệ thống).

- **Header:** `Authorization: [TOKEN]` hoặc `Authorization: Bearer [TOKEN]`
- **Accept:** `application/json`

---

## 2. API Hệ thống (Nội bộ)

### Lấy danh sách Ngân hàng hoạt động
- **Endpoint:** `GET /api/bank-accounts/active`
- **Mô tả:** Trả về danh sách tài khoản ngân hàng đang ở trạng thái hoạt động.

### Lấy danh sách Nguồn thu hoạt động
- **Endpoint:** `GET /api/payment-prefixes/active`
- **Mô tả:** Trả về danh sách các prefix ( SHOPEE, v.v.) đang hoạt động.

### Tạo Giao dịch mới
- **Endpoint:** `POST /api/transactions`
- **Payload:**
```json
{
  "prefix_id": 1,
  "bank_account_id": 1,
  "amount": 100000,
  "user_id": "optional_id",
  "sync_status": "PENDING"
}
```
- **Phản hồi:** Trả về thông tin giao dịch kèm mã `transaction_code`, `transfer_content` và link `qr_code`.

---

## 3. Webhook SePay (Tích hợp SePay.vn)

Hệ thống hỗ trợ tiếp nhận Webhook từ SePay để tự động khớp lệnh.

- **Endpoint:** `POST /api/sepay-webhook`
- **Xác thực:** Header `Authorization` khớp với `api_key_sepay` trong cài đặt.
- **Payload mẫu:**
```json
{
  "id": "WS123",
  "gateway": "TPBank",
  "accountNumber": "04030603101",
  "transferAmount": 100000,
  "transferType": "in",
  "content": "Nội dung bao gồm mã SHOPEE_XXXXXX"
}
```

---

## 4. Mã trạng thái (Status Codes)
- `200/201`: Thành công.
- `401`: Không có quyền truy cập (Token sai hoặc thiếu).
- `422`: Dữ liệu gửi lên không hợp lệ (Validation Error).
- `500`: Lỗi hệ thống.
