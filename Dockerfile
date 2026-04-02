FROM php:8.2-apache

# PHP модули для MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Копируем все файлы сайта
COPY . /var/www/html/

WORKDIR /var/www/html

# Включаем rewrite для красивых URL
RUN a2enmod rewrite

EXPOSE 80

# Запуск Apache в форграунд
CMD ["apache2-foreground"]
