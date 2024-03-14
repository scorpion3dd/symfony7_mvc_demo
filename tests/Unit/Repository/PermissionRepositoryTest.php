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

namespace App\Tests\Unit\Repository;

use App\Repository\PermissionRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class PermissionRepositoryTest - Unit tests for Repository PermissionRepository
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Repository
 */
class PermissionRepositoryTest extends KernelTestCase
{
    /**
     * @testCase - method __construct - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $roleRepository = new PermissionRepository($registry);
        $this->assertInstanceOf(PermissionRepository::class, $roleRepository);
    }
}
