FROM php:7.4-fpm

# Installation of PHP Extensions
RUN apt-get update && \
apt-get install -y curl zip unzip git libicu-dev libzip-dev libjpeg62-turbo-dev libfreetype6-dev libpng-dev \
xfonts-base xfonts-75dpi fontconfig libxrender1 libonig-dev && \
docker-php-ext-install pdo pdo_mysql intl iconv json zip gd && \
docker-php-ext-configure gd --with-freetype --with-jpeg && \
docker-php-ext-install -j$(nproc) gd

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Override with custom opcache settings
#COPY ./docker/php/config/opcache.ini $PHP_INI_DIR/conf.d/

# Installation of composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
php composer-setup.php && \
mv composer.phar /usr/local/bin/composer && \
php -r "unlink('composer-setup.php');"

ENV COMPOSER_CACHE_DIR=/.cache/composer

# Installation of wkhtmltopdf
RUN curl -sL -o /tmp/w.deb https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.5/wkhtmltox_0.12.5-1.stretch_amd64.deb && \
dpkg -i /tmp/w.deb && \
rm /tmp/w.deb
