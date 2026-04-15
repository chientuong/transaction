FROM php:8.4-cli

# Cài đặt các gói hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    supervisor

# Cài đặt Node.js (cần cho Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Cài đặt các extension PHP cần thiết cho Laravel (bao gồm MySQL và Redis)
RUN mkdir -p /usr/src/php/ext/redis \
    && curl -fsSL https://github.com/phpredis/phpredis/archive/6.1.0.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip-components=1 \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql mbstring exif pcntl bcmath gd intl zip redis

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

RUN mkdir -p /var/log/supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Lưu ý: Toàn bộ mã nguồn sẽ được mount vào container thông qua volumes trong docker-compose.yml
