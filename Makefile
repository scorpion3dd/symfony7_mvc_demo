##################
# This file is part of the Simple Web Demo Free Lottery Management Application.
#
# This project is no longer maintained.
# The project is written in Symfony Framework Release.
#
# @link https://github.com/scorpion3dd
# @author Denis Puzik <scorpion3dd@gmail.com>
# @copyright Copyright (c) 2023-2024 scorpion3dd
##################


SHELL := /bin/bash

check_project:
	composer cache-clear-dev
	composer cache-clear-test
	composer check-cs
	composer check-stan
	composer check-psalm
	composer check-phpmd
	composer check-phpunit-unit-no-coverage
	composer check-phpunit-unit-admin-no-coverage
	composer check-phpunit-unit-admin-form-no-coverage
	composer check-phpunit-integration-no-coverage
	composer check-phpunit-functional-no-coverage


php_version:
	php -v

composer_version:
	composer -V

composer_info:
	composer -v

node_version:
	node -v

chmod_project_init_sh:
	${SHELL} -c "chmod +x ./project_init.sh"

ls_la_project_init_sh:
	${SHELL} -c "ls -la ./project_init.sh"

openssl_jwt_generate_private:
	openssl genrsa -out config/jwt/private.pem -aes256 4096

openssl_jwt_generate_public:
	openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

php_jwt_generate_keypair:
	php bin/console lexik:jwt:generate-keypair --overwrite

php_jwt_revoke:
	php bin/console gesdinet:jwt:revoke TOKEN

php_tail_log_enter:
	@read -p "Enter log file name: " log_file; \
    ${SHELL} -c "tail -n 100 -f /var/www/back/var/log/$$log_file"

disk_space:
	df -h

top:
	top

file_find_enter:
	@read -p "Enter log file name: " find_file; \
	${SHELL} -c "find / -type f -iname $$find_file"