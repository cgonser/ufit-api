FROM php:7.4-fpm-alpine

RUN mkdir -p /etc/nginx/conf.d
ADD ./docker/nginx/etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
VOLUME /etc/nginx/conf.d

RUN apk --update --no-cache add git postgresql-dev libintl icu icu-dev
RUN docker-php-ext-install pdo_pgsql intl
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

CMD composer dump-autoload; bin/console cache:clear ; php-fpm

