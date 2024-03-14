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

use App\Command\StepInfoCommand;
use App\Tests\BaseCommandKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Class StepInfoCommandTest - Unit tests for
 * StepInfoCommand with Auth
 * with connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 111 - Business process - Console
 * @link https://www.atlassian.com/software/confluence/bp/111
 *
 * @package App\Tests\Unit\Command
 */
class StepInfoCommandTest extends BaseCommandKernelTestCase
{
    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->command = $this->container->get(StepInfoCommand::class);
        $this->app->add($this->command);
        $this->tester = new ApplicationTester($this->app);
    }

    /**
     * @testCase 1101 - Unit test for method execute step info
     * by Console without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1101
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2101 - Method execute step info
     * by Console without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2101
     * @bp 111 - Business process - Console
     * @link https://www.atlassian.com/software/confluence/bp/111
     *     Arrange:
     * without AUTH
     *     Act:
     * Command app:step:info
     *     Assert:
     * Output StatusCode = CommandIsSuccessful
     * Output Display contains string
     *
     * @return void
     * @throws Exception
     */
    public function testExecuteAppStepInfo(): void
    {
        $this->runCommand('StepInfoCommand', 'app:step:info');
        $this->assertCommand();
    }
}
