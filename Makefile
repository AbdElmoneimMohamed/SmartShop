BIN = ./vendor/bin
SAIL = $(BIN)/sail

# Docker --------------------------------------------------------------------- #
start:
	$(SAIL) up -d

stop:
	$(SAIL) stop

down:
	$(SAIL) down

rebuild:
	make down
	$(SAIL) build --no-cache
	make restart

ssh:
	docker exec -it smartshop-mini-laravel.test-1 bash

restart:
	make stop; make start

# App -------------------------------------------------------------------------#
local-setup:
	cp -n .env.example .env || true
	composer install
	make start
	$(SAIL) artisan key:generate
	$(SAIL) artisan storage:link
	make migrate
	make npm-install
	make npm-build

migrate:
	$(SAIL) artisan migrate:fresh --seed

seed:
	$(SAIL) artisan db:seed

clear:
	$(SAIL) artisan view:clear
	$(SAIL) artisan config:clear
	$(SAIL) artisan optimize:clear
	$(SAIL) artisan route:clear

# Frontend --------------------------------------------------------------------#
npm-install: ## Installs frontend dependencies
	$(SAIL) npm install

npm-build: ## Builds frontend assets for production
	$(SAIL) npm run build

npm-dev: ## Runs Vite in dev mode with hot-reload
	$(SAIL) npm run dev

# Quality gate ------------------------------------------------------------- #
test: ## Runs the full quality + test suite (Pint, PHPStan, Blade Formatter, Pest)
	$(SAIL) composer test

pint: ## Fixes code style with Pint
	$(SAIL) php $(BIN)/pint

stan: ## Runs PHPStan static analysis
	$(SAIL) php $(BIN)/phpstan analyse --xdebug

.PHONY: start stop down rebuild ssh restart local-setup migrate seed clear npm-install npm-build npm-dev test pint stan
