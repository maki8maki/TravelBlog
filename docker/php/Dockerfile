FROM php:8.0-apache

# rootディレクトリの変更
ENV APACHE_DOCUMENT_ROOT=/workdir/src
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN apt-get update && \
    apt-get install -y libonig-dev && \
    apt-get install -y git vim && \
    docker-php-ext-install pdo_mysql mysqli mbstring

WORKDIR /workdir

EXPOSE 80