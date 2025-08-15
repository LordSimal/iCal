-include Makefile.local

export XDEBUG_MODE=coverage

MAKEFLAGS += --warn-undefined-variables
SHELL := bash
PATH := $(CURDIR)/vendor/bin:$(PATH)
PHPUNIT_FLAGS ?=

.PHONY: help
help:
	@echo 'Available targets'
	@echo '  clean               Removes temporary build artifacts like'
	@echo '  website             Builds the documentation website'
	@echo '  test                Execute all tests'
	@echo '  vendor              Installs composer vendor'

.PHONY: test
test: test-validate-composer test-phpunit test-examples test-composer-normalize

.PHONY: test-phpunit
test-phpunit: vendor
	phpunit ${PHPUNIT_FLAGS}

.PHONY: test-examples
EXAMPLE_FILES := $(wildcard examples/*.php)
test-examples: $(EXAMPLE_FILES)

examples/example*.php: vendor
	php $@ > /dev/null

.PHONY: test-validate-composer
test-validate-composer:
	composer validate

.PHONY: test-composer-normalize
test-composer-normalize: vendor
test-composer-normalize:
	composer normalize --dry-run --diff

vendor: composer.json composer.lock
	composer install --no-interaction

.PHONY: fix
fix: fix-composer

.PHONY: fix-composer
fix-composer: vendor
fix-composer:
	composer normalize --no-update-lock
	composer update nothing

node_modules: yarn.lock package.json
	yarn

.PHONY: website
website:
	cd website && $(MAKE) build

.PHONY: clean
clean:
	rm -rf vendor node_modules .phpunit.result.cache build
	cd website && $(MAKE) clean
