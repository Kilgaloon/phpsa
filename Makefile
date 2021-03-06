
cs:
	./vendor/bin/phpcs --report=emacs

check-src:
	./bin/phpsa check -vvv ./src

# Rubric, hacking themselves (c) @Kistamushken
check-fixtures:
	./bin/phpsa check -vvv ./fixtures

# For local dev
dev:
	./bin/phpsa check -vvv ./sandbox

tests-local:
	php -d xdebug.profiler_enable=1 ./vendor/bin/phpunit -v

# Alias for tests-local
tests: tests-local

tests-ci:
	./vendor/bin/phpunit -v --debug --coverage-clover=coverage.clover

# For renewing config and documentation when analyzers were changed
analyzers:
	./bin/phpsa config:dump-reference > .phpsa.yml
	./bin/phpsa config:dump-documentation > docs/05_Analyzers.md

test: tests-local cs

ci: cs tests-ci check-fixtures check-src
