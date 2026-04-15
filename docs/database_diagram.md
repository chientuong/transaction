# Database Diagram (DBML)

Bạn có thể sao chép đoạn mã dưới đây và dán vào [dbdiagram.io](https://dbdiagram.io) để xem sơ đồ quan hệ thực thể (ERD).

```dbml
// Use dbdiagram.io to visualize this schema

Table users {
  id integer [primary key]
  name varchar
  email varchar [unique]
  password varchar
  status varchar
  last_login_at timestamp
  created_at timestamp
}

Table roles {
  id integer [primary key]
  name varchar
  guard_name varchar
}

Table model_has_roles {
  role_id integer
  model_type varchar
  model_id integer
}

Table settings {
  id integer [primary key]
  key varchar [unique]
  value text
  description varchar
  type varchar
  created_at timestamp
}

Table bank_accounts {
  id integer [primary key]
  bank_code varchar
  bank_branch varchar
  account_number varchar [unique]
  account_holder varchar
  description text
  is_active boolean
  created_by integer
  created_at timestamp
}

Table payment_prefixes {
  id integer [primary key]
  name varchar
  prefix_code varchar [unique]
  description text
  is_active boolean
  created_by integer
  created_at timestamp
}

Table transactions {
  id integer [primary key]
  transaction_code varchar [unique]
  prefix_id integer
  amount decimal
  bank_account_id integer
  transfer_content varchar
  user_id varchar
  sync_status varchar
  ops_status varchar
  expires_at timestamp
  expired_at timestamp
  confirmed_by integer
  confirmed_at timestamp
  ops_note text
  created_at timestamp
}

Table sepay_transactions {
  id integer [primary key]
  sepay_id varchar
  gateway varchar
  transaction_date timestamp
  account_number varchar
  amount_in decimal
  amount_out decimal
  code varchar
  content varchar
  raw_data json
  created_at timestamp
}

Table webhook_logs {
  id integer [primary key]
  transaction_id integer
  url varchar
  method varchar
  status_code integer
  payload json
  response_body text
  error_message text
  created_at timestamp
}

// Relationships
Ref: model_has_roles.model_id > users.id
Ref: model_has_roles.role_id > roles.id
Ref: bank_accounts.created_by > users.id
Ref: payment_prefixes.created_by > users.id
Ref: transactions.prefix_id > payment_prefixes.id
Ref: transactions.bank_account_id > bank_accounts.id
Ref: transactions.confirmed_by > users.id
Ref: webhook_logs.transaction_id > transactions.id
```

## Giải thích các mối quan hệ chính:
1.  **Transactions**: Là bảng trung tâm, kết nối với:
    - `payment_prefixes`: Để biết nguồn thu nào tạo ra giao dịch.
    - `bank_accounts`: Để biết tiền được hướng về tài khoản nào.
    - `users`: Để biết nhân viên nào đã xác nhận giao dịch (`confirmed_by`).
2.  **Webhook Logs**: Mỗi giao dịch có thể có nhiều log gửi webhook đi (One-to-Many).
3.  **SePay Transactions**: Lưu dữ liệu thô từ SePay phục vụ việc đối soát và khớp lệnh tự động vào bảng `transactions`.
4.  **RBAC (Users/Roles)**: Sử dụng cấu trúc của Spatie Permission để quản lý quyền hạn.
