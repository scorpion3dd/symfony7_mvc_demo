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

namespace App\Tests\Unit\Service;

use App\CQRS\Bus\CommandBus;
use App\CQRS\Bus\CommandBusInterface;
use App\CQRS\Bus\QueryBus;
use App\CQRS\Bus\QueryBusInterface;
use App\CQRS\Command\CreateUser\CreateUserCommand;
use App\CQRS\Query\FindUserByEmail\FindUserByEmailQuery;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryInterface;
use App\Service\UserService;
use App\Tests\Unit\BaseKernelTestCase;
use App\Tests\Unit\Interface\SlidingPaginationInterfaceMock;
use DateTime;
use Doctrine\ORM\AbstractQuery;
use Exception;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;

/**
 * Class UserServiceTest - Unit tests for service UserService
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Service
 */
class UserServiceTest extends BaseKernelTestCase
{
    /** @var UserService $userService */
    private UserService $userService;

    /** @var LoggerInterface|null $logger */
    private ?LoggerInterface $logger;

    /** @var UserRepositoryInterface|null $repository */
    private ?UserRepositoryInterface $repository;

    /** @var PaginatorInterface|null $paginator */
    private ?PaginatorInterface $paginator;

    /** @var CommandBusInterface|null $commandBus */
    private ?CommandBusInterface $commandBus;

    /** @var QueryBusInterface|null $queryBus */
    private ?QueryBusInterface $queryBus;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->paginator = $this->container->get(PaginatorInterface::class);
        $this->commandBus = $this->container->get(CommandBusInterface::class);
        $this->queryBus = $this->container->get(QueryBusInterface::class);
        $this->repository = $this->container->get(UserRepositoryInterface::class);
        $this->logger = $this->container->get(LoggerInterface::class);
        $this->userService = new UserService(
            $this->paginator,
            $this->commandBus,
            $this->queryBus,
            $this->repository,
            $this->logger
        );
    }

    /**
     * @testCase - method createFakerUser - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testCreateFakerUser(): void
    {
        $date = new DateTime();
        $message = new CreateUserCommand('', '', '', '', 1, 1, 1, $date, $date, $date);
        $envelope = Envelope::wrap($message);
        $busMock = $this->getMockBuilder(CommandBus::class)
            ->onlyMethods(['execute'])
            ->disableOriginalConstructor()
            ->getMock();
        $busMock->expects($this->exactly(1))
            ->method('execute')
            ->willReturn($envelope);
        $this->userService->setCommandBus($busMock);

        $command = $this->userService->createFakerUser();
        $this->assertInstanceOf(CreateUserCommand::class, $command);
    }

    /**
     * @testCase - method findUserByEmailQuery - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testFindUserByEmailQuery(): void
    {
        $email = $this->faker->email();
        $message = new FindUserByEmailQuery($email);
        $envelope = Envelope::wrap($message);
        $busMock = $this->getMockBuilder(QueryBus::class)
            ->onlyMethods(['execute'])
            ->disableOriginalConstructor()
            ->getMock();
        $busMock->expects($this->exactly(1))
            ->method('execute')
            ->willReturn($envelope);
        $this->userService->setQueryBus($busMock);

        $this->userService->findUserByEmailQuery($email);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method getUsersLottery - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetUsersLottery(): void
    {
        $page = 1;
        $usersLottery = [];
        $user = $this->createUser();
        $usersLottery[] = $user;
        $queryMock = $this->createMock(AbstractQuery::class);
        $queryMock->expects($this->exactly(1))
            ->method('getResult')
            ->willReturn($usersLottery);

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->onlyMethods(['findUsersLottery'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findUsersLottery')
            ->willReturn($queryMock);
        $this->userService->setRepository($repositoryMock);

        $usersLotteryNew = $this->userService->getUsersLottery($page);
        $this->assertIsArray($usersLotteryNew);
        $this->assertEquals(1, count($usersLotteryNew));
        $this->assertInstanceOf(User::class, $usersLotteryNew[0]);
    }

    /**
     * @testCase - method getUsersPaginator - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetUsersPaginator(): void
    {
        $page = 2;
        $template = 'pagination/sliding.html.twig';

        $queryMock = $this->createMock(AbstractQuery::class);

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->onlyMethods(['findUsersAccess'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findUsersAccess')
            ->willReturn($queryMock);
        $this->userService->setRepository($repositoryMock);

        $slidingPaginationMock = $this->createMock(SlidingPaginationInterfaceMock::class);
        $slidingPaginationMock->expects($this->exactly(1))
            ->method('setTemplate');
        $slidingPaginationMock->expects($this->exactly(1))
            ->method('setPageRange');

        $paginatorMock = $this->createMock(PaginatorInterface::class);
        $paginatorMock->expects($this->exactly(1))
            ->method('paginate')
            ->willReturn($slidingPaginationMock);

        $this->userService->setPaginator($paginatorMock);

        $pagination = $this->userService->getUsersPaginator($page, $template);
        $this->assertInstanceOf(SlidingPaginationInterface::class, $pagination);
    }

    /**
     * @testCase - method getLotteryUsers - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetLotteryUsers(): void
    {
        $usersLottery = [];
        $user = $this->createUser();
        $usersLottery[] = $user;
        $queryMock = $this->createMock(AbstractQuery::class);
        $queryMock->expects($this->exactly(1))
            ->method('getResult')
            ->willReturn($usersLottery);

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->onlyMethods(['findUsersAccess'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findUsersAccess')
            ->willReturn($queryMock);
        $this->userService->setRepository($repositoryMock);

        $usersLotteryNew = $this->userService->getLotteryUsers();
        $this->assertIsArray($usersLotteryNew);
        $this->assertEquals(1, count($usersLotteryNew));
        $this->assertInstanceOf(User::class, $usersLotteryNew[0]);
    }

    /**
     * @testCase - method findOneByField - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testFindOneByField(): void
    {
        $key = 'email';
        $value = $this->faker->email();
        $result = $this->createUser();

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo([$key => $value]))
            ->willReturn($result);
        $this->userService->setRepository($repositoryMock);

        $user = $this->userService->findOneByField($key, $value);
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @testCase - method findByField - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testFindByField(): void
    {
        $key = 'email';
        $value = $this->faker->email();
        $users = [];
        $user = $this->createUser();
        $users[] = $user;

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->onlyMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with($this->equalTo([$key => $value]))
            ->willReturn($users);
        $this->userService->setRepository($repositoryMock);

        $usersNew = $this->userService->findByField($key, $value);
        $this->assertIsArray($usersNew);
        $this->assertEquals(1, count($usersNew));
        $this->assertInstanceOf(User::class, $usersNew[0]);
    }

    /**
     * @testCase - method save - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testSave(): void
    {
        $user = $this->createUser();
        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->onlyMethods(['save'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('save');
        $this->userService->setRepository($repositoryMock);

        $this->userService->save($user);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method remove - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRemove(): void
    {
        $user = $this->createUser();
        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->onlyMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('remove');
        $this->userService->setRepository($repositoryMock);

        $this->userService->remove($user);
        $this->assertTrue(true);
    }
}
