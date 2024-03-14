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

namespace App\Tests\Unit\DataFixtures;

use App\DataFixtures\AppFixtures;
use App\DataFixtures\Builder\AppMainFixturesBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AppFixturesTest - Unit tests for AppFixtures
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\DataFixtures
 */
class AppFixturesTest extends BaseTestAppFixtures
{
    /** @var AppFixtures $fixtures */
    private AppFixtures $fixtures;

    /** @var AppMainFixturesBuilder $builderMock */
    private $builderMock;

    /** @var KernelInterface $kernelMock */
    protected $kernelMock;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->kernelMock = $this->createMock(KernelInterface::class);
        $this->builderMock = $this->createMock(AppMainFixturesBuilder::class);
        $this->fixtures = $this->getMockBuilder(AppFixtures::class)
            ->setConstructorArgs([$this->builderMock, $this->appGlobalsMock, $this->loggerMock, $this->kernelMock])
            ->onlyMethods(['getIo'])
            ->getMock();
    }

    /**
     * @testCase - method load - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testLoad(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('getDatabase')
            ->willReturn('test_db');

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->expects(self::once())
            ->method('getConnection')
            ->willReturn($connection);

        $this->fixtures->expects(self::exactly(3))
            ->method('getIo')
            ->willReturn($this->getSymfonyStyle());

        $this->fixtures->load($manager);
        $this->assertTrue(method_exists($this->fixtures, 'debugFunction'));
    }
}
