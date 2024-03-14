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

namespace App\Tests\Unit;

use App\Entity\Admin;
use App\Factory\AdminFactory;
use App\Factory\CommentFactory;
use App\Factory\LogFactory;
use App\Factory\PermissionFactory;
use App\Factory\RoleFactory;
use App\Factory\RolePermissionFactory;
use App\Factory\UserFactory;
use App\Helper\ApplicationGlobals;
use App\Tests\TestTrait;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Base class BaseKernelTestCase - for all unit tests
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @link https://symfony.com/doc/current/testing.html#types-of-tests
 *
 * @package App\Tests\Unit\Controller
 */
class BaseKernelTestCase extends KernelTestCase
{
    use TestTrait;

    /** @var KernelInterface $kernelTest */
    protected KernelInterface $kernelTest;

    /** @var ContainerInterface $container */
    protected ContainerInterface $container;

    /** @var Generator $faker */
    protected readonly Generator $faker;

    /** @var string $appDomain */
    protected string $appDomain;

    /** @var ApplicationGlobals $appGlobals */
    protected ApplicationGlobals $appGlobals;

    /** @var EntityManagerInterface $entityManager */
    protected EntityManagerInterface $entityManager;

    /** @var Admin $admin */
    protected Admin $admin;

    /** @var AdminFactory $adminFactory */
    protected AdminFactory $adminFactory;

    /** @var UserFactory $userFactory */
    protected UserFactory $userFactory;

    /** @var RoleFactory $roleFactory */
    protected RoleFactory $roleFactory;

    /** @var PermissionFactory $permissionFactory */
    protected PermissionFactory $permissionFactory;

    /** @var RolePermissionFactory $rolePermissionFactory */
    protected RolePermissionFactory $rolePermissionFactory;

    /** @var CommentFactory $commentFactory */
    protected CommentFactory $commentFactory;

    /** @var LogFactory $logFactory */
    protected LogFactory $logFactory;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $this->kernelTest = self::bootKernel();
        $this->container = static::getContainer();
        $this->faker = \Faker\Factory::create();
        $this->appDomain = $this->container->getParameter('app.domain');
        $this->appGlobals = $this->container->get(ApplicationGlobals::class);
        $this->appGlobals->setType(ApplicationGlobals::TYPE_APP_TESTS);
        $this->adminFactory = $this->container->get(AdminFactory::class);
        $this->roleFactory = $this->container->get(RoleFactory::class);
        $this->permissionFactory = $this->container->get(PermissionFactory::class);
        $this->rolePermissionFactory = $this->container->get(RolePermissionFactory::class);
        $this->userFactory = $this->container->get(UserFactory::class);
        $this->commentFactory = $this->container->get(CommentFactory::class);
        $this->logFactory = $this->container->get(LogFactory::class);
//        $this->prepareDbMySqlMock();
    }

    /**
     * @return KernelInterface
     */
    public function getKernelTest(): KernelInterface
    {
        return $this->kernelTest;
    }
}
