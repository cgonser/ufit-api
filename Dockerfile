FROM php:7.4-fpm-alpine

RUN mkdir -p /etc/nginx/conf.d
ADD ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
ADD ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
VOLUME /etc/nginx/conf.d

ADD ./docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf
ADD ./docker/php-fpm/xdebug.ini.disabled /usr/local/etc/php/conf.d/xdebug.ini.disabled

RUN set -e; \
         apk add --no-cache \
                acl \
                coreutils \
                curl-dev \
                freetype-dev \
                git \
                icu \
                icu-dev \
                imap-dev \
                jpeg-dev \
                libgcrypt-dev \
                libintl \
                libjpeg-turbo-dev \
                libjpeg-turbo \
                libpng-dev \
                libxslt-dev libxml2-dev \
                libzip-dev \
                postgresql-dev \
                zlib-dev

RUN docker-php-ext-install sockets \
    && apk add --no-cache --update rabbitmq-c-dev \
    && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
    && for i in $(seq 1 3); do echo yes | pecl install -o "xdebug" && s=0 && break || s=$? && sleep 1; done; (exit $s) \
    && pecl install -o -f amqp \
    && docker-php-ext-enable amqp

ARG MPDECIMAL_VERSION=2.5.1

RUN set -eux; \
	cd /tmp/; \
		curl -sSL -O https://www.bytereef.org/software/mpdecimal/releases/mpdecimal-${MPDECIMAL_VERSION}.tar.gz; \
		tar -xzf mpdecimal-${MPDECIMAL_VERSION}.tar.gz; \
			cd mpdecimal-${MPDECIMAL_VERSION}; \
			./configure; \
			make; \
			make install

RUN pecl install decimal \
    && docker-php-ext-enable decimal \
    && apk del .phpize-deps

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd pdo_pgsql intl exif zip

WORKDIR /app
ARG APP_ENV=prod

COPY composer.json composer.lock symfony.lock ./
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN set -eux; \
    composer install --no-dev --no-scripts ; \
    composer clear-cache

COPY .env.dist ./.env
COPY assets assets/
COPY bin bin/
COPY config config/
COPY docker docker/
COPY migrations migrations/
COPY public public/
COPY src src/
COPY templates templates/
COPY translations translations/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	# composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; \
    bin/console cache:clear; \
    bin/console assets:install; \
	sync

VOLUME /app

COPY docker/php-fpm/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint
ENTRYPOINT ["docker-entrypoint"]

CMD ["php-fpm"]
