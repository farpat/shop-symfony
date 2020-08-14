FROM php:7.4-fpm

# Installation of basic packages
RUN apt-get update && apt-get install -y curl zip unzip git libzip-dev libicu-dev

# PHP Extensions
RUN docker-php-ext-install intl json zip
# PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo_pgsql
# Imagick
RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends && pecl install imagick && docker-php-ext-enable imagick

# Override with custom opcache settings
#COPY ./docker/php/config/opcache.ini $PHP_INI_DIR/conf.d/

# Installation of composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
php composer-setup.php && \
mv composer.phar /usr/local/bin/composer && \
php -r "unlink('composer-setup.php');"

ENV COMPOSER_CACHE_DIR=/.cache/composer

# Installation of wkhtmltopdf
RUN apt-get install -y xfonts-75dpi xfonts-base && \
curl -sL -o /tmp/w.deb https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.stretch_amd64.deb && \
dpkg -i /tmp/w.deb && \
rm /tmp/w.deb
