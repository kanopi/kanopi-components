CWD = $(shell pwd)

.EXPORT_ALL_VARIABLES:

default: complete

phpcs:
	@docker run -it --rm -v $(CWD):/home/circleci/source cimg/php:$(VERSION) \
		bash -c 'set -ex; cp -R ~/source/. ./; composer --quiet install; \
		composer -n phpcs -- --standard="./.phpcs-$(VERSION).xml.dist" ./'

phpunit:
	@docker run -it --rm -v $(CWD):/home/circleci/source cimg/php:$(VERSION) \
		bash -c 'set -ex; cp -R ~/source/. ./; composer --quiet install; \
		composer -n phpunit'

phpcsreport:
	@docker run -it --rm -v $(CWD):/home/circleci/source cimg/php:$(VERSION) \
		bash -c 'set -ex; cp -R ~/source/. ./; composer --quiet install; \
		composer -n phpcs -- --standard="./.phpcs-$(VERSION).xml.dist" ./' > phpcs-$(VERSION).txt ~/source/

phpunitreport:
	@docker run -it --rm -v $(CWD):/home/circleci/source cimg/php:$(VERSION) \
		bash -c 'set -ex; cp -R ~/source/. ./; composer --quiet install; \
		composer -n phpunit' > phpunit-$(VERSION).txt

.PHONY: php80

php80:
	$(MAKE) phpcsreport VERSION=8.0

.PHONY: unitphp80

unitphp80:
	$(MAKE) phpunitreport VERSION=8.0

.PHONY: php81

php81:
	$(MAKE) phpcsreport VERSION=8.1

.PHONY: unitphp81

unitphp81:
	$(MAKE) phpunitreport VERSION=8.1

.PHONY: php82

php82:
	$(MAKE) phpcsreport VERSION=8.2

.PHONY: unitphp82

unitphp82:
	$(MAKE) phpunitreport VERSION=8.2

.PHONY: test

test:
	$(MAKE) -j 3 php80 php81 php82

.PHONY: unit

unit:
	$(MAKE) -j 3 unitphp80 unitphp81 unitphp82

complete:
	$(MAKE) -j 2 unit test

.PHONY: complete
