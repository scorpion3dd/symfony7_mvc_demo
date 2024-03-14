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

use App\Factory\UserRoleFactory;
use App\Service\RolePermissionService;
use App\Tests\Unit\BaseKernelTestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Class RolePermissionServiceTest - Unit tests for service RolePermissionService
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Service
 */
class RolePermissionServiceTest extends BaseKernelTestCase
{
    /** @var RolePermissionService $rolePermissionService */
    private RolePermissionService $rolePermissionService;

    /** @var UserRoleFactory|null $userRoleFactory */
    private ?UserRoleFactory $userRoleFactory;

    /** @var LoggerInterface|null $logger */
    private ?LoggerInterface $logger;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userRoleFactory = $this->container->get(UserRoleFactory::class);
        $this->logger = $this->container->get(LoggerInterface::class);
        $this->rolePermissionService = new RolePermissionService(
            $this->rolePermissionFactory,
            $this->userRoleFactory,
            $this->logger
        );
    }

    /**
     * @testCase - method persistRolePermissionByUser - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testPersistRolePermissionByUser(): void
    {
        $user = $this->createUser();
        $user->setRolePermissions($this->createRolePermissions());
        $rolePermission = $this->createRolePermission();
        $userRoleOld = null;

        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturn($userRoleOld, $rolePermission);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $entityManager = $this->rolePermissionService->persistRolePermissionByUser($entityManager, $user);
        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }

    /**
     * @testCase - method persistRolePermissionByPermission - must be a success
     *
     * @dataProvider providePersistRolePermissionByPermission
     *
     * @param string $version
     * @param int $id
     *
     * @return void
     */
    public function testPersistRolePermissionByPermission(string $version, int $id): void
    {
        $permission = $this->createPermission();
        $role = $this->createRole();
        if ($version == '1') {
            $role->setId($id);
        }
        $permission->addRole($role);
        $rolePermission = null;
        $rolePermissions = $this->createRolePermissions();
        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->willReturn($rolePermissions);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->willReturn($rolePermission);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $entityManager = $this->rolePermissionService->persistRolePermissionByPermission(
            $entityManager,
            $permission,
            'update'
        );
        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }

    /**
     * @return iterable
     */
    public static function providePersistRolePermissionByPermission(): iterable
    {
        $version = '1';
        $id = 1;
        yield $version => [$version, $id];

        $version = '2';
        $id = 0;
        yield $version => [$version, $id];
    }

    /**
     * @testCase - method persistRolePermissionByRole - must be a success
     *
     * @dataProvider providePersistRolePermissionByRole
     *
     * @param string $version
     * @param int $id
     *
     * @return void
     */
    public function testPersistRolePermissionByRole(string $version, int $id): void
    {
        $permission = $this->createPermission();
        if ($version == '1') {
            $permission->setId($id);
        }
        $role = $this->createRole();
        $role->addPermission($permission);
        $rolePermission = null;
        $rolePermissions = $this->createRolePermissions();
        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->willReturn($rolePermissions);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->willReturn($rolePermission);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $entityManager = $this->rolePermissionService->persistRolePermissionByRole(
            $entityManager,
            $role,
            'update'
        );
        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }

    /**
     * @return iterable
     */
    public static function providePersistRolePermissionByRole(): iterable
    {
        $version = '1';
        $id = 1;
        yield $version => [$version, $id];

        $version = '2';
        $id = 0;
        yield $version => [$version, $id];
    }
}
