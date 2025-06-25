# Sử dụng image PHP chính thức
FROM php:8.2-fpm

# Cài các dependency
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy mã nguồn
WORKDIR /var/www
COPY . .

# Cài dependency Laravel
RUN composer install --no-dev --optimize-autoloader

# Gán quyền cho thư mục cache/log
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]

