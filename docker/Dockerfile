FROM php:8.3-zts-bookworm

RUN apt-get update && apt-get upgrade -y &&  \
    apt-get install -y libzip-dev libpng-dev libonig-dev libxml2-dev libicu-dev curl zip unzip libwebp-dev  && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl && \
    docker-php-ext-configure iconv && \
    docker-php-ext-install iconv && \
    docker-php-ext-configure pdo_mysql && \
    docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure gd --with-webp \
    && docker-php-ext-install gd

RUN docker-php-ext-configure sockets
RUN docker-php-ext-install sockets

RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;
RUN echo 'max_execution_time = 0' >> /usr/local/etc/php/conf.d/docker-php-maxexectime.ini;
COPY ./docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./www /var/www/html

workdir /var/www/html

