FROM php:7.4-fpm

# Installation of basic packages
RUN apt-get update && apt-get install -y curl zip unzip git libzip-dev libicu-dev

# PHP Extensions
RUN docker-php-ext-install intl json zip
# PostgreSQL
RUN apt-get install -y libpq-dev && docker-php-ext-install pdo_pgsql
# Imagick
RUN apt-get install -y libmagickwand-dev --no-install-recommends && pecl install imagick && docker-php-ext-enable imagick

# Override with custom opcache settings
#COPY ./docker/php/config/opcache.ini $PHP_INI_DIR/conf.d/

# Installation of composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

ENV COMPOSER_CACHE_DIR=/.cache/composer

# Installation of wkhtmltopdf
RUN apt-get install -y xfonts-75dpi xfonts-base && \
curl -sL -o /tmp/w.deb https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.5/wkhtmltox_0.12.5-1.stretch_amd64.deb && \
dpkg -i /tmp/w.deb && \
rm /tmp/w.deb
