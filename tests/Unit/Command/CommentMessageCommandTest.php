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

use App\Command\CommentMessageCommand;
use App\Tests\BaseCommandKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Class CommentMessageCommandTest - Unit tests for CommentMessageCommand without Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 111 - Business process - Console
 * @link https://www.atlassian.com/software/confluence/bp/111
 *
 * @package App\Tests\Unit\Command
 */
class CommentMessageCommandTest extends BaseCommandKernelTestCase
{
    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->command = $this->container->get(CommentMessageCommand::class);
        $this->app->add($this->command);
        $this->tester = new ApplicationTester($this->app);
    }

    /**
     * @testCase 1095 - Unit test for method execute comment message sync by Console without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1095
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2095 - Method execute comment message sync by Console without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2095
     * @bp 111 - Business process - Console
     * @link https://www.atlassian.com/software/confluence/bp/111
     *     Arrange:
     * without AUTH
     *     Act:
     * Command app:comment:message
     *     Assert:
     * Output StatusCode = CommandIsSuccessful
     * Output Display contains string
     *
     * @return void
     * @throws Exception
     */
    public function testExecute(): void
    {
        $this->runCommand('CommentMessageCommand', 'app:comment:message');
        $output = $this->assertCommand();
        $this->assertStringContainsString('Inputs:', $output);
        $this->assertStringContainsString('messageId =', $output);
        $this->assertStringContainsString('Process - comment message sync', $output);
    }
}
