FROM php:8.0.2-fpm-alpine

# Install tools required for build stage
RUN apk add --update --no-cache \
    bash curl wget rsync ca-certificates openssl openssh git tzdata openntpd \
    libxrender fontconfig libc6-compat \
    mysql-client gnupg binutils-gold autoconf \
    g++ gcc gnupg libgcc linux-headers make python3

RUN chmod -R 777 storage /var/www/html/storage

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install
# Install additional PHP libraries
#RUN docker-php-ext-install bcmath pdo_mysql
RUN docker-php-ext-install bcmath mysqli pdo_mysql
RUN docker-php-ext-enable pdo_mysql mysqli
#RUN docker-php-ext-configure gd --with-freetype --with-jpeg
#RUN docker-php-ext-install gd

# Install libraries for compiling GD, then build it
RUN apk add --no-cache freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev \
 && docker-php-ext-install gd \
 && apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev

# Add ZIP archives support
RUN apk add --update --no-cache zlib-dev libzip-dev \
 && docker-php-ext-install zip



WORKDIR /
