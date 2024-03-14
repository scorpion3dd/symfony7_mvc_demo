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

use App\Entity\Permission;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class PermissionTest - Unit tests for Entity Permission
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Entity
 */
class PermissionTest extends BaseKernelTestCase
{
    /** @var Permission $permission */
    public Permission $permission;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->permission = $this->createPermission();
    }

    /**
     * @testCase - function __toString - must be a success
     *
     * @return void
     */
    public function testToString(): void
    {
        $permissionString = (string)$this->permission;
        $expected = 'Usa';
        $this->assertEquals($expected, $permissionString);
    }

    /**
     * @testCase - function getRolesArray - must be a success
     *
     * @return void
     */
    public function testGetRolesArray(): void
    {
        $role = $this->createRole();
        $role->setId(1);
        $this->permission->addRole($role);
        $expected = [0 => 1];
        $this->assertEquals($expected, $this->permission->getRolesArray());
    }

    /**
     * @testCase - function getRolesArrayIri - must be a success
     *
     * @return void
     */
    public function testGetRolesArrayIri(): void
    {
        $role = $this->createRole();
        $role->setId(1);
        $this->permission->addRole($role);
        $expected = [0 => '/api/roles/1'];
        $this->assertEquals($expected, $this->permission->getRolesArrayIri());
    }

    /**
     * @testCase - function removeRole - must be a success
     *
     * @return void
     */
    public function testRemoveRole(): void
    {
        $role = $this->createRole();
        $role->setId(1);
        $this->permission->addRole($role);
        $this->assertEquals($this->permission, $this->permission->removeRole($role));
    }
}
