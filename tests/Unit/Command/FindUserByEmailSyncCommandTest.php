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

use App\Command\FindUserByEmailSyncCommand;
use App\Factory\UserFactory;
use App\Service\UserServiceInterface;
use App\Tests\BaseCommandKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Class FindUserByEmailSyncCommandTest - Unit tests for
 * FindUserByEmailSyncCommand without Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 111 - Business process - Console
 * @link https://www.atlassian.com/software/confluence/bp/111
 *
 * @package App\Tests\Unit\Command
 */
class FindUserByEmailSyncCommandTest extends BaseCommandKernelTestCase
{
    /** @var UserFactory $userFactory */
    protected UserFactory $userFactory;

    /**
     * @testCase 1100 - Unit test for method execute find user by email sync
     * by Console without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1100
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2100 - Method execute find user by email sync
     * by Console without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2100
     * @bp 111 - Business process - Console
     * @link https://www.atlassian.com/software/confluence/bp/111
     *     Arrange:
     * without AUTH
     *     Act:
     * Command app:users:find-user-by-email-sync
     *     Assert:
     * Output StatusCode = CommandIsSuccessful
     * Output Display contains string
     *
     * @dataProvider provideUser
     *
     * @param string $version
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testExecuteAppUsersFindUserByEmailSync(string $version): void
    {
        $this->userFactory = $this->container->get(UserFactory::class);
        $user = null;
        if ($version == '1') {
            $user = $this->createUser();
        }
        $userServiceMock = $this->userServiceMock();
        $userServiceMock->expects($this->exactly(1))
            ->method('findOneByField')
            ->willReturn($user);
        $this->container->set(UserServiceInterface::class, $userServiceMock);

        $this->command = $this->container->get(FindUserByEmailSyncCommand::class);
        $this->app->add($this->command);
        $this->tester = new ApplicationTester($this->app);

        $this->runCommand('FindUserByEmailSyncCommand', 'app:users:find-user-by-email-sync');
        $output = $this->assertCommand();
        $this->assertStringContainsString('Inputs:', $output);
        $this->assertStringContainsString('email =', $output);
        $this->assertStringContainsString('Process - find user by email sync', $output);
    }

    /**
     * @return iterable
     */
    public static function provideUser(): iterable
    {
        $version = '1';
        yield $version => [$version];

        $version = '2';
        yield $version => [$version];
    }
}
