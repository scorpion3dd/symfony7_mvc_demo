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

namespace App\DataFixtures\Builder;

use App\DataFixtures\Builder\Parts\AppMainFixture;
use App\DataFixtures\Builder\Parts\Fixtures;
use App\Entity\Admin;
use App\Entity\Comment;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\RolePermission;
use App\Entity\User;
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
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Psr\Log\LoggerInterface;
use Redis;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * Class AppMainFixturesBuilder - is part of the Builder design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/Builder/README.html
 * @package App\DataFixtures\Builder
 */
class AppMainFixturesBuilder extends BaseFixturesBuilder implements Builder
{
    /** @var Redis $redis */
    /** @psalm-suppress PropertyNotSetInConstructor */
    private Redis $redis;

    /** @var string $environment */
    private string $environment = '';

    /**
     * @param AdminFactory $adminFactory
     * @param PasswordHasherFactoryInterface $passwordFactory
     * @param UserFactory $userFactory
     * @param CommentFactory $commentFactory
     * @param RoleFactory $roleFactory
     * @param PermissionFactory $permissionFactory
     * @param RolePermissionFactory $rolePermFactory
     * @param UserRoleFactory $userRoleFactory
     * @param JwtTokensHelper $jwtTokensHelper
     * @param DocumentManager $dm
     * @param LogFactory $logFactory
     * @param LoggerInterface $logger
     * @param int|null $countAdmins
     * @param int|null $countResidentUsers
     * @param int|null $countNotResidUsers
     * @param int|null $countLogs
     * @param string $appDomain
     * @param string $photoDir
     * @param string $redisHost
     * @throws Exception
     */
    public function __construct(
        private readonly AdminFactory $adminFactory,
        private readonly PasswordHasherFactoryInterface $passwordFactory,
        private readonly UserFactory $userFactory,
        private readonly CommentFactory $commentFactory,
        private readonly RoleFactory $roleFactory,
        private readonly PermissionFactory $permissionFactory,
        private readonly RolePermissionFactory $rolePermFactory,
        private readonly UserRoleFactory $userRoleFactory,
        private readonly JwtTokensHelper $jwtTokensHelper,
        private readonly DocumentManager $dm,
        LogFactory $logFactory,
        LoggerInterface $logger,
        #[Autowire('%app.countAdmins%')] private ?int $countAdmins,
        #[Autowire('%app.countResidentUsers%')] private ?int $countResidentUsers,
        #[Autowire('%app.countNotResidentUsers%')] private ?int $countNotResidUsers,
        #[Autowire('%app.countLogs%')] ?int $countLogs,
        #[Autowire('%app.domain%')] private string $appDomain,
        #[Autowire('%app.photoDir%')] private string $photoDir,
        #[Autowire('%app.redisHost%')] private string $redisHost,
    ) {
        parent::__construct($logFactory, $countLogs, $logger);
        $this->debugConstruct(self::class);
        if (empty($this->countAdmins)) {
            $this->countAdmins = self::COUNT_ADMINS;
        }
        if (empty($this->countResidentUsers)) {
            $this->countResidentUsers = self::COUNT_RESIDENT_USERS;
        }
        if (empty($this->countNotResidUsers)) {
            $this->countNotResidUsers = self::COUNT_NOT_RESIDENT_USERS;
        }
    }

    /**
     * @param ObjectManager $om
     *
     * @return Fixtures
     * @throws Exception
     */
    public function build(ObjectManager $om): Fixtures
    {
        $this->createFixtures(new AppMainFixture());
        $this->cleanUploadsPhotosDir();
        $this->addAdmins($om);
        /**
         * @var Role $roleNotResident
         * @var Role $roleResident
         */
        list($roleNotResident, $roleResident) = $this->addRoles($om);
        $residentUsers = $this->addResidentUsers($om);
        $notResidentUsers = $this->addNotResidentUsers($om);
        /**
         * @var Permission $permissionUserUsa
         * @var Permission $permissionUserEurope
         * @var Permission $permissionUserAsia
         * @var Permission $permissionUserMoldova
         * @var Permission $permissionUserUkraine
         */
        list($permissionUserUsa,
            $permissionUserEurope,
            $permissionUserAsia,
            $permissionUserMoldova,
            $permissionUserUkraine) = $this->addPermissions($om);
        /**
         * @var array $notResidentRolePermissions
         * @var RolePermission $rolePermissionResident
         */
        list($notResidentRolePermissions, $rolePermissionResident) = $this->addRolePermissions(
            $om,
            $roleNotResident,
            $roleResident,
            $permissionUserUsa,
            $permissionUserEurope,
            $permissionUserAsia,
            $permissionUserMoldova,
            $permissionUserUkraine
        );
        $this->addResidentUserRoles($residentUsers, $om, $rolePermissionResident, $notResidentRolePermissions);
        $this->addNotResidentUserRoles($notResidentUsers, $notResidentRolePermissions, $om);
        $this->flush($om);
        $this->addLogs($this->dm);

        return $this->getFixtures();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function initRedis(): void
    {
        $this->debugFunction(self::class, 'initRedis');
        $this->redis = new Redis();
        try {
            $this->redis->connect($this->redisHost);
        // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $this->exception(self::class, $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @psalm-suppress TypeDoesNotContainType
     *
     * @return Redis
     * @throws Exception
     */
    public function getRedis(): Redis
    {
        if (empty($this->redis)) {
            $this->initRedis();
        }

        return $this->redis;
    }

    /**
     * @param Redis $redis
     *
     * @return void
     */
    public function setRedis(Redis $redis): void
    {
        $this->redis = $redis;
    }

    /**
     * @return void
     */
    private function cleanUploadsPhotosDir(): void
    {
        $this->debugFunction(self::class, 'cleanUploadsPhotosDir');
        $this->getFixtures()->addElement('Clean dir ' . $this->photoDir);
        $files = glob($this->photoDir . "/*");
        if ($files === false) {
            // @codeCoverageIgnoreStart
            $files = [];
            // @codeCoverageIgnoreEnd
        }
        $count = count($files);
        if ($count > 0) {
            // @codeCoverageIgnoreStart
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            // @codeCoverageIgnoreEnd
        }
        $this->getFixtures()->addElement('Unlink files = ' . $count . ';');
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     * @throws Exception
     */
    private function addAdmins(ObjectManager $manager): void
    {
        for ($i = 1; $i <= $this->countAdmins; $i++) {
            $admin = $this->adminFactory->create(
                'admin' . $i,
                $this->passwordFactory->getPasswordHasher(Admin::class)->hash('admin' . $i),
            );
            $token = $this->jwtTokensHelper->createJwtToken($admin);
            if ($token) {
                $admin->setToken($token);
            }
            $manager->persist($admin);

            $refreshToken = $this->jwtTokensHelper->createJwtRefreshToken($admin);
            $manager->persist($refreshToken);
        }
        $this->getFixtures()->addElement('Count Admins = ' . $this->countAdmins . ';');
    }

    /**
     * @param ObjectManager $manager
     *
     * @return array
     * @throws Exception
     */
    private function addRoles(ObjectManager $manager): array
    {
        $roles = [];
        $roleHuman = $this->roleFactory->create(
            'Human',
            'A person who is human.'
        );
        $manager->persist($roleHuman);
        $roles[] = $roleHuman;

        $roleAnimal = $this->roleFactory->create(
            'Animal',
            'A person who is animal.'
        );
        $manager->persist($roleAnimal);
        $roles[] = $roleAnimal;

        $manager->flush();

        $roleNotResident = $this->roleFactory->create(
            'Not resident',
            'A person who is not resident this country.',
            $roleHuman,
            $roleAnimal
        );
        $manager->persist($roleNotResident);
        $roles[] = $roleNotResident;

        $roleResident = $this->roleFactory->create(
            'Resident',
            'A person who is resident this country.',
            $roleHuman,
            $roleAnimal
        );
        $manager->persist($roleResident);
        $roles[] = $roleResident;

        $this->dropRedis($roles);
        $this->createRoleRedis($roles);

        $this->getFixtures()->addElement('Count Roles = ' . count($roles) . ';');

        return [$roleNotResident, $roleResident];
    }

    /**
     * @param array $roles
     *
     * @return void
     * @throws Exception
     */
    private function dropRedis(array $roles): void
    {
        if ($this->getEnvironment() == Environments::TEST) {
            $this->getRedis()->del(Role::REDIS_SETS_ROLES_TEST);
            $this->getRedis()->del(Role::REDIS_ROLE_SET_TEST);
        } elseif ($this->getEnvironment() == Environments::DEV) {
            $this->getRedis()->del(Role::REDIS_SETS_ROLES);
            $this->getRedis()->del(Role::REDIS_ROLE_SET);
        }
        /** @var Role $role */
        foreach ($roles as $role) {
            if ($this->getEnvironment() == Environments::TEST) {
                $this->getRedis()->del(Role::REDIS_ROLE_TEST . $role->getId());
            } elseif ($this->getEnvironment() == Environments::DEV) {
                $this->getRedis()->del(Role::REDIS_ROLE . $role->getId());
            }
        }
    }

    /**
     * @psalm-suppress InvalidCast
     * @param array $roles
     *
     * @return void
     * @throws Exception
     */
    private function createRoleRedis(array $roles): void
    {
        $count = $this->getRedis()->lLen(Role::REDIS_SETS_ROLES);
        $count = (int) $count;
        if (! empty($count) && $count > 0) {
            // @codeCoverageIgnoreStart
            $this->getRedis()->del(Role::REDIS_SETS_ROLES);
            // @codeCoverageIgnoreEnd
        }
        $countRoles = 0;
        /** @var Role $role */
        foreach ($roles as $role) {
            $roleSerialize = serialize($role);
            $this->getRedis()->rPush(Role::REDIS_SETS_ROLES, $roleSerialize);
            $this->getRedis()->set(
                Role::REDIS_ROLE . $role->getId(),
                serialize($role),
                ['EX' => Role::REDIS_SETS_ROLES_TTL]
            );
            $countRoles++;
        }
        $this->getRedis()->expire(Role::REDIS_SETS_ROLES, Role::REDIS_SETS_ROLES_TTL);

        $value = 'Count Roles = ' . $countRoles . ' items to Redis DB in set ' . Role::REDIS_SETS_ROLES;
        $this->getFixtures()->addElement($value);
    }

    /**
     * @param ObjectManager $manager
     *
     * @return array
     * @throws Exception
     */
    private function addResidentUsers(ObjectManager $manager): array
    {
        $residentUsers = [];
        for ($i = 1; $i <= $this->countResidentUsers; $i++) {
            $user = $this->addUser($i, 'resident');
            $manager->persist($user);
            $residentUsers[] = $user;
            $this->addCommentsForUser($user, $manager);
        }
        $this->getFixtures()->addElement('Count Resident Users = ' . $this->countResidentUsers . ';');

        return $residentUsers;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return array
     * @throws Exception
     */
    private function addNotResidentUsers(ObjectManager $manager): array
    {
        $notResidentUsers = [];
        for ($i = 1; $i <= $this->countNotResidUsers; $i++) {
            $user = $this->addUser($i, 'not-resident');
            $manager->persist($user);
            $notResidentUsers[] = $user;
            $this->addCommentsForUser($user, $manager);
        }
        $this->getFixtures()->addElement('Count Not Resident Users = ' . $this->countNotResidUsers . ';');

        return $notResidentUsers;
    }

    /**
     * @param User $user
     * @param ObjectManager $manager
     *
     * @return void
     * @throws Exception
     */
    private function addCommentsForUser(User $user, ObjectManager $manager): void
    {
        $countComments = random_int(0, 3);
        for ($j = 1; $j <= $countComments; $j++) {
            $genderId = User::randomGenderId();
            $gender = $genderId == User::GENDER_MALE_ID ? User::GENDER_MALE : User::GENDER_FEMALE;
            $comment = $this->commentFactory->create(
                $user,
                $this->faker->name($gender),
                $this->faker->email(),
                $this->faker->text(1024),
                Comment::randomStateComment()
            );
            $manager->persist($comment);
        }
    }

    /**
     * @param int $i
     * @param string $beginerEmail
     *
     * @return User
     * @throws Exception
     */
    private function addUser(int $i, string $beginerEmail): User
    {
        $genderId = User::randomGenderId();
        $gender = $genderId == User::GENDER_MALE_ID ? User::GENDER_MALE : User::GENDER_FEMALE;
        $createdAt = $this->faker->dateTimeBetween('-15 years', 'now');

        return $this->userFactory->create(
            "$beginerEmail-$i@{$this->appDomain}",
            $genderId,
            $this->faker->userName() . $i,
            $this->faker->name($gender),
            $this->faker->text(1024),
            User::randomStatusId(),
            User::randomAccessId(),
            $this->faker->dateTimeBetween('-50 years', '-20 years'),
            $createdAt,
            $this->faker->dateTimeBetween($createdAt, 'now'),
        );
    }

    /**
     * @param ObjectManager $manager
     *
     * @return array
     */
    private function addPermissions(ObjectManager $manager): array
    {
        $countPermissions = 0;
        $permissionUserUsa = $this->permissionFactory->create('Usa', 'Users from USA');
        $manager->persist($permissionUserUsa);
        $countPermissions++;

        $permissionUserEurope = $this->permissionFactory->create('Europe', 'Users from Europe');
        $manager->persist($permissionUserEurope);
        $countPermissions++;

        $permissionUserAsia = $this->permissionFactory->create('Asia', 'Users from Asia');
        $manager->persist($permissionUserAsia);
        $countPermissions++;

        $permissionUserMoldova = $this->permissionFactory->create('Moldova', 'Users from Moldova');
        $manager->persist($permissionUserMoldova);
        $countPermissions++;

        $permissionUserUkraine = $this->permissionFactory->create('Ukraine', 'Users from Ukraine');
        $manager->persist($permissionUserUkraine);
        $countPermissions++;
        $this->getFixtures()->addElement('Count Permissions = ' . $countPermissions . ';');

        $manager->flush();

        return [$permissionUserUsa, $permissionUserEurope, $permissionUserAsia, $permissionUserMoldova, $permissionUserUkraine];
    }

    /**
     * @param ObjectManager $manager
     * @param Role $roleNotResident
     * @param Role $roleResident
     * @param Permission $permissionUserUsa
     * @param Permission $permissionUserEurope
     * @param Permission $permissionUserAsia
     * @param Permission $permissionUserMoldova
     * @param Permission $permissionUserUkraine
     *
     * @return array
     */
    private function addRolePermissions(
        ObjectManager $manager,
        Role $roleNotResident,
        Role $roleResident,
        Permission $permissionUserUsa,
        Permission $permissionUserEurope,
        Permission $permissionUserAsia,
        Permission $permissionUserMoldova,
        Permission $permissionUserUkraine
    ): array {
        $countRolePermission = 0;
        $notResidentRolePermissions = [];

        $rolePermUserUsa = $this->rolePermFactory->create($roleNotResident, $permissionUserUsa);
        $manager->persist($rolePermUserUsa);
        $notResidentRolePermissions[] = $rolePermUserUsa;
        $countRolePermission++;

        $rolePermUserEurope = $this->rolePermFactory->create($roleNotResident, $permissionUserEurope);
        $manager->persist($rolePermUserEurope);
        $notResidentRolePermissions[] = $rolePermUserEurope;
        $countRolePermission++;

        $rolePermUserAsia = $this->rolePermFactory->create($roleNotResident, $permissionUserAsia);
        $manager->persist($rolePermUserAsia);
        $notResidentRolePermissions[] = $rolePermUserAsia;
        $countRolePermission++;

        $rolePermUserMoldova = $this->rolePermFactory->create($roleNotResident, $permissionUserMoldova);
        $manager->persist($rolePermUserMoldova);
        $notResidentRolePermissions[] = $rolePermUserMoldova;
        $countRolePermission++;

        $rolePermissionResident = $this->rolePermFactory->create($roleResident, $permissionUserUkraine);
        $manager->persist($rolePermissionResident);
        $countRolePermission++;

        $this->getFixtures()->addElement('Count Role Permissions = ' . $countRolePermission . ';');

        $manager->flush();

        return [$notResidentRolePermissions, $rolePermissionResident];
    }

    /**
     * @param array $residentUsers
     * @param ObjectManager $manager
     * @param RolePermission $rolePermissionResident
     * @param array $notResidentRolePermissions
     *
     * @return void
     * @throws Exception
     */
    private function addResidentUserRoles(
        array $residentUsers,
        ObjectManager $manager,
        RolePermission $rolePermissionResident,
        array $notResidentRolePermissions
    ): void {
        foreach ($residentUsers as $residentUser) {
            $userRole = $this->userRoleFactory->create($rolePermissionResident, $residentUser);
            $manager->persist($userRole);
            if (random_int(0, 5) == 5) {
                if (count($notResidentRolePermissions) > 0) {
                    $userRole = $this->userRoleFactory->create(
                        $notResidentRolePermissions[random_int(0, count($notResidentRolePermissions) - 1)],
                        $residentUser
                    );
                    $manager->persist($userRole);
                }
            }
        }
    }

    /**
     * @param array $notResidentUsers
     * @param array $notResidentRolePermissions
     * @param ObjectManager $manager
     *
     * @return void
     * @throws Exception
     */
    private function addNotResidentUserRoles(
        array $notResidentUsers,
        array $notResidentRolePermissions,
        ObjectManager $manager
    ): void {
        foreach ($notResidentUsers as $notResidentUser) {
            if (count($notResidentRolePermissions) > 0) {
                $userRole = $this->userRoleFactory->create(
                    $notResidentRolePermissions[random_int(0, count($notResidentRolePermissions) - 1)],
                    $notResidentUser
                );
                $manager->persist($userRole);
            }
        }
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment(string $environment): void
    {
        $this->environment = $environment;
    }
}
