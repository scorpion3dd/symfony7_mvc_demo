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

use App\Entity\Admin;
use App\Enum\Roles;

/**
 * Class AdminFactory - is the Factory Method design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/FactoryMethod/README.html
 * @package App\Factory
 */
class AdminFactory
{
    public function __construct()
    {
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return Admin
     */
    public function create(
        string $username,
        string $password,
    ): Admin {
        $admin = new Admin();
        $admin->setRoles([Roles::ROLE_ADMIN]);
        $admin->setUsername($username);
        $admin->setPassword($password);

        return $admin;
    }

    /**
     * @return Admin
     */
    public function createEmpty(): Admin
    {
        return new Admin();
    }
}
