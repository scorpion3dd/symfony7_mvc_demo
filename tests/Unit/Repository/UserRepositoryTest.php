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

namespace App\Tests\Unit\Repository;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class UserRepositoryTest - Unit tests for Repository UserRepository
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Repository
 */
class UserRepositoryTest extends KernelTestCase
{
    /**
     * @testCase - method findUsersAccess - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testFindUsersAccess(): void
    {
        $access = 1;
        $status = 1;
        $state = 'published';

        $query = $this->createMock(Query::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('select')
            ->with(
                'u.id',
                'u.uid',
                'u.email',
                'u.fullName',
                'u.dateBirthday',
                'u.gender',
                'u.slug',
                'count(c.user) as commentsCount'
            )
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('from')
            ->with(User::class, 'u')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('leftJoin')
            ->with(Comment::class, 'c', 'WITH', 'c.user = u.id and c.state = :state')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('u.access = :access')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('u.status = :status')
            ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
            ->method('setParameter')
            ->willReturnCallback(fn($key, $value) => match([$key, $value]) {
                ['access', $access] => $queryBuilder,
                ['status', $status] => $queryBuilder,
                ['state', $state] => $queryBuilder,
            });
        $queryBuilder->expects($this->once())
            ->method('groupBy')
            ->with('u.id')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $registry = $this->createMock(ManagerRegistry::class);
        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['getEntityManager'])
            ->getMock();
        $userRepository->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $result = $userRepository->findUsersAccess();
        $this->assertInstanceOf(Query::class, $result);
    }

    /**
     * @testCase - method findUsersLottery - must be a success
     *
     * @return void
     * @throws QueryException
     */
    public function testFindUsersLottery(): void
    {
        $access = 1;
        $status = 1;
        $state = 'published';

        $query = $this->createMock(Query::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('select')
            ->with(
                'u.id',
                'u.uid',
                'u.email',
                'u.fullName',
                'u.dateBirthday',
                'u.gender',
                'u.slug',
                'count(c.user) as commentsCount'
            )
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('from')
            ->with(User::class, 'u')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('leftJoin')
            ->with(Comment::class, 'c', 'WITH', 'c.user = u.id and c.state = :state')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('u.access = :access')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('u.status = :status')
            ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
            ->method('setParameter')
            ->willReturnCallback(fn($key, $value) => match([$key, $value]) {
                ['access', $access] => $queryBuilder,
                ['status', $status] => $queryBuilder,
                ['state', $state] => $queryBuilder,
            });
        $queryBuilder->expects($this->once())
            ->method('groupBy')
            ->with('u.id')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('addCriteria')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $registry = $this->createMock(ManagerRegistry::class);
        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['getEntityManager'])
            ->getMock();
        $userRepository->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $result = $userRepository->findUsersLottery(1, 5);
        $this->assertInstanceOf(Query::class, $result);
    }

    /**
     * @testCase - method findUsersAccessed - must be a success
     *
     * @return void
     */
    public function testFindUsersAccessed(): void
    {
        $result = [
            [
                'countCreatedAt' => 15,
                'countUpdatedAt' => 14,
                'yearAt' => 2020,
            ],
            [
                'countCreatedAt' => 18,
                'countUpdatedAt' => 16,
                'yearAt' => 2021,
            ]
        ];
        $access = 1;
        $status = 1;

        $query = $this->createMock(Query::class);
        $query->expects($this->once())
            ->method('getResult')
            ->willReturn($result);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('select')
            ->with(
                'count(u.createdAt) as countCreatedAt',
                'count(u.updatedAt) as countUpdatedAt',
                'YEAR(u.createdAt) as yearAt'
            )
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('from')
            ->with(User::class, 'u')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('u.createdAt IS NOT NULL')
            ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
            ->method('andWhere')
            ->willReturnCallback(fn($key) => match([$key]) {
                ['u.updatedAt IS NOT NULL'] => $queryBuilder,
                ['u.access = :access'] => $queryBuilder,
                ['u.status = :status'] => $queryBuilder,
            });
        $queryBuilder->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturnCallback(fn($key, $value) => match([$key, $value]) {
                ['access', $access] => $queryBuilder,
                ['status', $status] => $queryBuilder,
            });
        $queryBuilder->expects($this->once())
            ->method('groupBy')
            ->with('yearAt')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with('yearAt')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $configuration = $this->createMock(Configuration::class);
        $configuration->expects($this->once())
            ->method('addCustomDatetimeFunction')
            ->with('YEAR', 'DoctrineExtensions\Query\Mysql\Year');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($configuration);
        $entityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $registry = $this->createMock(ManagerRegistry::class);
        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['getEntityManager'])
            ->getMock();
        $userRepository->expects($this->exactly(2))
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $users = $userRepository->findUsersAccessed();

        $this->assertIsArray($users);
        $this->assertEquals($result, $users);
    }

    /**
     * @testCase - method save - must be a success
     *
     * @return void
     */
    public function testSave(): void
    {
        $user = new User();

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('persist');
        $entityManager->expects($this->once())
            ->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['getEntityManager'])
            ->getMock();
        $userRepository->expects($this->exactly(2))
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $userRepository->save($user, true);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method remove - must be a success
     *
     * @return void
     */
    public function testRemove(): void
    {
        $user = new User();

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('remove');
        $entityManager->expects($this->once())
            ->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['getEntityManager'])
            ->getMock();
        $userRepository->expects($this->exactly(2))
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $userRepository->remove($user, true);
        $this->assertTrue(true);
    }
}
