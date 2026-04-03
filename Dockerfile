FROM php:8.2-apache

# Устанавливаем mysqli
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Отключаем лишние MPM модули, оставляем только prefork
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite

# Копируем файлы проекта
COPY . /var/www/html/

# Права доступа
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
