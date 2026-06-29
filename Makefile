.PHONY: install update up upd stop down clear ps bash cc migrate fresh

DOCKER_CMD=CURRENT_UID=1000 CURRENT_GID=1000 docker compose

RUN_CMD=$(DOCKER_CMD) run -u application --rm --no-deps na-api

install:
	$(RUN_CMD) composer install

update:
	$(RUN_CMD) composer update

up:
	@$(DOCKER_CMD) up na-mysql na-api na-phpmyadmin

upd:
	@$(DOCKER_CMD) up -d na-mysql na-api na-phpmyadmin na-scheduler

down:
	@$(DOCKER_CMD) down

clear:
	@$(DOCKER_CMD) down -v --remove-orphans

ps:
	@$(DOCKER_CMD) ps

bash: upd
	@$(DOCKER_CMD) exec na-api bash

cc: upd
	@$(DOCKER_CMD) exec -u application na-api php artisan optimize:clear

migrate:
	$(RUN_CMD) php artisan migrate --seed --ansi

migrate-fresh:
	$(RUN_CMD) php artisan migrate:fresh --seed --ansi

seed:
	$(RUN_CMD) php artisan db:seed --ansi

pint:
	$(RUN_CMD) vendor/bin/pint
