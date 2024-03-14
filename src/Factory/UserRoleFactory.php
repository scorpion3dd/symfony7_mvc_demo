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

namespace App\Factory;

use App\Entity\RolePermission;
use App\Entity\User;
use App\Entity\UserRole;

/**
 * Class UserRoleFactory - is the Factory Method design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/FactoryMethod/README.html
 * @package App\Factory
 */
class UserRoleFactory
{
    public function __construct()
    {
    }

    /**
     * @param RolePermission $rolePermission
     * @param User $user
     *
     * @return UserRole
     */
    public function create(RolePermission $rolePermission, User $user): UserRole
    {
        $userRole = new UserRole();
        $userRole->setRolePermissionId($rolePermission->getId() ?? 0);
        $userRole->setUserId($user->getId() ?? 0);

        return $userRole;
    }
}
