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

use App\Entity\Role;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class RoleTest - Unit tests for Entity Role
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Entity
 */
class RoleTest extends BaseKernelTestCase
{
    /** @var Role $role */
    public Role $role;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->role = $this->createRole();
    }

    /**
     * @testCase - function __toString - must be a success
     *
     * @return void
     */
    public function testToString(): void
    {
        $roleString = (string)$this->role;
        $expected = 'Human';
        $this->assertEquals($expected, $roleString);
    }

    /**
     * @testCase - function getPermissionsArray - must be a success
     *
     * @return void
     */
    public function testGetPermissionsArray(): void
    {
        $permission = $this->createPermission();
        $permission->setId(1);
        $this->role->setPermissions($permission);
        $expected = [0 => 1];
        $this->assertEquals($expected, $this->role->getPermissionsArray());
    }

    /**
     * @testCase - function getPermissionsArrayIri - must be a success
     *
     * @return void
     */
    public function testGetPermissionsArrayIri(): void
    {
        $permission = $this->createPermission();
        $permission->setId(1);
        $this->role->setPermissions($permission);
        $expected = [0 => '/api/permissions/1'];
        $this->assertEquals($expected, $this->role->getPermissionsArrayIri());
    }

    /**
     * @testCase - function removePermission - must be a success
     *
     * @return void
     */
    public function testRemovePermission(): void
    {
        $permission = $this->createPermission();
        $permission->setId(1);
        $this->role->setPermissions($permission);
        $this->assertEquals($this->role, $this->role->removePermission($permission));
    }

    /**
     * @testCase - function addParent - must be a success, false
     *
     * @return void
     */
    public function testAddParentFalse(): void
    {
        $this->role->setId(1);
        $this->assertFalse($this->role->addParent($this->role));
    }

    /**
     * @testCase - function clearParentRoles - must be a success, false
     *
     * @return void
     */
    public function testClearParentRoles(): void
    {
        $this->role->clearParentRoles();
        $this->assertTrue(true);
    }

    /**
     * @testCase - function addChild - must be a success, false
     *
     * @return void
     */
    public function testAddChildFalse(): void
    {
        $this->role->setId(1);
        $this->assertFalse($this->role->addChild($this->role));
    }
}
