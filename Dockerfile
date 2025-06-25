FROM php:8.2-fpm

# Cài các extension PHP
RUN apt-get update && apt-get install -y \
    zip unzip curl git libzip-dev libpng-dev libonig-dev libxml2-dev \
    libssl-dev ca-certificates \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath

# Cài composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy toàn bộ source
COPY . .

# Cài dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Quyền cho storage & cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Mở port cho Render scan
EXPOSE 10000

# Lệnh chạy server Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]
