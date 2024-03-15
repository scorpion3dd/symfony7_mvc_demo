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

namespace App\Tests\Unit\DataFixtures\Builder;

use App\DataFixtures\Builder\AppMainFixturesBuilder;
use App\DataFixtures\Builder\BaseFixturesBuilder;
use App\DataFixtures\Builder\Parts\Fixtures;
use App\Enum\Environments;
use App\Factory\AdminFactory;
use App\Factory\CommentFactory;
use App\Factory\LogFactory;
use App\Factory\PermissionFactory;
use App\Factory\RoleFactory;
use App\Factory\RolePermissionFactory;
use App\Factory\UserFactory;
use App\Factory\UserRoleFactory;
use App\Helper\JwtTokensHelper;
use App\Tests\Unit\DataFixtures\BaseTestAppFixtures;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Redis;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * Class AppMainFixturesBuilderTest - Unit tests for AppMainFixturesBuilder
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\DataFixtures\Builder
 */
class AppMainFixturesBuilderTest extends BaseTestAppFixtures
{
    public const PATH = '/../../data/DataFixtures/Builder';

    /** @var AppMainFixturesBuilder $builder */
    private AppMainFixturesBuilder $builder;

    /** @var AdminFactory $adminFactoryMock */
    private $adminFactoryMock;

    /** @var UserFactory $userFactoryMock */
    private $userFactoryMock;

    /** @var CommentFactory $commentFactoryMock */
    private $commentFactoryMock;

    /** @var RoleFactory $roleFactoryMock */
    private $roleFactoryMock;

    /** @var PermissionFactory $permissionFactoryMock */
    private $permissionFactoryMock;

    /** @var RolePermissionFactory $rolePermFactoryMock */
    private $rolePermFactoryMock;

    /** @var UserRoleFactory $userRoleFactoryMock */
    private $userRoleFactoryMock;

    /** @var JwtTokensHelper $jwtTokensHelperMock */
    private $jwtTokensHelperMock;

    /** @var DocumentManager $dmMock */
    private $dmMock;

    /** @var PasswordHasherFactoryInterface $passwordFactoryMock */
    private $passwordFactoryMock;

    /** @var LogFactory $logFactoryMock */
    private $logFactoryMock;

    /** @var ObjectManager $objectManagerMock */
    private $objectManagerMock;

    /** @var int|null $countAdmins */
    private ?int $countAdmins = null;

    /** @var int|null $countResidentUsers */
    private ?int $countResidentUsers = null;

    /** @var int|null $countNotResidUsers */
    private ?int $countNotResidUsers = null;

    /** @var int|null $countLogs */
    private ?int $countLogs = null;

    /** @var string $photoDir */
    private string $photoDir = self::PATH;

    /** @var Redis $redisMock */
    private $redisMock;

    /** @var string $redisHost */
    private string $redisHost;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->adminFactoryMock = $this->createMock(AdminFactory::class);
        $this->userFactoryMock = $this->createMock(UserFactory::class);
        $this->commentFactoryMock = $this->createMock(CommentFactory::class);
        $this->roleFactoryMock = $this->createMock(RoleFactory::class);
        $this->permissionFactoryMock = $this->createMock(PermissionFactory::class);
        $this->rolePermFactoryMock = $this->createMock(RolePermissionFactory::class);
        $this->userRoleFactoryMock = $this->createMock(UserRoleFactory::class);
        $this->jwtTokensHelperMock = $this->createMock(JwtTokensHelper::class);
        $this->dmMock = $this->createMock(DocumentManager::class);
        $this->passwordFactoryMock = $this->createMock(PasswordHasherFactoryInterface::class);
        $this->logFactoryMock = $this->createMock(LogFactory::class);
        $this->objectManagerMock = $this->createMock(ObjectManager::class);
        $this->redisMock = $this->createMock(Redis::class);
        $this->redisHost = $this->container->getParameter('app.redisHost');
        $this->builder = new AppMainFixturesBuilder(
            $this->adminFactoryMock,
            $this->passwordFactoryMock,
            $this->userFactoryMock,
            $this->commentFactoryMock,
            $this->roleFactoryMock,
            $this->permissionFactoryMock,
            $this->rolePermFactoryMock,
            $this->userRoleFactoryMock,
            $this->jwtTokensHelperMock,
            $this->dmMock,
            $this->logFactoryMock,
            $this->loggerMock,
            $this->countAdmins,
            $this->countResidentUsers,
            $this->countNotResidUsers,
            $this->countLogs,
            $this->appDomain,
            $this->photoDir,
            $this->redisHost,
        );
    }

    /**
     * @testCase - method build - must be a success
     *
     * @dataProvider provideBuild
     *
     * @param string $version
     * @param string $environment
     *
     * @return void
     * @throws Exception
     */
    public function testBuild(string $version, string $environment): void
    {
        $token = 'dfgs87afas';
        $this->jwtTokensHelperMock->expects($this->any())
            ->method('createJwtToken')
            ->willReturn($token);

        $this->objectManagerMock->expects($this->any())
            ->method('persist');
        $this->objectManagerMock->expects($this->exactly(4))
            ->method('flush');

        $this->builder->setRedis($this->redisMock);
        $this->builder->setEnvironment($environment);

        $fixtures = $this->builder->build($this->objectManagerMock);
        $this->assertInstanceOf(Fixtures::class, $fixtures);
        $expected = [
            0 => 'Clean dir ' . $this->photoDir,
            1 => 'Unlink files = 0;',
            2 => 'Count Admins = ' . BaseFixturesBuilder::COUNT_ADMINS . ';',
            3 => 'Count Roles = 4 items to Redis DB in set roles',
            4 => 'Count Roles = 4;',
            5 => 'Count Resident Users = ' . BaseFixturesBuilder::COUNT_RESIDENT_USERS . ';',
            6 => 'Count Not Resident Users = ' . BaseFixturesBuilder::COUNT_NOT_RESIDENT_USERS . ';',
            7 => 'Count Permissions = 5;',
            8 => 'Count Role Permissions = 5;',
            9 => 'Count Logs = ' . BaseFixturesBuilder::COUNT_LOGS . ';'
        ];
        $this->assertEquals($expected, $fixtures->getElements());
    }

    /**
     * @return iterable
     */
    public static function provideBuild(): iterable
    {
        $version = '1';
        $environment = Environments::TEST;
        yield $version => [$version, $environment];

        $version = '2';
        $environment = Environments::DEV;
        yield $version => [$version, $environment];
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
        $this->builder->setRedis($redis);
        $result = $this->builder->getRedis();
        $this->assertSame($redis, $result);
    }
}
