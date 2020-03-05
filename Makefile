.PHONY: install update clean help test dusk dev stop-dev build migrate bash
.DEFAULT_GOAL   = help

include .env

PRIMARY_COLOR   		= \033[0;34m
PRIMARY_COLOR_BOLD   	= \033[1;34m

SUCCESS_COLOR   		= \033[0;32m
SUCCESS_COLOR_BOLD   	= \033[1;32m

DANGER_COLOR    		= \033[0;31m
DANGER_COLOR_BOLD    	= \033[1;31m

WARNING_COLOR   		= \033[0;33m
WARNING_COLOR_BOLD   	= \033[1;33m

NO_COLOR      			= \033[m

# For test
filter      ?= tests
dir         ?=

php := docker-compose run --rm php_dev php
php_dusk := docker-compose run --rm php_dusk php
bash := docker-compose run --rm php_dev bash
composer := docker-compose run --rm php_dev composer
mariadb := docker-compose exec mariadb mysql -uroot -proot -e
npm := npm

node_modules: package.json
	@$(npm) install

vendor: composer.json
	@$(composer) install

install: vendor node_modules ## Install the composer dependencies and npm dependencies

update: ## Update the composer dependencies and npm dependencies
	@$(composer) update
	@$(npm) run update
	@$(npm) install
	@$(php) artisan app:build-translations

clean: ## Remove composer dependencies (vendor folder) and npm dependencies (node_modules folder)
	@echo "$(DANGER_COLOR_BOLD) Deleting composer and npm files/directories$(NO_COLOR)"
	rm -rf vendor node_modules package-lock.json composer.lock

help: ## Display this help
	@awk 'BEGIN {FS = ":.*##"; } /^[a-zA-Z_-]+:.*?##/ { printf "$(PRIMARY_COLOR_BOLD)%-10s$(NO_COLOR) %s\n", $$1, $$2 }' $(MAKEFILE_LIST) | sort

test: dev ## Run unit tests (parameters : dir=tests/Feature/LoginTest.php || filter=get)
	@echo "Creating database: $(PRIMARY_COLOR_BOLD)$(APP_NAME)_test$(NO_COLOR) ..."
	@$(mariadb) "drop database if exists $(APP_NAME)_test; create database $(APP_NAME)_test;"
	@$(php) vendor/bin/phpunit $(dir) --filter $(filter) --stop-on-failure

dusk: install ## Run dusk tests (parameters : build=1 to build assets before run dusk tests)
ifdef build
	@echo "$(PRIMARY_COLOR)Building assets...$(NO_COLOR)"
	make build
endif
	@docker-compose up -d nginx_dusk chrome
	@$(mariadb) "drop database if exists $(APP_NAME)_test; create database $(APP_NAME)_test;"
#	@$(php) artisan dusk
	@docker-compose stop nginx_dusk php_dusk chrome
	@echo "End of browser tests"

dev: install ## Run development servers
	@docker-compose up -d nginx_dev webpack_dev_server #laravel_echo_server
	@echo "Dev server launched on http://localhost:$(APP_PORT)"
	@echo "Mail server launched on http://localhost:1080"
	@echo "Webpack dev server launched on http://localhost:3000"

stop-dev: ## Stop development servers
	@docker-compose down
	@echo "Dev server stopped: http://localhost:$(APP_PORT)"
	@echo "Mail server stopped: http://localhost:1080"
	@echo "Webpack dev server stopped: http://localhost:3000"

build: install ## Build assets projects for production
	@rm -rf ./public/assets/*
	@$(npm) run build
#	@$(php) artisan app:build-translations

migrate: install ## Refresh database by running new migrations
#	@$(php) artisan migrate:fresh --seed

bash: install ## Run bash in PHP container
	@$(bash)
