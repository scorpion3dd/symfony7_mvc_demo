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

use App\Entity\Role;
use App\Service\RoleService;
use App\Tests\Unit\BaseKernelTestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Redis;

/**
 * Class RoleServiceTest - Unit tests for service RoleService
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Service
 */
class RoleServiceTest extends BaseKernelTestCase
{
    /** @var RoleService $roleService */
    private RoleService $roleService;

    /** @var Redis $redisMock */
    private $redisMock;

    /** @var LoggerInterface|null $logger */
    private ?LoggerInterface $logger;

    /** @var string $redisHost */
    protected string $redisHost;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->redisMock = $this->createMock(Redis::class);
        $this->redisHost = $this->container->getParameter('app.redisHost');
        $this->logger = $this->container->get(LoggerInterface::class);
        $this->roleService = new RoleService($this->redisHost, $this->logger);
    }

    /**
     * @testCase - method persistParentRoles - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testPersistParentRoles(): void
    {
        $role = $this->createRole();
        $role->setId(3);
        $roleHierarchyOld = null;

        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->willReturn($roleHierarchyOld);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $entityManager = $this->roleService->persistParentRoles($entityManager, $role, 'update');
        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }

    /**
     * @testCase - method persistChildRoles - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testPersistChildRoles(): void
    {
        $role = $this->createRole();
        $role->setId(3);
        $roleHierarchyOld = null;

        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->willReturn($roleHierarchyOld);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $entityManager = $this->roleService->persistChildRoles($entityManager, $role, 'update');
        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }

    /**
     * @testCase - method roleSetRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRoleSetRedis(): void
    {
        $this->roleService->setRedis($this->redisMock);
        $role = $this->createRole();
        $this->roleService->roleSetRedis($role);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method rolePushToQueueRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRolePushToQueueRedis(): void
    {
        $this->roleService->setRedis($this->redisMock);
        $role = $this->createRole();
        $this->roleService->rolePushToQueueRedis($role);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method rolesGetFromQueueRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRolesGetFromQueueRedis(): void
    {
        $rolesRedises = [];
        $role = $this->createRole();
        $rolesRedis = serialize($role);
        $rolesRedises[] = $rolesRedis;
        $this->redisMock->expects($this->any())
            ->method('lRange')
            ->willReturn($rolesRedises);
        $this->roleService->setRedis($this->redisMock);

        $expected = [];
        $expected[] = $role;
        $roles = $this->roleService->rolesGetFromQueueRedis();
        $this->assertIsArray($roles);
        $this->assertEquals($expected, $roles);
    }

    /**
     * @testCase - method rolesPushToQueueRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRolesPushToQueueRedis(): void
    {
        $roles = [];
        $role = $this->createRole();
        $roles[] = $role;
        $this->roleService->setRedis($this->redisMock);
        $this->roleService->rolesPushToQueueRedis($roles);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method roleCheckRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRoleCheckRedis(): void
    {
        $roleId = 1;
        $this->redisMock->expects($this->any())
            ->method('exists')
            ->willReturn(true);
        $this->roleService->setRedis($this->redisMock);

        $check = $this->roleService->roleCheckRedis($roleId);
        $this->assertTrue($check);
    }

    /**
     * @testCase - method roleGetRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRoleGetRedis(): void
    {
        $roleId = 1;
        $role = $this->createRole();
        $rolesRedis = serialize($role);
        $this->redisMock->expects($this->any())
            ->method('get')
            ->willReturn($rolesRedis);
        $this->roleService->setRedis($this->redisMock);

        $roleGet = $this->roleService->roleGetRedis($roleId);
        $this->assertInstanceOf(Role::class, $roleGet);
    }

    /**
     * @testCase - method roleGetRedis - must be a success, Null
     *
     * @return void
     * @throws Exception
     */
    public function testRoleGetRedisNull(): void
    {
        $roleId = 1;
        $this->redisMock->expects($this->any())
            ->method('get')
            ->willReturn(null);
        $this->roleService->setRedis($this->redisMock);

        $roleGet = $this->roleService->roleGetRedis($roleId);
        $this->assertNull($roleGet);
    }

    /**
     * @testCase - method getRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetRedis(): void
    {
        $redis = new Redis();
        $this->roleService->setRedis($redis);
        $result = $this->roleService->getRedis();
        $this->assertSame($redis, $result);
    }
}
