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

use App\Repository\RefreshTokenRepository;
use DateTime;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PHPUnit\Framework\MockObject\Exception;

/**
 * Class RefreshTokenRepositoryTest - Unit tests for Repository RefreshTokenRepository
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Repository
 */
class RefreshTokenRepositoryTest extends KernelTestCase
{
    /**
     * @testCase - method findInvalid - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testFindInvalid(): void
    {
        $result = [];
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
            ->method('getResult')
            ->willReturn($result);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('u.valid < :datetime')
            ->willReturnSelf();
        $datetime = new DateTime();
        $datetime = $datetime->setTime(0, 0, 0);
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with(':datetime', $datetime)
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $registry = $this->createMock(ManagerRegistry::class);
        $refreshTokenRepository = $this->getMockBuilder(RefreshTokenRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();
        $refreshTokenRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('u')
            ->willReturn($queryBuilder);

        $refreshToken = $refreshTokenRepository->findInvalid();
        $this->assertIsArray($refreshToken);
        $this->assertEquals($result, $refreshToken);
    }
}
