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

use App\Command\CommentCleanupCommand;
use App\Service\CommentServiceInterface;
use App\Tests\BaseCommandKernelTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Class CommentCleanupCommandTest - Unit tests for
 * CommentCleanupCommand without Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 111 - Business process - Console
 * @link https://www.atlassian.com/software/confluence/bp/111
 *
 * @package App\Tests\Unit\Command
 */
class CommentCleanupCommandTest extends BaseCommandKernelTestCase
{
    /**
     * @testCase 1097 - Unit test for method execute Deletes rejected and spam comments from the database
     * by Console without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1097
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2097 - Method execute Deletes rejected and spam comments from the database
     * by Console without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2097
     * @bp 111 - Business process - Console
     * @link https://www.atlassian.com/software/confluence/bp/111
     *     Arrange:
     * without AUTH
     *     Act:
     * Command app:comment:cleanup
     *     Assert:
     * Output StatusCode = CommandIsSuccessful
     * Output Display contains string
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testExecuteAppCommentCleanup(): void
    {
        $count = 1;
        $commentServiceMock = $this->commentServiceMock();
        $commentServiceMock->expects($this->exactly(1))
            ->method('deleteOldRejected')
            ->willReturn($count);
        $this->container->set(CommentServiceInterface::class, $commentServiceMock);

        $this->initial();

        $this->runCommand('CommentCleanupCommand', 'app:comment:cleanup');
        $this->assertCommand();
    }

    /**
     * @testCase 1097 - Integration test for method execute Deletes rejected and spam comments from the database
     * by Console without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1097
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2097 - Method execute Deletes rejected and spam comments from the database
     * by Console without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2097
     * @bp 111 - Business process - Console
     * @link https://www.atlassian.com/software/confluence/bp/111
     *     Arrange:
     * without AUTH
     *     Act:
     * Command app:comment:cleanup --dry-run
     *     Assert:
     * Output StatusCode = CommandIsSuccessful
     * Output Display contains string
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testExecuteAppCommentCleanupDryRun(): void
    {
        $count = 1;
        $commentServiceMock = $this->commentServiceMock();
        $commentServiceMock->expects($this->exactly(1))
            ->method('countOldRejected')
            ->willReturn($count);
        $this->container->set(CommentServiceInterface::class, $commentServiceMock);

        $this->initial();

        $this->runCommand('CommentCleanupCommand', 'app:comment:cleanup', ['--dry-run' => 'enabled']);
        $this->assertCommand();
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function initial(): void
    {
        $this->command = $this->container->get(CommentCleanupCommand::class);
        $this->app->add($this->command);
        $this->tester = new ApplicationTester($this->app);
    }
}
