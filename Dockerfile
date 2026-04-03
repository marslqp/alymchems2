FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libmysqlclient-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && apt-get clean

WORKDIR /var/www/html

COPY . .

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80"]
