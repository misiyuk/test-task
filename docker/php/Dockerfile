FROM php:8.1.1-fpm

RUN apt-get update && \
    apt-get install -y --force-yes \
        libmcrypt-dev \
        libpq-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
        wget \
        nano \
        curl

RUN docker-php-ext-install \
    bcmath \
    gd \
    soap \
    zip \
    opcache \
    pdo pdo_mysql

ADD ./composer.sh   /usr/local/bin/composer.sh
ADD ./conf/php.ini  /usr/local/etc/php/conf.d/php.ini

RUN sh /usr/local/bin/composer.sh && composer clear-cache

WORKDIR /var/www
