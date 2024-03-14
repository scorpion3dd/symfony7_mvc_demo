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

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\RolePermission;
use App\Entity\UserRole;
use App\Repository\RolePermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class RolePermissionRepositoryTest - Unit tests for Repository RolePermissionRepository
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Repository
 */
class RolePermissionRepositoryTest extends KernelTestCase
{
    /**
     * @testCase - method findRolePermissionsBy - must be a success
     *
     * @return void
     */
    public function testFindRolePermissionsBy(): void
    {
        $userId = 1;

        $query = $this->createMock(Query::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('select')
            ->with(
                'rp.id',
                'r.name as nameRole',
                'p.name as namePermission'
            )
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('from')
            ->with(RolePermission::class, 'rp')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('join')
            ->with(UserRole::class, 'ur', 'WITH', 'rp.id = ur.rolePermissionId')
            ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
            ->method('leftJoin')
            ->willReturnCallback(fn($class, $alias, $w, $condition) => match ([$class, $alias, $w, $condition]) {
                [Role::class, 'r', 'WITH', 'rp.role = r.id'] => $queryBuilder,
                [Permission::class, 'p', 'WITH', 'rp.permission = p.id'] => $queryBuilder,
            });
        $queryBuilder->expects($this->once())
            ->method('where')
            ->with("ur.userId = :userId")
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('userId', $userId)
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with('r.name, p.name')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $registry = $this->createMock(ManagerRegistry::class);
        $rolePermissionRepository = $this->getMockBuilder(RolePermissionRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['getEntityManager'])
            ->getMock();
        $rolePermissionRepository->expects($this->exactly(1))
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $result = $rolePermissionRepository->findRolePermissionsBy($userId);
        $this->assertInstanceOf(Query::class, $result);
    }

    /**
     * @testCase - method findRolePermissions - must be a success
     *
     * @return void
     */
    public function testFindRolePermissions(): void
    {
        $query = $this->createMock(Query::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
            ->method('select')
            ->with(
                'rp.id',
                'r.name as nameRole',
                'p.name as namePermission'
            )
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('from')
            ->with(RolePermission::class, 'rp')
            ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
            ->method('leftJoin')
            ->willReturnCallback(fn($class, $alias, $w, $condition) => match ([$class, $alias, $w, $condition]) {
                [Role::class, 'r', 'WITH', 'rp.role = r.id'] => $queryBuilder,
                [Permission::class, 'p', 'WITH', 'rp.permission = p.id'] => $queryBuilder,
            });
        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with('r.name, p.name')
            ->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $registry = $this->createMock(ManagerRegistry::class);
        $rolePermissionRepository = $this->getMockBuilder(RolePermissionRepository::class)
            ->setConstructorArgs([$registry])
            ->onlyMethods(['getEntityManager'])
            ->getMock();
        $rolePermissionRepository->expects($this->exactly(1))
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $result = $rolePermissionRepository->findRolePermissions();
        $this->assertInstanceOf(Query::class, $result);
    }
}
