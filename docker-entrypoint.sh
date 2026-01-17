#!/bin/bash

# Kuyruk işçisini arka planda (& işaretiyle) başlat
php artisan queue:work --verbose --tries=3 --timeout=90 &

# Ana işlem olarak sunucuyu başlat (Render bunu dinler)
php artisan serve --host=0.0.0.0 --port=$PORT