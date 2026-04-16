#!/bin/bash

# Script thiết lập dự án tự động (Dành cho máy Host có cài Docker)

echo "--- 🚀 Bắt đầu cài đặt dự án ---"

# 1. Kiểm tra xem có đang chạy trong Docker container không
if [ -f /.dockerenv ]; then
    IN_DOCKER=true
    PHP_CMD="php"
else
    IN_DOCKER=false
    PHP_CMD="docker exec laravel_app php"
fi

# 2. Khởi chạy Docker (Nếu đang ở máy Host)
if [ "$IN_DOCKER" = false ]; then
    echo "--- 1. Khởi chạy Docker containers ---"
    docker compose -f docker/docker-compose.yml up -d
    echo "Đợi 10 giây để cơ sở dữ liệu sẵn sàng..."
    sleep 10
fi

# 3. Chạy Migration và Master Data
echo "--- 2. Khởi tạo cơ sở dữ liệu & Master Data ---"
$PHP_CMD artisan migrate --force
$PHP_CMD artisan db:seed --class=RoleAndPermissionSeeder --force
$PHP_CMD artisan db:seed --class=SettingSeeder --force

# 4. Tạo tài khoản Admin
echo "--- 3. Khởi tạo tài khoản Admin (admin@example.com / password) ---"
$PHP_CMD create_admin.php

echo "--- ✅ Cài đặt hoàn tất! ---"
echo "Bạn có thể đăng nhập tại: http://localhost:9999/admin"
echo "Tài khoản: admin@example.com / password"
