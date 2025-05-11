FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    && docker-php-ext-install pdo pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=true" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html
RUN cd /var/www/html
COPY .env.example .env


RUN composer install

RUN chmod -R 777 /var/www/html/storage

COPY entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
