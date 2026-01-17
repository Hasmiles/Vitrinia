# PHP 8.2 sürümünü kullan (Laravel 10/11 için uygun)
FROM php:8.2-cli

# Gerekli kütüphaneleri ve PostgreSQL sürücüsünü yükle
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Composer'ı yükle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Çalışma dizinini ayarla
WORKDIR /var/www

# Proje dosyalarını kopyala
COPY . .

# Bağımlılıkları yükle
RUN composer install --no-dev --optimize-autoloader

# Render'ın verdiği PORT üzerinden uygulamayı başlat
CMD php artisan serve --host=0.0.0.0 --port=$PORT