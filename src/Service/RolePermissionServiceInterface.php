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

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Interface RolePermissionServiceInterface
 * @package App\Service
 */
interface RolePermissionServiceInterface extends BaseServiceInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param User $user
     *
     * @return EntityManagerInterface
     */
    public function persistRolePermissionByUser(EntityManagerInterface $entityManager, User $user): EntityManagerInterface;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Permission $permission
     * @param string $method
     *
     * @return EntityManagerInterface
     */
    public function persistRolePermissionByPermission(
        EntityManagerInterface $entityManager,
        Permission $permission,
        string $method = 'create'
    ): EntityManagerInterface;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Role $role
     * @param string $method
     *
     * @return EntityManagerInterface
     */
    public function persistRolePermissionByRole(
        EntityManagerInterface $entityManager,
        Role $role,
        string $method = 'create'
    ): EntityManagerInterface;
}
