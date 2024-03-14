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

use App\Entity\Admin;
use App\Repository\AdminRepository;
use App\Repository\AdminRepositoryInterface;
use App\Service\AdminService;
use App\Tests\Unit\BaseKernelTestCase;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AdminServiceTest - Unit tests for service AdminService
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Service
 */
class AdminServiceTest extends BaseKernelTestCase
{
    /** @var AdminService $adminService */
    private AdminService $adminService;

    /** @var AdminRepositoryInterface|null $repository */
    private ?AdminRepositoryInterface $repository;

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
        $this->logger = $this->container->get(LoggerInterface::class);
        $this->repository = $this->container->get(AdminRepositoryInterface::class);
        $this->adminService = new AdminService($this->repository, $this->logger);
    }

    /**
     * @testCase - method findOneById - must be a success
     *
     * @return void
     */
    public function testFindOneById(): void
    {
        $adminId = 1;
        $username = 'username';
        $password = 'password';
        $admin = $this->createAdmin($username, $password);
        $repositoryMock = $this->getMockBuilder(AdminRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo($adminId))
            ->willReturn($admin);
        $this->adminService->setRepository($repositoryMock);

        $adminNew = $this->adminService->findOneById($adminId);
        $this->assertInstanceOf(Admin::class, $adminNew);
        $this->assertEquals($username, $adminNew->getUsername());
        $this->assertEquals($password, $adminNew->getPassword());
    }

    /**
     * @testCase - method findOneByLogin - must be a success
     *
     * @return void
     * @throws NonUniqueResultException
     */
    public function testFindOneByLogin(): void
    {
        $username = 'username';
        $password = 'password';
        $admin = $this->createAdmin($username, $password);
        $repositoryMock = $this->getMockBuilder(AdminRepository::class)
            ->onlyMethods(['findOneByLogin'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByLogin')
            ->with($this->equalTo($username))
            ->willReturn($admin);
        $this->adminService->setRepository($repositoryMock);

        $adminNew = $this->adminService->findOneByLogin($username);
        $this->assertInstanceOf(Admin::class, $adminNew);
        $this->assertEquals($username, $adminNew->getUsername());
        $this->assertEquals($password, $adminNew->getPassword());
    }

    /**
     * @testCase - method findAll - must be a success
     *
     * @return void
     */
    public function testFindAll(): void
    {
        $username = 'username';
        $password = 'password';
        $admin = $this->createAdmin($username, $password);
        $admins = [];
        $admins[] = $admin;
        $repositoryMock = $this->getMockBuilder(AdminRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findAll')
            ->willReturn($admins);
        $this->adminService->setRepository($repositoryMock);

        $adminsNew = $this->adminService->findAll();
        $this->assertIsArray($adminsNew);
        foreach ($adminsNew as $adminNew) {
            $this->assertInstanceOf(Admin::class, $adminNew);
            $this->assertEquals($username, $adminNew->getUsername());
            $this->assertEquals($password, $adminNew->getPassword());
        }
    }
}
