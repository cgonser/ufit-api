FROM php:7.4-fpm-alpine

RUN mkdir -p /etc/nginx/conf.d
ADD ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
ADD ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
VOLUME /etc/nginx/conf.d

ADD ./docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

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

COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . /app

RUN set -eux; \
    composer install --no-dev --no-scripts ; \
    composer dump-autoload --no-dev --optimize --classmap-authoritative
    # composer install --no-ansi --no-interaction --no-autoloader --no-scripts; \
	# composer install --prefer-dist --no-dev --no-scripts --no-progress --no-suggest; \
	# composer clear-cache

RUN rm -rf /app/var/* ; \
	mkdir -p /app/var/cache /app/var/log ; \
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX /app/var ; \
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX /app/var ; \
	chmod 777 /app/var -R

VOLUME /app

CMD composer dump-autoload; \
    bin/console cache:clear; \
    bin/console assets:install; \
    bin/console doctrine:migrations:migrate --no-interaction; \
    php-fpm

