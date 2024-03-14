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

use App\Entity\Admin;
use App\Repository\AdminRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PHPUnit\Framework\MockObject\Exception;

/**
 * Class AdminRepositoryTest - Unit tests for Repository AdminRepository
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Repository
 */
class AdminRepositoryTest extends KernelTestCase
{
    /**
     * @testCase - method upgradePassword - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testUpgradePassword(): void
    {
        $newHashedPassword = '';
        $user = new Admin();
        $user->setPassword($newHashedPassword);
        $registry = $this->createMock(ManagerRegistry::class);
        $adminRepository = $this->getMockBuilder(AdminRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['save'])
            ->getMock();
        $adminRepository->expects($this->once())
            ->method('save')
            ->with($user, true);
        $adminRepository->upgradePassword($user, $newHashedPassword);
        $this->assertEquals($newHashedPassword, $user->getPassword());
    }

    /**
     * @testCase - method findOneByLogin - must be a success
     *
     * @return void
     * @throws NonUniqueResultException|Exception
     */
    public function testFindOneByLogin(): void
    {
        $value = 'test_username';
        $user = new Admin();
        $query = $this->createMock(Query::class);
        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->willReturn($user);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('u.username = :val')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('val', $value)
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $registry = $this->createMock(ManagerRegistry::class);
        $adminRepository = $this->getMockBuilder(AdminRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();
        $adminRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('u')
            ->willReturn($queryBuilder);

        $admin = $adminRepository->findOneByLogin($value);
        $this->assertInstanceOf(Admin::class, $admin);
    }
}
