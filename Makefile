CWD = $(shell pwd)

.EXPORT_ALL_VARIABLES:

default: test

phptest:
	@docker run -it --rm -v $(CWD):/home/circleci/source cimg/php:$(VERSION) \
		bash -c 'set -ex; cp -R ~/source/. ./; composer --quiet install; \
		composer -n phpcs' > results-$(VERSION).txt

phpunittest:
	@docker run -it --rm -v $(CWD):/home/circleci/source cimg/php:$(VERSION) \
		bash -c 'set -ex; cp -R ~/source/. ./; composer --quiet install; \
		composer -n phpunit' > results-unit-$(VERSION).txt

.PHONY: php74

php74:
	$(MAKE) phptest VERSION=7.4

.PHONY: unitphp74

unitphp74:
	$(MAKE) phpunittest VERSION=7.4

.PHONY: php80

php80:
	$(MAKE) phptest VERSION=8.0

.PHONY: unitphp80

unitphp80:
	$(MAKE) phpunittest VERSION=8.0

.PHONY: php81

php81:
	$(MAKE) phptest VERSION=8.1

.PHONY: unitphp81

unit-php81:
	$(MAKE) phpunittest VERSION=8.1

.PHONY: test

test:
	$(MAKE) -j 2 php74 php80

.PHONY: unit

unit:
	$(MAKE) -j 2 unitphp74 unitphp80

.PHONY: test-experimental

test-experimental:
	$(MAKE) -j 3 php74 php80 php81