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
use Carbon\Carbon;

/**
 * Class PermissionFactory - is the Factory Method design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/FactoryMethod/README.html
 * @package App\Factory
 */
class PermissionFactory
{
    public function __construct()
    {
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @return Permission
     */
    public function create(string $name, string $description): Permission
    {
        $permission = new Permission();
        $permission->setName($name);
        $permission->setDescription($description);
        $permission->setDateCreated(Carbon::now());

        return $permission;
    }
}
