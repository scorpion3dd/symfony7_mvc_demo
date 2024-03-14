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

use App\Entity\User;
use App\Repository\CommentRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CommentRepositoryTest - Unit tests for Repository CommentRepository
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Repository
 */
class CommentRepositoryTest extends KernelTestCase
{
    /**
     * @testCase - method countOldRejected - must be a success
     *
     * @return void
     * @throws NoResultException
     * @throws NonUniqueResultException|Exception
     */
    public function testCountOldRejected(): void
    {
        $result = 1;

        $query = $this->createMock(Query::class);
        $query->expects($this->once())
            ->method('getSingleScalarResult')
            ->willReturn($result);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->exactly(1))
            ->method('select')
            ->willReturnCallback(fn($key) => match ([$key]) {
                ['c'] => $queryBuilder,
                ['COUNT(c.id)'] => $queryBuilder,
            });
        $queryBuilder->expects($this->exactly(2))
            ->method('andWhere')
            ->willReturnCallback(fn($key) => match ([$key]) {
                ['c.state = :state_rejected or c.state = :state_spam'] => $queryBuilder,
                ['c.createdAt < :date'] => $queryBuilder,
            });
        $mockDateTime = new DateTimeImmutable('-' . CommentRepository::DAYS_BEFORE_REJECTED_REMOVAL . ' days');
        $mockDateTime = $mockDateTime->setTime(0, 0, 0);
        $parametersArray = [
            'state_rejected' => 'rejected',
            'state_spam' => 'spam',
            'date' => $mockDateTime
        ];
        $parameters = new ArrayCollection($parametersArray);
        $queryBuilder->expects($this->once())
            ->method('setParameters')
            ->with($parameters)
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $registry = $this->createMock(ManagerRegistry::class);
        $commentRepository = $this->getMockBuilder(CommentRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();
        $commentRepository->expects($this->exactly(1))
            ->method('createQueryBuilder')
            ->with('c')
            ->willReturn($queryBuilder);

        $count = $commentRepository->countOldRejected();
        $this->assertIsInt($count);
        $this->assertEquals($result, $count);
    }

    /**
     * @testCase - method deleteOldRejected - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testDeleteOldRejected(): void
    {
        $result = 1;
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
            ->method('execute')
            ->willReturn($result);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->exactly(2))
            ->method('andWhere')
            ->willReturnCallback(fn($key) => match ([$key]) {
                ['c.state = :state_rejected or c.state = :state_spam'] => $queryBuilder,
                ['c.createdAt < :date'] => $queryBuilder,
            });
        $mockDateTime = new DateTimeImmutable('-' . CommentRepository::DAYS_BEFORE_REJECTED_REMOVAL . ' days');
        $mockDateTime = $mockDateTime->setTime(0, 0, 0);
        $parametersArray = [
            'state_rejected' => 'rejected',
            'state_spam' => 'spam',
            'date' => $mockDateTime
        ];
        $parameters = new ArrayCollection($parametersArray);
        $queryBuilder->expects($this->once())
            ->method('setParameters')
            ->with($parameters)
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('delete')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $registry = $this->createMock(ManagerRegistry::class);
        $commentRepository = $this->getMockBuilder(CommentRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();
        $commentRepository->expects($this->exactly(1))
            ->method('createQueryBuilder')
            ->with('c')
            ->willReturn($queryBuilder);

        $count = $commentRepository->deleteOldRejected();
        $this->assertIsInt($count);
        $this->assertEquals($result, $count);
    }

    /**
     * @testCase - method getComment - must be a success
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testGetComment(): void
    {
        $user = new User();
        $user->setId(1);
        $offset = 1;
        $perPage = 5;
        $state = 'published';

        $query = $this->createMock(Query::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->exactly(2))
            ->method('andWhere')
            ->willReturnCallback(fn($key) => match ([$key]) {
                ['c.user = :user'] => $queryBuilder,
                ['c.state = :state'] => $queryBuilder,
            });
        $queryBuilder->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturnCallback(fn($key, $value) => match ([$key, $value]) {
                ['user', $user] => $queryBuilder,
                ['state', $state] => $queryBuilder,
            });
        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with('c.createdAt', 'DESC')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with($perPage)
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('setFirstResult')
            ->with($offset)
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $registry = $this->createMock(ManagerRegistry::class);
        $commentRepository = $this->getMockBuilder(CommentRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();
        $commentRepository->expects($this->exactly(1))
            ->method('createQueryBuilder')
            ->with('c')
            ->willReturn($queryBuilder);

        $result = $commentRepository->getComment($user, $offset, $perPage);
        $this->assertInstanceOf(Query::class, $result);
    }
}
