CWD := $(shell pwd)
VERSIONS := 8.0 8.1 8.2

.PHONY: complete test unit $(addprefix php, $(VERSIONS)) $(addprefix unitphp, $(VERSIONS))

complete: unit test

define run_docker_command
	@docker run -it --rm -v ./:/home/circleci/source cimg/php:$(1) \
		bash -c 'set -e; cp -R ~/source/. ./; composer --quiet install; $(2)'
endef

phpcs:
	$(call run_docker_command,$(VERSION),composer -n phpcs -- --standard="./.phpcs-$(VERSION).xml.dist" ./)

phpunit:
	$(call run_docker_command,$(VERSION),composer -n phpunit)

phpcsreport:
	$(call run_docker_command,$(VERSION),composer -n phpcs -- --standard="./.phpcs-$(VERSION).xml.dist" ./ | tee ~/source/phpcs-$(VERSION).txt)

phpunitreport:
	$(call run_docker_command,$(VERSION),composer -n phpunit | tee ~/source/phpunit-$(VERSION).txt)

$(addprefix php, $(VERSIONS)): php%:
	$(MAKE) phpcsreport VERSION=$*

$(addprefix unitphp, $(VERSIONS)): unitphp%:
	$(MAKE) phpunitreport VERSION=$*

test: $(addprefix php, $(VERSIONS))

unit: $(addprefix unitphp, $(VERSIONS))
