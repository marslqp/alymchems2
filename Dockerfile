# Используем официальный PHP с Apache
FROM php:8.2-apache

# Включаем расширение для MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Копируем все файлы в папку веб-сервера
COPY . /var/www/html/

# Делаем /var/www/html доступной
WORKDIR /var/www/html

# Разрешаем mod_rewrite (нужно для URL, если используешь)
RUN a2enmod rewrite
