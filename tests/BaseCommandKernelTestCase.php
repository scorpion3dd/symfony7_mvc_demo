<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Symfony Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2023-2024 scorpion3dd
 */

declare(strict_types=1);

namespace App\Tests;

use App\Helper\ApplicationGlobals;
use Blackfire\Bridge\Symfony\MonitorableCommandInterface;
use Faker\Generator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Abstract base class BaseCommandKernelTestCase - for all tests
 * in Console Command
 *
 * @link https://symfony.com/doc/current/testing.html#types-of-tests
 *
 * @package App\Tests
 */
abstract class BaseCommandKernelTestCase extends KernelTestCase
{
    use TestTrait;

    /** @var Application $app */
    protected Application $app;

    /** @var ApplicationTester $tester */
    protected ApplicationTester $tester;

    /** @var MonitorableCommandInterface $command */
    protected MonitorableCommandInterface $command;

    /** @var ContainerInterface $container */
    protected ContainerInterface $container;

    /** @var Generator $faker */
    protected readonly Generator $faker;

    /** @var string $appDomain */
    protected string $appDomain;

    /** @var ApplicationGlobals $appGlobals */
    protected ApplicationGlobals $appGlobals;

    /** @var string $className */
    protected string $className;

    /** @var string $commandName */
    protected string $commandName;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $this->kernelTest = self::bootKernel();
        $this->container = static::getContainer();
        $this->faker = \Faker\Factory::create();
        $this->appDomain = $this->container->getParameter('app.domain');
        $this->appGlobals = $this->container->get(ApplicationGlobals::class);
        $this->appGlobals->setType(ApplicationGlobals::TYPE_APP_TESTS);
        $this->app = new Application();
        $this->app->setAutoExit(false);
    }

    /**
     * @param string $name
     * @param string $command
     * @param array $options
     *
     * @return void
     */
    protected function runCommand(string $name = '', string $command = '', array $options = []): void
    {
        $this->className = $name;
        $this->commandName = $command;
        $input = [
            'command' => $this->commandName,
            '--no-interaction' => '',
            '--ansi' => '',
        ];
        if (count($options) > 0) {
            foreach ($options as $key => $value) {
                $input[$key] = $value;
            }
        }
        $this->tester->run($input);
    }

    /**
     * @return string
     */
    protected function assertCommand(): string
    {
        $this->tester->assertCommandIsSuccessful();
        $output = $this->tester->getDisplay();
        $this->assertStringContainsString("{$this->className} execute", $output);
        if ((strlen($this->className) + strlen($this->commandName)) < 55) {
            $this->assertStringContainsString("[NOTE] {$this->className} {$this->commandName} - started", $output);
            $this->assertStringContainsString("[NOTE] {$this->className} {$this->commandName} - finished", $output);
        } else {
            $this->assertStringContainsString("[NOTE] {$this->className} {$this->commandName}", $output);
            $this->assertStringContainsString("started", $output);
            $this->assertStringContainsString("[NOTE] {$this->className} {$this->commandName}", $output);
            $this->assertStringContainsString("finished", $output);
        }
        $this->assertStringContainsString('[OK] ALL EXECUTED SUCCESS', $output);

        return $output;
    }
}
