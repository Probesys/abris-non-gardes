.DEFAULT_GOAL := help
include docker.env

ifeq ($(shell docker --help | grep "compose"),)
	DOCKER_COMPOSE_ALIAS := docker-compose --env-file docker.env
else
	DOCKER_COMPOSE_ALIAS := docker compose --env-file docker.env
endif

.PHONY: up
up: ## Start the development environment
	$(DOCKER_COMPOSE_ALIAS) up -d
	@echo "Connectez-vous à l’adresse http://localhost:$(PORT_WEB)"

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

ip-adresses: ## [host] get ip addresses of containers
	# --- mysql ip
	docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' abris-non-gardes-mysql-1

symfony-install-core: ## Install Symfony framework core
	$(DOCKER_COMPOSE_ALIAS) exec web composer create-project symfony/skeleton:"6.3.*" temp_directory && cp -r temp_directory/* . && cp temp_directory/.env . && rm -rf temp_directory

symfony-install-webapp: ## Install Symfony webapp
	$(DOCKER_COMPOSE_ALIAS) exec web composer require webapp

.PHONY: help
help: ## Show this help
	@grep -hE '^[A-Za-z0-9_ \-]*?:.*##.*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

