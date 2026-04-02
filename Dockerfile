# Используем PHP 8.2 с Apache
FROM php:8.2-apache

# Включаем расширения для MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Копируем все файлы сайта (index.php, login.php, setup_db.php, db.php, JS, CSS)
COPY . /var/www/html/

# Делаем рабочую директорию
WORKDIR /var/www/html

# Включаем rewrite, если нужен для красивых URL
RUN a2enmod rewrite

# Слушаем 80 порт
EXPOSE 80

# Запуск Apache в форграунд
CMD ["apache2-foreground"]
