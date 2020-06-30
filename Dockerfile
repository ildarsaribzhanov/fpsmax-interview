FROM php:7.4-cli

RUN apt-get update \
    && apt-get install -y libmcrypt-dev mariadb-client libzip-dev wget zlib1g-dev zip libcurl4-gnutls-dev autoconf pkg-config libssl-dev --no-install-recommends \
    && docker-php-ext-install bcmath pcntl curl pdo_mysql zip sockets

WORKDIR /var/www

COPY composer.json .
COPY composer.lock .

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/bin --filename=composer \
    && rm composer-setup.php

RUN composer install --no-dev --prefer-dist --no-scripts --no-autoloader

COPY . /var/www

RUN composer dump-autoload

RUN mv .env.example .env

RUN chmod -R a+w storage

CMD ["php", "artisan", "SyncMatches"]