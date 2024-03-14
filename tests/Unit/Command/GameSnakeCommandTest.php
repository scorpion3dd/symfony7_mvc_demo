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

namespace App\Tests\Unit\Command;

use App\Command\GameSnakeCommand;
use App\Tests\BaseCommandKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Class GameSnakeCommandTest - Unit tests for GameSnakeCommand
 * with Auth
 * with connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 111 - Business process - Console
 * @link https://www.atlassian.com/software/confluence/bp/111
 *
 * @package App\Tests\Unit\Command
 */
class GameSnakeCommandTest extends BaseCommandKernelTestCase
{
    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->command = $this->container->get(GameSnakeCommand::class);
        $this->app->add($this->command);
        $this->tester = new ApplicationTester($this->app);
    }

    /**
     * @testCase 1096 - Unit test for method execute game snake by Console without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1096
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2096 - Method execute game snake by Console without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2096
     * @bp 111 - Business process - Console
     * @link https://www.atlassian.com/software/confluence/bp/111
     *     Arrange:
     * without AUTH
     *     Act:
     * Command app:game:snake
     *     Assert:
     * Output StatusCode = CommandIsSuccessful
     * Output Display contains string
     *
     * @return void
     * @throws Exception
     */
    public function testExecuteAppGameSnake(): void
    {
        $this->runCommand('GameSnakeCommand', 'app:game:snake');
        $output = $this->assertCommand();
        $this->assertStringContainsString('Process - game snake', $output);
        $this->assertStringContainsString('Includes:', $output);
        $this->assertStringContainsString('Score:', $output);
        $this->assertStringContainsString('Game Over. Your score:', $output);
    }
}
