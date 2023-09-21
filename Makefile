.DEFAULT_GOAL := help

ifeq ($(shell docker --help | grep "compose"),)
	DOCKER_COMPOSE_ALIAS := docker-compose --env-file docker.env
else
	DOCKER_COMPOSE_ALIAS := docker compose --env-file docker.env
endif

up: ## Start the development environment
	$(DOCKER_COMPOSE_ALIAS) up -d

up-build: ## Start the development environment by rebuilding the Docker images
	$(DOCKER_COMPOSE_ALIAS) up -d --build

down: ## Shutdown the Docker containers
	$(DOCKER_COMPOSE_ALIAS) down

down-v: ## Shutdown the Docker containers AND delete the volumes (including the database)
	$(DOCKER_COMPOSE_ALIAS) down -v

bash: ## Open a bash on web with current user
	$(DOCKER_COMPOSE_ALIAS) exec -u ${MY_UID}:${MY_GID} -it web /bin/bash

bash-root: ## open a bash on web with root user
	$(DOCKER_COMPOSE_ALIAS) exec -u root -it web /bin/bash

migration: ## Install/update database
	$(DOCKER_COMPOSE_ALIAS) exec web bin/console doctrine:migration:migrate --no-interaction

messenger-consume: ## Consume messenger messages
	$(DOCKER_COMPOSE_ALIAS) exec web  php bin/console messenger:consume async

symfony-install-core: ## Install Symfony framework core
	$(DOCKER_COMPOSE_ALIAS) exec web composer create-project symfony/skeleton:"6.3.*" temp_directory && cp -r temp_directory/* . && cp temp_directory/.env . && rm -rf temp_directory

symfony-install-webapp: ## Install Symfony webapp
	$(DOCKER_COMPOSE_ALIAS) exec web composer require webapp

.PHONY: help
help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

