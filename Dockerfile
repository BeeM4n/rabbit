FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
        mc \
        wget \
        curl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install sockets

WORKDIR /home/www

CMD ["php-fpm"]