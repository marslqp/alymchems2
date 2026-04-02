# Используем PHP с Apache
FROM php:8.2-apache

# Включаем расширения для MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Копируем все файлы сайта в папку веб-сервера
COPY . /var/www/html/

# Делаем рабочую директорию
WORKDIR /var/www/html

# Включаем модуль rewrite для Apache (если нужен)
RUN a2enmod rewrite

# Указываем, что контейнер слушает 80 порт
EXPOSE 80

# Старт Apache в форграунд режиме (очень важно для Railway!)
CMD ["apache2-foreground"]
