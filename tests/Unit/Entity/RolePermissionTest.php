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

namespace App\Tests\Unit\Entity;

use App\Entity\RolePermission;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class RolePermissionTest - Unit tests for Entity RolePermission
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Entity
 */
class RolePermissionTest extends BaseKernelTestCase
{
    /** @var RolePermission $rolePermission */
    public RolePermission $rolePermission;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->rolePermission = $this->createRolePermission();
    }

    /**
     * @testCase - function __toString - must be a success
     *
     * @return void
     */
    public function testToString(): void
    {
        $rolePermissionString = (string)$this->rolePermission;
        $expected = 'Human / Usa';
        $this->assertEquals($expected, $rolePermissionString);
    }

    /**
     * @testCase - function __toString - must be a success, /
     *
     * @return void
     */
    public function testToString2(): void
    {
        $role = $this->createRole();
        $role->setId(1);
        $rolePermission = new RolePermission();
        $rolePermission->setRole($role);
        $rolePermissionString = (string)$rolePermission;
        $expected = ' / ';
        $this->assertEquals($expected, $rolePermissionString);
    }
}
