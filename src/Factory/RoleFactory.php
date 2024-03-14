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

use App\Entity\Role;
use Carbon\Carbon;

/**
 * Class RoleFactory - is the Factory Method design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/FactoryMethod/README.html
 * @package App\Factory
 */
class RoleFactory
{
    public function __construct()
    {
    }

    /**
     * @param string $name
     * @param string $description
     * @param Role|null $parentRole
     * @param Role|null $childRole
     *
     * @return Role
     */
    public function create(string $name, string $description, ?Role $parentRole = null, ?Role $childRole = null): Role
    {
        $role = new Role();
        $role->setName($name);
        $role->setDescription($description);
        $role->setDateCreated(Carbon::now());
        if (! empty($parentRole)) {
            $role->setParentRole($parentRole);
            $role->addParent($parentRole);
        }
        if (! empty($childRole)) {
            $role->setChildRole($childRole);
            $role->addChild($childRole);
        }

        return $role;
    }
}
