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

use App\Command\CreateFakerUserCommand;
use App\Helper\ApplicationGlobals;
use App\Tests\BaseCommandKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Class CreateFakerUserCommandIntegrationTest - Integration tests for
 * CreateFakerUserCommand with Auth
 * with connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 111 - Business process - Console
 * @link https://www.atlassian.com/software/confluence/bp/111
 *
 * @package App\Tests\Integration\Command
 */
class CreateFakerUserCommandIntegrationTest extends BaseCommandKernelTestCase
{
    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->command = $this->container->get(CreateFakerUserCommand::class);
        $this->app->add($this->command);
        $this->tester = new ApplicationTester($this->app);
        $this->appGlobals->setType(ApplicationGlobals::TYPE_APP_TESTS_INTEGRATION);
    }

    /**
     * @testCase 1098 - Integration test for method execute create user async
     * by Console without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1098
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2098 - Method execute create user async
     * by Console without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2098
     * @bp 111 - Business process - Console
     * @link https://www.atlassian.com/software/confluence/bp/111
     *     Arrange:
     * without AUTH
     *     Act:
     * Command app:users:create-faker-user
     *     Assert:
     * Output StatusCode = CommandIsSuccessful
     * Output Display contains string
     *
     * @return void
     * @throws Exception
     */
    public function testExecuteAppUsersCreateFakerUser(): void
    {
        $this->runCommand('CreateFakerUserCommand', 'app:users:create-faker-user');
        $output = $this->assertCommand();
        $this->assertStringContainsString('Inputs:', $output);
        $this->assertStringContainsString('email =', $output);
        $this->assertStringContainsString('userName =', $output);
        $this->assertStringContainsString('fullName =', $output);
        $this->assertStringContainsString('statusId =', $output);
        $this->assertStringContainsString('accessId =', $output);
        $this->assertStringContainsString('Process - create user async', $output);
        $this->assertStringContainsString('Results: message "createUserCommand" write to DB messenger_messages', $output);
    }
}
