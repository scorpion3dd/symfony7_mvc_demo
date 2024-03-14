# PHPUnit 11.0.5

## UNIT TESTS

### COVERAGE

Running all PHP Unit Tests with coverage in HTML-format:
```bash
composer check-phpunit-unit-coverage-html
```
or
```bash
php bin/phpunit --testsuite=Unit --colors=always  --coverage-html ./var/check/tests/unit
```

Running all PHP Unit Tests (--testsuite=Unit) for a specific directory (--filter 'Util') 
with no coverage:
```bash
composer check-phpunit-filter-dir
```
or
```bash
php bin/phpunit --testsuite=Unit --filter 'Util' --colors=always --no-coverage --testdox
```

Running all PHP Unit Tests (--testsuite=Unit) for a specific directory (--filter 'Util') 
with coverage in HTML-format:
```bash
composer check-phpunit-filter-dir
```
or
```bash
php bin/phpunit --testsuite=Unit --filter 'Util' --colors=always --testdox --coverage-html ./var/check/tests/unit
```

This project has a complete PHP Unit tests with `full coverage code` for all methods in:

- CQRS +;
- Command +;
- Controller +;
- DataFixtures +;
- Document +;
- Entity +;
- EntityListener +;
- EventSubscriber +;
- Factory ?;
- Form +;
- Helper +;
- Message ?;
- MessageHandler +;
- Notification +;
- Repository +
- Security +;
- Service +;
- State +;
- Util +;
- Validator +;


### PHPUNIT.XML

<?xml version="1.0" encoding="UTF-8"?>
<!--
  ~ This file is part of the Simple Web Demo Free Lottery Management Application.
  ~
  ~ This project is no longer maintained.
  ~ The project is written in Symfony Framework Release.
  ~
  ~ @link https://github.com/scorpion3dd
  ~ @author Denis Puzik <scorpion3dd@gmail.com>
  ~ @copyright Copyright (c) 2023-2024 scorpion3dd
  -->

<!-- https://phpunit.readthedocs.io/en/latest/configuration.html -->
<!-- For PHPUnit 11.0.* -->
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
bootstrap="tests/bootstrap.php"
stopOnError="false"
stopOnFailure="false"
stopOnIncomplete="false"
stopOnSkipped="false"
colors="true"
stderr="true"
backupGlobals="true"
>
    <php>
        <ini name="memory_limit" value="-1"/>
        <ini name="display_errors" value="1"/>
        <ini name="error_reporting" value="-1"/>
        <ini name="xdebug.mode" value="coverage"/>
        <env name="XDEBUG_MODE" value="coverage"/>
        <env name="SYMFONY_DEPRECATIONS_HELPER" value="weak"/>
        <env name="KERNEL_CLASS" value="App\Kernel"/>
        <env name="DOMAIN" value="http://symfony-mvc.learn.vms"/>
        <server name="APP_ENV" value="test" force="true"/>
        <server name="SHELL_VERBOSITY" value="-1"/>
        <server name="SYMFONY_PHPUNIT_REMOVE" value=""/>
        <server name="SYMFONY_PHPUNIT_VERSION" value="11.0"/>
    </php>

    <testsuites>
        <testsuite name="Unit">
            <directory>./tests/Unit</directory>
        </testsuite>
        <testsuite name="UnitAdmin">
            <directory>./tests/UnitAdmin</directory>
        </testsuite>
        <testsuite name="UnitAdminForm">
            <directory>./tests/UnitAdminForm</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>./tests/Integration</directory>
        </testsuite>
        <testsuite name="Functional">
            <directory>./tests/Functional</directory>
        </testsuite>
    </testsuites>

    <coverage includeUncoveredFiles="true">
        <report>
            <clover outputFile="clover.xml"/>
            <cobertura outputFile="cobertura.xml"/>
            <crap4j outputFile="crap4j.xml" threshold="50"/>
            <html outputDirectory="./var/check/tests/unit" lowUpperBound="50" highLowerBound="90"/>
            <php outputFile="coverage.php"/>
            <text outputFile="coverage.txt" showUncoveredFiles="false" showOnlySummary="true"/>
            <xml outputDirectory="xml-coverage"/>
        </report>
    </coverage>

    <extensions>
        <bootstrap class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension" />
    </extensions>

    <!--    <listeners>-->
    <!--        <listener class="Symfony\Bridge\PhpUnit\SymfonyTestsListener" />-->
    <!--    </listeners>-->

    <!-- Run `composer require symfony/panther` before enabling this extension -->
    <!--
    <extensions>
        <extension class="Symfony\Component\Panther\ServerExtension" />
    </extensions>
    -->
</phpunit>
