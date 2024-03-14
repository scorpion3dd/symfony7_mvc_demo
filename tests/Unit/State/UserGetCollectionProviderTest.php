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

namespace App\Tests\Unit\State;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use App\Service\UserServiceInterface;
use App\State\UserGetCollectionProvider;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserGetCollectionProviderTest - Unit tests for State UserGetCollectionProvider
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\State
 */
class UserGetCollectionProviderTest extends BaseKernelTestCase
{
    /** @var UserServiceInterface $userServiceMock */
    private UserServiceInterface $userServiceMock;

    /** @var LoggerInterface $loggerMock */
    private LoggerInterface $loggerMock;

    /** @var UserGetCollectionProvider $provider */
    private UserGetCollectionProvider $provider;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userServiceMock = $this->createMock(UserServiceInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->provider = new UserGetCollectionProvider(
            $this->userServiceMock,
            $this->loggerMock
        );
    }

    /**
     * @testCase - method provide - must be a success, GetCollection operation
     *
     * @return void
     * @throws Exception
     */
    public function testProvide(): void
    {
        $users = [];
        $user = $this->createUser();
        $users[] = $user;

        $this->userServiceMock->expects($this->once())
            ->method('getLotteryUsers')
            ->willReturn($users);

        $response = $this->provider->provide(new GetCollection());
        $this->assertIsArray($response);
        $this->assertEquals($users, $response);
    }

    /**
     * @testCase - method provide - must be a success, NotFoundHttpException
     *
     * @return void
     * @throws Exception
     */
    public function testProvideNotFoundHttpException(): void
    {
        $operationMock = $this->createMock(Operation::class);
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Operation not Get.');
        $this->provider->provide($operationMock);
    }
}
