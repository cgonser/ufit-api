FROM php:7.4-fpm-alpine

RUN mkdir -p /etc/nginx/conf.d
ADD ./docker/nginx/etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
VOLUME /etc/nginx/conf.d

RUN set -e; \
         apk add --no-cache \
                coreutils \
                freetype-dev \
                libintl \
                libjpeg-turbo-dev \
                libjpeg-turbo \
                libpng-dev \
                libzip-dev \
                jpeg-dev \
                git \
                icu \
                icu-dev \
                zlib-dev \
                curl-dev \
                imap-dev \
                libxslt-dev libxml2-dev \
                postgresql-dev \
                libgcrypt-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install gd pdo_pgsql intl exif zip

COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . /app

RUN set -eux; \
    composer install --no-dev --no-scripts ; \
    composer dump-autoload --no-dev --optimize --classmap-authoritative
    # composer install --no-ansi --no-interaction --no-autoloader --no-scripts; \
	# composer install --prefer-dist --no-dev --no-scripts --no-progress --no-suggest; \
	# composer clear-cache

RUN rm -rf var/cache/* ; \
    rm -rf var/log/* ; \
	mkdir -p var/cache var/log; \
	chmod -R 777 var;

VOLUME /app

CMD composer dump-autoload; \
    bin/console cache:clear; \
    bin/console assets:install; \
    php-fpm

