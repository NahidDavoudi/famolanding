FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
      libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev libzip-dev libicu-dev \
 && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
 && docker-php-ext-install -j"$(nproc)" gd pdo_mysql mysqli zip intl opcache bcmath \
 && a2enmod rewrite headers remoteip \
 && rm -rf /var/lib/apt/lists/*

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/custom.ini "$PHP_INI_DIR/conf.d/zz-custom.ini"
COPY docker/remoteip.conf /etc/apache2/conf-available/remoteip.conf
COPY docker/apache-tune.conf /etc/apache2/conf-available/apache-tune.conf
RUN a2enconf remoteip apache-tune

# اگه document root پروژه‌ت public/ ـه (مثلاً MVC)، این خط رو فعال کن:
# ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
# RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY ./src/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html
