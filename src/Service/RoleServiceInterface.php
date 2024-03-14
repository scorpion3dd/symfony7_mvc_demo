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
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Redis;

/**
 * Interface RoleServiceInterface
 * @package App\Service
 */
interface RoleServiceInterface extends BaseServiceInterface
{
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
    ): EntityManagerInterface;

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
    ): EntityManagerInterface;

    /**
     * @return Redis
     * @throws Exception
     */
    public function getRedis(): Redis;

    /**
     * @param Redis $redis
     */
    public function setRedis(Redis $redis): void;

    /**
     * @param Role|null $role
     * @throws Exception
     */
    public function roleSetRedis(?Role $role): void;

    /**
     * @param Role $role
     *
     * @return void
     * @throws Exception
     */
    public function rolePushToQueueRedis(Role $role): void;

    /**
     * @return array
     * @throws Exception
     */
    public function rolesGetFromQueueRedis(): array;

    /**
     * @param array $roles
     *
     * @return void
     * @throws Exception
     */
    public function rolesPushToQueueRedis(array $roles): void;

    /**
     * @param int $roleId
     *
     * @return bool
     * @throws Exception
     */
    public function roleCheckRedis(int $roleId): bool;

    /**
     * @param int $roleId
     *
     * @return Role|null
     * @throws Exception
     */
    public function roleGetRedis(int $roleId): ?Role;
}
