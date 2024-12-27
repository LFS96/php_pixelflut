FROM php:8.4-zts-bookworm

RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y --no-install-recommends \
    libwebp-dev libpng-dev libzip-dev libicu-dev && \
    docker-php-ext-configure intl && docker-php-ext-install intl && \
    docker-php-ext-configure gd --with-webp && docker-php-ext-install gd && \
    docker-php-ext-configure sockets && docker-php-ext-install sockets && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

COPY ./docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./www /var/www/html

WORKDIR /var/www/html

# Set PHP runtime configuration
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini && \
    echo 'max_execution_time = 0' >> /usr/local/etc/php/conf.d/docker-php-maxexectime.ini


