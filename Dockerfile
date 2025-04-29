
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

WORKDIR /var/www

COPY composer.json composer.lock /var/www/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# RUN composer install --no-interaction --prefer-dist

COPY . /var/www

RUN chown -R www-data:www-data /var/www

EXPOSE 80

CMD ["php-fpm"]


# FROM php:8.2-fpm

# RUN apt-get update && apt-get install -y \
#     zip unzip curl git libzip-dev \
#     && docker-php-ext-install zip pdo pdo_mysql

# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# WORKDIR /var/www

