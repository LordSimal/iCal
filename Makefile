-include Makefile.local

export XDEBUG_MODE=coverage
export PHP_CS_FIXER_IGNORE_ENV=1

MAKEFLAGS += --warn-undefined-variables
SHELL := bash
PATH := $(CURDIR)/vendor/bin:$(PATH)
PHPUNIT_FLAGS ?=

.PHONY: help
help:
	@echo 'Available targets'
	@echo '  clean               Removes temporary build artifacts like'
	@echo '  website             Builds the documentation website'
	@echo '  fix                 Fixes composer.json and code style'
	@echo '  test                Execute all tests'
	@echo '  vendor              Installs composer vendor'

.PHONY: test
test: test-validate-composer test-code-style test-phpunit test-examples test-composer-normalize

.PHONY: test-code-style
test-code-style: vendor
	php-cs-fixer fix --dry-run --diff

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
fix: fix-code-style fix-composer

.PHONY: fix-code-style
fix-code-style: vendor
fix-code-style:
	php-cs-fixer -- fix

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
	rm -rf vendor node_modules .phpunit.result.cache .php-cs-fixer.cache build
	cd website && $(MAKE) clean
