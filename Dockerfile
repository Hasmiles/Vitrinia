# PHP 8.2 sürümünü kullan
FROM php:8.2-cli

# Gerekli kütüphaneler
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Çalışma dizini
WORKDIR /var/www

# Proje dosyalarını kopyala
COPY . .

# Bağımlılıkları yükle
RUN composer install --no-dev --optimize-autoloader

# --- YENİ EKLENEN KISIM ---
# Script dosyasını kopyala ve çalıştırma izni ver (+x)
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# CMD kısmını değiştiriyoruz, artık scripti çağıracak
CMD ["docker-entrypoint.sh"]