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

namespace App\Service;

use App\Entity\Role;
use App\Entity\RoleHierarchy;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Redis;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Class RoleService
 * @package App\Service
 */
class RoleService extends BaseService implements RoleServiceInterface
{
    /** @var Redis $redis */
    /** @psalm-suppress PropertyNotSetInConstructor */
    private Redis $redis;

    /**
     * @param string $redisHost
     * @param LoggerInterface $logger
     */
    public function __construct(
        #[Autowire('%app.redisHost%')] private string $redisHost,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Role $role
     * @param string $method
     *
     * @return EntityManagerInterface
     */
    public function persistParentRoles(
        EntityManagerInterface $entityManager,
        Role $role,
        string $method = 'create'
    ): EntityManagerInterface {
        $collection = $role->getParentRoles();
        $parentRoles = $collection->getValues();
        /** @var Role $parentRole */
        foreach ($parentRoles as $parentRole) {
            $roleHierarchyOld = null;
            if ($method == 'update') {
                $roleHierarchyOld = $entityManager->getRepository(RoleHierarchy::class)->findOneBy([
                    'childRoleId' => $role->getId(),
                    'parentRoleId' => $parentRole->getId(),
                ]);
            }
            if (empty($roleHierarchyOld)) {
                $role->addParent($parentRole);
                $entityManager->persist($role);
            }
        }

        return $entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Role $role
     * @param string $method
     *
     * @return EntityManagerInterface
     */
    public function persistChildRoles(
        EntityManagerInterface $entityManager,
        Role $role,
        string $method = 'create'
    ): EntityManagerInterface {
        $collection = $role->getChildRoles();
        $childRoles = $collection->getValues();
        /** @var Role $childRole */
        foreach ($childRoles as $childRole) {
            $roleHierarchyOld = null;
            if ($method == 'update') {
                $roleHierarchyOld = $entityManager->getRepository(RoleHierarchy::class)->findOneBy([
                    'childRoleId' => $childRole->getId(),
                    'parentRoleId' => $role->getId(),
                ]);
            }
            if (empty($roleHierarchyOld)) {
                $role->addChild($childRole);
                $entityManager->persist($role);
            }
        }

        return $entityManager;
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
     */
    public function setRedis(Redis $redis): void
    {
        $this->redis = $redis;
    }

    /**
     * @param Role|null $role
     * @throws Exception
     */
    public function roleSetRedis(?Role $role): void
    {
        if (! empty($role)) {
            $this->getRedis()->set(
                $this->getNameRedisRole() . $role->getId(),
                serialize($role),
                ['EX' => $this->getTtlRedisSetsRoles()]
            );
        }
    }

    /**
     * @return string
     */
    private function getNameRedisRole(): string
    {
        $name = Role::REDIS_ROLE;
        if ($this->isEnvTest()) {
            // @codeCoverageIgnoreStart
            $name = Role::REDIS_ROLE_TEST;
            // @codeCoverageIgnoreEnd
        }

        return $name;
    }

    /**
     * @param string $env
     *
     * @return bool
     */
    private function isEnvTest(string $env = 'TEST'): bool
    {
        $envIs = getenv('APP_ENV');

        return $envIs === $env;
    }

    /**
     * @return int
     */
    private function getTtlRedisSetsRoles(): int
    {
        $ttl = Role::REDIS_SETS_ROLES_TTL;
        if ($this->isEnvTest()) {
            // @codeCoverageIgnoreStart
            $ttl = Role::REDIS_SETS_ROLES_TTL_TEST;
            // @codeCoverageIgnoreEnd
        }

        return $ttl;
    }

    /**
     * @param Role $role
     *
     * @return void
     * @throws Exception
     */
    public function rolePushToQueueRedis(Role $role): void
    {
        $this->getRedis()->rPush($this->getNameRedisSetsRoles(), serialize($role));
        $this->getRedis()->expire($this->getNameRedisSetsRoles(), $this->getTtlRedisSetsRoles());
    }

    /**
     * @return string
     */
    private function getNameRedisSetsRoles(): string
    {
        $name = Role::REDIS_SETS_ROLES;
        if ($this->isEnvTest()) {
            // @codeCoverageIgnoreStart
            $name = Role::REDIS_SETS_ROLES_TEST;
            // @codeCoverageIgnoreEnd
        }

        return $name;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function rolesGetFromQueueRedis(): array
    {
        $roles = [];
        $rolesRedises = $this->getRedis()->lRange($this->getNameRedisSetsRoles(), 0, -1);
        $rolesRedises = (array) $rolesRedises;
        foreach ($rolesRedises as $rolesRedis) {
            if (is_string($rolesRedis)) {
                /** @var Role|null $roleRedis */
                $roleRedis = unserialize($rolesRedis);
            }
            if (! empty($roleRedis) && $roleRedis instanceof Role) {
                $roles[] = $roleRedis;
            }
        }

        return $roles;
    }

    /**
     * @param array $roles
     *
     * @return void
     * @throws Exception
     */
    public function rolesPushToQueueRedis(array $roles): void
    {
        foreach ($roles as $role) {
            $this->getRedis()->rPush($this->getNameRedisSetsRoles(), serialize($role));
        }
        $this->getRedis()->expire($this->getNameRedisSetsRoles(), $this->getTtlRedisSetsRoles());
    }

    /**
     * @param int $roleId
     *
     * @return bool
     * @throws Exception
     */
    public function roleCheckRedis(int $roleId): bool
    {
        return (bool)$this->getRedis()->exists($this->getNameRedisRole() . $roleId);
    }

    /**
     * @param int $roleId
     *
     * @return Role|null
     * @throws Exception
     */
    public function roleGetRedis(int $roleId): ?Role
    {
        /** @var string|null $roleStr */
        $roleStr = $this->getRedis()->get($this->getNameRedisRole() . $roleId);
        if (is_string($roleStr)) {
            /** @var Role|null $role */
            $role = unserialize($roleStr);
        }
        if (! empty($role) && $role instanceof Role) {
            return $role;
        }

        return null;
    }
}
