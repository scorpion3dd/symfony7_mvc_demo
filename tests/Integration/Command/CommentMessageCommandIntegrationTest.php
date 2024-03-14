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

namespace App\Tests\Integration\Command;

use App\Command\CommentMessageCommand;
use App\Helper\ApplicationGlobals;
use App\Tests\BaseCommandKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Class CommentMessageCommandTest - Integration tests for CommentMessageCommand
 * with Auth
 * with connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 111 - Business process - Console
 * @link https://www.atlassian.com/software/confluence/bp/111
 *
 * @package App\Tests\Integration\Command
 */
class CommentMessageCommandIntegrationTest extends BaseCommandKernelTestCase
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
        $this->appGlobals->setType(ApplicationGlobals::TYPE_APP_TESTS_INTEGRATION);
    }

    /**
     * @testCase 1095 - Integration test for method execute comment message sync by Console without AUTH - must be a success
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
    public function testExecuteAppCommentMessage(): void
    {
        $this->runCommand('CommentMessageCommand', 'app:comment:message');
        $output = $this->assertCommand();
        $this->assertStringContainsString('Inputs:', $output);
        $this->assertStringContainsString('messageId =', $output);
        $this->assertStringContainsString('Process - comment message sync', $output);
        $this->assertStringContainsString('Includes:', $output);
        $this->assertStringContainsString('[NOTE] App\MessageHandler\CommentMessageHandler invoke - started', $output);
        $this->assertStringContainsString('comment-id =', $output);
        $this->assertStringContainsString('[NOTE] App\MessageHandler\CommentMessageHandler invoke - finished', $output);
    }
}
