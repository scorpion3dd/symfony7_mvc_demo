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

namespace App\Tests\Unit\CQRS\Command\CreateUser;

use App\CQRS\Command\CreateUser\CreateUserCommand;
use App\Factory\UserFactory;
use App\Helper\ApplicationGlobals;
use App\CQRS\Command\CreateUser\CreateUserCommandHandler;
use App\Service\UserServiceInterface;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CreateUserCommandHandlerTest - Unit tests for State CreateUserCommandHandler
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\CQRS\Command\CreateUser
 */
class CreateUserCommandHandlerTest extends BaseKernelTestCase
{
    /** @var CreateUserCommandHandler $handler */
    private CreateUserCommandHandler $handler;

    /** @var UserServiceInterface $userServiceMock */
    private UserServiceInterface $userServiceMock;

    /** @var UserFactory $userFactoryMock */
    private UserFactory $userFactoryMock;

    /** @var LoggerInterface $loggerMock */
    private LoggerInterface $loggerMock;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|\PHPUnit\Framework\MockObject\Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->appGlobals->setType(ApplicationGlobals::TYPE_APP_TESTS_HIDE);
        $this->userServiceMock = $this->createMock(UserServiceInterface::class);
        $this->userFactoryMock = $this->createMock(UserFactory::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->handler = new CreateUserCommandHandler(
            $this->userServiceMock,
            $this->userFactoryMock,
            $this->appGlobals,
            $this->loggerMock
        );
    }

    /**
     * @testCase - method invoke - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $user = $this->createUser();
        $user->setId(1);
        $createUserCommand = new CreateUserCommand(
            $user->getEmail(),
            $user->getUsername(),
            $user->getFullName(),
            $user->getDescription(),
            $user->getGender(),
            $user->getStatus(),
            $user->getAccess(),
            $user->getDateBirthday(),
            $user->getCreatedAt(),
            $user->getUpdatedAt(),
        );

        $this->userFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($user);

        $this->userServiceMock->expects($this->once())
            ->method('save')
            ->with($user, true);

        $this->handler->__invoke($createUserCommand);
        $this->assertTrue(method_exists($this->handler, 'debugFunction'));
    }
}
