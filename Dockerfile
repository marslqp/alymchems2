# Используем официальный PHP-Apache образ
FROM php:8.2-apache

# Устанавливаем PHP модули для MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Копируем проект в рабочую директорию
COPY . /var/www/html/

WORKDIR /var/www/html

# Включаем только то, что нужно
RUN a2enmod rewrite

EXPOSE 80

# Запуск Apache
CMD ["apache2-foreground"]
