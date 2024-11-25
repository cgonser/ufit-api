#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

## Enable xdebug if ENABLE_XDEBUG env is defined
if [ ! -f ${PHP_INI_DIR}/conf.d/xdebug.ini ] && [ "${ENABLE_XDEBUG}" != "" ]; then
    logger "Enabling xdebug"

    mv ${PHP_INI_DIR}/conf.d/xdebug.ini.disabled ${PHP_INI_DIR}/conf.d/xdebug.ini
fi

if [ "${NEW_RELIC_LICENSE_KEY}" != "" ] && [ "${NEW_RELIC_APP_NAME}" != "" ]; then
    NEW_RELIC_DAEMON_ADDRESS=`ip route | grep default | cut -d ' ' -f 3`

    sed -i \
        -e "s/newrelic.license =.*/newrelic.license = ${NEW_RELIC_LICENSE_KEY}/" \
        -e "s/newrelic.appname =.*/newrelic.appname = ${NEW_RELIC_APP_NAME}/" \
        -e "s/newrelic.daemon.address =.*/newrelic.daemon.address = ${NEW_RELIC_DAEMON_ADDRESS}:31339/" \
        /usr/local/etc/php/conf.d/newrelic.ini
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-production"
	if [ "$APP_ENV" != 'prod' ]; then
		PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-development"
	fi
	ln -sf "$PHP_INI_RECOMMENDED" "$PHP_INI_DIR/php.ini"

	mkdir -p var/cache var/log
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

	if [ "$APP_ENV" != 'prod' ]; then
		composer install --prefer-dist --no-progress --no-interaction
	fi

	if grep -q DATABASE_URL= .env; then
		echo "Waiting for database to be ready..."
		ATTEMPTS_LEFT_TO_REACH_DATABASE=60
		until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(php bin/console doctrine:query:sql -q "SELECT 1" 2>&1); do
			if [ $? -eq 255 ]; then
				# If the Doctrine command exits with 255, an unrecoverable error occurred
				ATTEMPTS_LEFT_TO_REACH_DATABASE=0
				break
			fi
			sleep 1
			ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
			echo "Still waiting for database to be ready... Or maybe the database is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left."
		done

		if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
			echo "The database is not up or not reachable:"
			echo "$DATABASE_ERROR"
			exit 1
		else
			echo "The database is now ready and reachable"
		fi

		if ls -A migrations/*.php >/dev/null 2>&1; then
			php bin/console doctrine:migrations:migrate --no-interaction
		fi
	fi
fi

exec docker-php-entrypoint "$@"
