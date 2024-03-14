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

use App\Entity\Admin;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AdminTest - Unit tests for Entity Admin
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Entity
 */
class AdminTest extends BaseKernelTestCase
{
    /** @var Admin $admin */
    public Admin $admin;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin('username', 'password');
    }

    /**
     * @testCase - function __toString - must be a success
     *
     * @return void
     */
    public function testToString(): void
    {
        $adminString = (string)$this->admin;
        $expected = $this->admin->getUsername();
        $this->assertEquals($expected, $adminString);
    }

    /**
     * @testCase - function eraseCredentials - must be a success
     *
     * @return void
     */
    public function testEraseCredentials(): void
    {
        $this->admin->eraseCredentials();
        $this->assertTrue(true);
    }

    /**
     * @testCase - function getPlainPassword - must be a success
     *
     * @return void
     */
    public function testGetPlainPassword(): void
    {
        $expected = 'password';
        $this->admin->setPlainPassword($expected);
        $this->assertEquals($expected, $this->admin->getPlainPassword());
    }

    /**
     * @testCase - function getRefreshToken - must be a success
     *
     * @return void
     */
    public function testGetRefreshToken(): void
    {
        $expected = 'vujrehbvuenha3456gbhj7kg08920542hdkjvhd';
        $this->admin->setRefreshToken($expected);
        $this->assertEquals($expected, $this->admin->getRefreshToken());
    }
}
