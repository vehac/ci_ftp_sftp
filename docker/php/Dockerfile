FROM php:7.1-apache

RUN apt-get update && apt-get install -y git zlib1g-dev libicu-dev g++ libxml2-dev libmcrypt-dev libreadline-dev curl libcurl4-gnutls-dev pkg-config zlibc libssl-dev \
 default-mysql-client openssl build-essential vim nano zip unzip wget

# install mongodb ext
RUN pecl install mongodb-1.7.4 && docker-php-ext-enable mongodb

# install ext php
RUN docker-php-ext-configure intl && docker-php-ext-install intl \
 && docker-php-ext-install zip pdo pdo_mysql mysqli mbstring xml \
 && docker-php-ext-install soap && docker-php-ext-install mcrypt \
 && docker-php-ext-install mbstring && docker-php-ext-install curl \
 && docker-php-ext-install bcmath && docker-php-ext-install sockets \
 && apt-get install -y libfreetype6-dev libpng-dev libjpeg-dev \
 && docker-php-ext-configure gd \
    --with-freetype-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ \
    --with-png-dir=/usr/include/ \
 && docker-php-ext-install -j$(nproc) gd \
 && apt-get purge -y libfreetype6-dev libpng-dev libjpeg-dev

#active a2enmod and install composer
RUN a2enmod rewrite && a2enmod ssl && a2enmod headers \
 && curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

# install ext memcached and clean the image
RUN apt-get update \
 && apt-get install -y libfreetype6-dev libmemcached-dev \
 && curl -L -o /tmp/memcached.tar.gz "https://github.com/php-memcached-dev/php-memcached/archive/php7.tar.gz" \
 && mkdir -p /usr/src/php/ext/memcached \
 && tar -C /usr/src/php/ext/memcached -zxvf /tmp/memcached.tar.gz --strip 1 \
 && docker-php-ext-configure memcached \
 && docker-php-ext-install memcached \
 && rm /tmp/memcached.tar.gz \
 && rm -rf /var/lib/apt/lists/* /tmp/*

ADD ./localhost.conf /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80

WORKDIR /var/www/html