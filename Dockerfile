FROM php:8.2-fpm

# نصب ابزارهای مورد نیاز برای Laravel + SQLite
RUN apt-get update && apt-get install -y \
    build-essential \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath zip

# نصب Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تنظیم مسیر کاری داخل کانتینر
WORKDIR /var/www

# کپی کردن سورس پروژه داخل کانتینر
COPY . .

# نصب وابستگی‌های پروژه با Composer
RUN composer install

# تنظیم دسترسی‌ها
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# اجرای PHP-FPM
CMD ["php-fpm"]
