# --------------------------------------------------------------------
# Copyright (c) 2019 LINKIT, The Netherlands. All Rights Reserved.
# Author(s): Anthony Potappel
#
# This software may be modified and distributed under the terms of the
# MIT license. See the LICENSE file for details.
# --------------------------------------------------------------------

# If you see pwd_unknown showing up, this is why. Re-calibrate your system.
PWD ?= pwd_unknown

# PROJECT_NAME defaults to name of the current directory.
# should not to be changed if you follow GitOps operating procedures.
PROJECT_NAME=ufit

# Note. If you change this, you also need to update docker-compose.yml.
# only useful in a setting with multiple services/ makefiles.
SERVICE_TARGET := php-fpm

POSTGRES_USER=ufit
POSTGRES_DB=ufit
POSTGRES_PASSWORD=ufit

# aws configs
AWS_PROFILE=ufit
AWS_REPOSITORY_URI=
AWS_REGION=us-east-1
AWS_ECS_CLUSTER=prod
AWS_ECS_SERVICE=site

DOCKER_IMAGE_NAME=ufit-api

THIS_FILE := $(lastword $(MAKEFILE_LIST))
CMD_ARGUMENTS ?= $(cmd)

# export such that its passed to shell functions for Docker to pick up.
export PROJECT_NAME

# all our targets are phony (no files to check).
.PHONY: shell help build rebuild service start stop login test test-docker clean prune symfony aws-push aws-deploy build-push-deploy sql

# suppress makes own output
#.SILENT:

# shell is the first target. So instead of: make shell cmd="whoami", we can type: make cmd="whoami".
# more examples: make shell cmd="whoami && env", make shell cmd="echo hello container space".
# leave the double quotes to prevent commands overflowing in makefile (things like && would break)
# special chars: '',"",|,&&,||,*,^,[], should all work. Except "$" and "`", if someone knows how, please let me know!).
# escaping (\) does work on most chars, except double quotes (if someone knows how, please let me know)
# i.e. works on most cases. For everything else perhaps more useful to upload a script and execute that.
shell:
ifeq ($(CMD_ARGUMENTS),)
	# no command is given, default to shell
	docker-compose -p $(PROJECT_NAME) exec $(SERVICE_TARGET) bash
else
	# run the command
	docker-compose -p $(PROJECT_NAME) exec $(SERVICE_TARGET) bash -c "$(CMD_ARGUMENTS)"
endif

# Regular Makefile part for buildpypi itself
help:
	@echo ''
	@echo 'Usage: make [TARGET] [EXTRA_ARGUMENTS]'
	@echo 'Targets:'
	@echo '  build		build docker --image--'
	@echo '  rebuild	rebuild docker --image--'
	@echo '  test		run tests on the local environment'
	@echo '  test		run tests inside of the docker container
	@echo '  service	run as service --container--'
	@echo '  start		start services'
	@echo '  stop		stop services'
	@echo '  login		run as service and login --container--'
	@echo '  clean		remove docker --image--'
	@echo '  shell		run docker --container--'
	@echo '  symfony	run symfony command'
	@echo '  sql		open psql terminal'
	@echo '  aws-push	push image to ecr'
	@echo '  aws-deploy	deploy image to ecs'
	@echo '  build-push-deploy'
	@echo ''
	@echo 'Extra arguments:'
	@echo 'cmd=:	make cmd="whoami"'

rebuild:
	# force a rebuild by passing --no-cache
	docker-compose build --no-cache $(SERVICE_TARGET)

service:
	# run as a (background) service
	docker-compose -p $(PROJECT_NAME) up -d $(SERVICE_TARGET)

start:
	# start services in background
	docker-compose -p $(PROJECT_NAME) up -d

stop:
	# stop services
	docker-compose -p $(PROJECT_NAME) down

login: service
	# run as a service and attach to it
	docker exec -it $(PROJECT_NAME) sh

build:
	# only build the container. Note, docker does this also if you apply other targets.
	docker-compose build --no-cache $(SERVICE_TARGET)

clean:
	# remove created images
	@docker-compose -p $(PROJECT_NAME) down --remove-orphans --rmi all 2>/dev/null \
	&& echo 'Image(s) for "$(PROJECT_NAME)" removed.' \
	|| echo 'Image(s) for "$(PROJECT_NAME)" already removed.'

sql:
	@docker-compose -p $(PROJECT_NAME) exec db sh -c "psql ${POSTGRES_USER} ${POSTGRES_DB}"

db-fixtures-load:
	docker-compose -p $(PROJECT_NAME) run -v $(PWD)/data/sql/fixtures:/fixtures --rm db \
	sh -c 'for f in /fixtures/*; do PGPASSWORD=${POSTGRES_PASSWORD} psql -h db ${POSTGRES_DB} ${POSTGRES_USER} -f $$f; done'

db-recreate:
	docker-compose -p $(PROJECT_NAME) stop db
	docker-compose -p $(PROJECT_NAME) rm -f db
	docker volume rm $(PROJECT_NAME)_db-data
	docker-compose -p $(PROJECT_NAME) up -d db
	docker-compose -p $(PROJECT_NAME) run --rm $(SERVICE_TARGET) sh -c "/var/www/html/symfony doctrine:insert-sql"

test:
	php bin/console --env=test doctrine:database:drop --force -q
	php bin/console --env=test doctrine:database:create -q
	php bin/console --env=test doctrine:schema:create -q
	php bin/console --env=test doctrine:fixtures:load -n
	bin/fakes3 -db var/cache/test/s3.db -port localhost:7003 > /dev/null 2>&1 &
	php ./vendor/bin/phpunit
	kill $!

test-docker:
	docker-compose -p $(PROJECT_NAME) exec $(SERVICE_TARGET) php bin/console --env=test doctrine:database:drop --force -q
	docker-compose -p $(PROJECT_NAME) exec $(SERVICE_TARGET) php bin/console --env=test doctrine:database:create -q
	docker-compose -p $(PROJECT_NAME) exec $(SERVICE_TARGET) php bin/console --env=test doctrine:schema:create -q
	docker-compose -p $(PROJECT_NAME) exec $(SERVICE_TARGET) php ./vendor/bin/phpunit


aws-push:
	# ecr login + tag image + push image
	$$(aws --profile=${AWS_PROFILE} ecr get-login --region ${AWS_REGION} | sed 's/ -e none//g')
	docker tag ${DOCKER_IMAGE_NAME}\:latest ${AWS_REPOSITORY_URI}\:latest
	docker push ${AWS_REPOSITORY_URI}\:latest

aws-deploy:
	aws --profile=${AWS_PROFILE} ecs update-service --cluster=${AWS_ECS_CLUSTER} --service=${AWS_ECS_SERVICE} --force-new-deployment

build-push-deploy: build aws-push aws-deploy
