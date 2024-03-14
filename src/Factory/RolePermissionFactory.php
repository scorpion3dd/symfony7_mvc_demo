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

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\RolePermission;

/**
 * Class RolePermissionFactory - is the Factory Method design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/FactoryMethod/README.html
 * @package App\Factory
 */
class RolePermissionFactory
{
    public function __construct()
    {
    }

    /**
     * @param Role $role
     * @param Permission $permission
     *
     * @return RolePermission
     */
    public function create(Role $role, Permission $permission): RolePermission
    {
        $rolePermission = new RolePermission();
        $rolePermission->setRole($role);
        $rolePermission->setPermission($permission);

        return $rolePermission;
    }
}
