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

use App\Entity\User;
use App\Tests\Unit\BaseKernelTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class UserTest - Unit tests for Entity User
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Entity
 */
class UserTest extends BaseKernelTestCase
{
    /** @var User $user */
    public User $user;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser();
    }

    /**
     * @testCase - function computeSlug - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testComputeSlug(): void
    {
        $this->user->setSlug('-');
        $slugger = $this->container->get(SluggerInterface::class);
        $this->user->computeSlug($slugger);
        $slug = (string)$slugger->slug((string) $this->user)->lower();
        $this->assertEquals($slug, $this->user->getSlug());
    }

    /**
     * @testCase - function getComments - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetComments(): void
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->user->getComments());
    }

    /**
     * @testCase - function getCommentsArray - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetCommentsArray(): void
    {
        $comment1 = $this->createComment($this->user);
        $this->user->addComment($comment1);
        $comment2 = $this->createComment($this->user);
        $this->user->addComment($comment2);
        $this->user->removeComment($comment2);
        $expected = [[
            'author' => $comment1->getAuthor(),
            'text' => $comment1->getText(),
            'email' => $comment1->getEmail()
        ]];
        $this->assertEquals($expected, $this->user->getCommentsArray());
    }

    /**
     * @testCase - function getUserIdentifier - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetUserIdentifier(): void
    {
        $this->assertEquals($this->user->getUsername(), $this->user->getUserIdentifier());
    }

    /**
     * @testCase - function eraseCredentials - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testEraseCredentials(): void
    {
        $this->user->eraseCredentials();
        $this->assertTrue(true);
    }

    /**
     * @testCase - function getGenderList - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetGenderLists(): void
    {
        $expected = [
            User::GENDER_MALE_ID => User::GENDER_MALE,
            User::GENDER_FEMALE_ID => User::GENDER_FEMALE
        ];
        $this->assertEquals($expected, $this->user->getGenderList());
    }

    /**
     * @testCase - function getGenderChoices - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetGenderChoices(): void
    {
        $expected = [
            User::GENDER_MALE => User::GENDER_MALE_ID,
            User::GENDER_FEMALE => User::GENDER_FEMALE_ID
        ];
        $this->assertEquals($expected, $this->user->getGenderChoices());
    }

    /**
     * @testCase - function getAccessChoices - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetAccessChoices(): void
    {
        $expected = [
            User::ACCESS_YES => User::ACCESS_YES_ID,
            User::ACCESS_NO => User::ACCESS_NO_ID
        ];
        $this->assertEquals($expected, $this->user->getAccessChoices());
    }

    /**
     * @testCase - function getStatusChoices - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetStatusChoices(): void
    {
        $expected = [
            User::STATUS_ACTIVE => User::STATUS_ACTIVE_ID,
            User::STATUS_DISACTIVE => User::STATUS_DISACTIVE_ID
        ];
        $this->assertEquals($expected, $this->user->getStatusChoices());
    }

    /**
     * @testCase - function getGenderAsString - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetGenderAsString(): void
    {
        $expected = '';
        $list = User::getGenderList();
        if (isset($list[$this->user->getGender()])) {
            $expected = $list[$this->user->getGender()];
        }
        $gender = $this->user->getGenderAsString();
        $this->assertEquals($expected, $gender);
    }

    /**
     * @testCase - function getGenderAsString - must be a success, Unknown
     *
     * @return void
     * @throws Exception
     */
    public function testGetGenderAsStringUnknown(): void
    {
        $this->user->setGender(100);
        $gender = $this->user->getGenderAsString();
        $this->assertEquals('Unknown', $gender);
    }

    /**
     * @testCase - function getRolesAsString - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetRolesAsString(): void
    {
        $roles = [];
        $role1 = $this->createRole();
        $roles[] = $role1;
        $role2 = $this->createRole();
        $role2->setName($role1->getName() . ' sample');
        $roles[] = $role2;
        $this->user->setRoles($roles);
        $expected = $role1->getName() . ', ' . $role2->getName();
        $roleList = $this->user->getRolesAsString();
        $this->assertEquals($expected, $roleList);
    }

    /**
     * @testCase - function getRolesChoices - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetRolesChoices(): void
    {
        $expected = ['Guest' => 0];
        $this->assertEquals($expected, $this->user->getRolesChoices());
    }

    /**
     * @testCase - function getRolesChoices - must be a success, Role
     *
     * @return void
     * @throws Exception
     */
    public function testGetRolesChoicesRole(): void
    {
        $roles = [];
        $roles[] = $this->createRole();
        $this->user->setRoles($roles);
        $expected = [0 => 'Human'];
        $this->assertEquals($expected, $this->user->getRolesChoices());
    }

    /**
     * @testCase - function getRolesChoicesStatic - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetRolesChoicesStatic(): void
    {
        $expected = ['Guest' => 0];
        $this->assertEquals($expected, $this->user->getRolesChoicesStatic());
    }

    /**
     * @testCase - function getRolesId - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetRolesId(): void
    {
        $rolesId = 11;
        $this->user->setRolesId($rolesId);
        $this->assertEquals($rolesId, $this->user->getRolesId());
    }

    /**
     * @testCase - function getRolesId - must be a success, 0
     *
     * @return void
     * @throws Exception
     */
    public function testGetRolesId0(): void
    {
        $this->assertEquals(0, $this->user->getRolesId());
    }

    /**
     * @testCase - function getRolePermissionsAsString - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetRolePermissionsAsString(): void
    {
        $rolePermissionsArray = [];
        $rolePermissionsArray[] = $this->createRolePermission();
        $rolePermissionsArray[] = $this->createRolePermission();
        $rolePermissions = new ArrayCollection($rolePermissionsArray);
        $this->user->setRolePermissions($rolePermissions);
        $expected = 'Human / Usa, Human / Usa';
        $this->assertEquals($expected, $this->user->getRolePermissionsAsString());
    }

    /**
     * @testCase - function getRolePermissionsArray - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetRolePermissionsArray(): void
    {
        $rolePermissionsArray = [];
        $rolePermissionsArray[] = $this->createRolePermission();
        $rolePermissionsArray[] = $this->createRolePermission();
        $rolePermissions = new ArrayCollection($rolePermissionsArray);
        $this->user->setRolePermissions($rolePermissions);
        $expected = [0 => null, 1 => null];
        $this->assertEquals($expected, $this->user->getRolePermissionsArray());
    }

    /**
     * @testCase - function getRolePermissionsArrayIri - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetRolePermissionsArrayIri(): void
    {
        $rolePermissionsArray = [];
        $rolePermissionsArray[] = $this->createRolePermission()->setId(1);
        $rolePermissionsArray[] = $this->createRolePermission()->setId(2);
        $rolePermissions = new ArrayCollection($rolePermissionsArray);
        $this->user->setRolePermissions($rolePermissions);
        $expected = [
            0 => ['@id' => '/api/role_permissions/1'],
            1 => ['@id' => '/api/role_permissions/2']
        ];
        $this->assertEquals($expected, $this->user->getRolePermissionsArrayIri());
    }
}
