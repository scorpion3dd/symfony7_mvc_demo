#!/bin/bash

# This is a build configuration for PHP.
#
# This file is part of the Simple Web Demo Free Lottery Management Application.
#
# This project is no longer maintained.
# The project is written in Symfony Framework Release.
#
# @link https://github.com/scorpion3dd
# @author Denis Puzik <scorpion3dd@gmail.com>
# @copyright Copyright (c) 2023-2024 scorpion3dd

echo "Current directory: $(pwd)"
echo "Contents of current directory:"
ls -la

echo 'copy files:'
cp .env.dist .env
cp .env.test.dist .env.test
#cp bitbucket-pipelines.yml.dist bitbucket-pipelines.yml
cp phpcs.xml.dist phpcs.xml
cp phpmd_ruleset.xml.dist phpmd_ruleset.xml
cp phpstan.neon.dist phpstan.neon
cp phpunit.xml.dist phpunit.xml
cp psalm.xml.dist psalm.xml
cp sonar-project.properties.dist sonar-project.properties

echo 'chown folders:'
chown -R www-data:www-data ./var/cache
chown -R www-data:www-data ./var/log

echo 'chmod folders:'
chmod -R 777 ./var/cache
ls -ld ./var/cache
chmod -R 777 ./var/log
ls -ld ./var/log
chmod -R 777 ./config/jwt
ls -ld ./config/jwt