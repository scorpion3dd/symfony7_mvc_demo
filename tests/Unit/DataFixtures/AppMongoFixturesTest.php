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

use App\DataFixtures\AppMongoFixtures;
use App\DataFixtures\Builder\AppMongoFixturesBuilder;
use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use MongoDB\Database;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AppMongoFixturesTest - Unit tests for AppMongoFixtures
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\DataFixtures
 */
class AppMongoFixturesTest extends BaseTestAppFixtures
{
    /** @var AppMongoFixtures $fixtures */
    private AppMongoFixtures $fixtures;

    /** @var AppMongoFixturesBuilder $builderMock */
    private $builderMock;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->builderMock = $this->createMock(AppMongoFixturesBuilder::class);
        $this->fixtures = $this->getMockBuilder(AppMongoFixtures::class)
            ->setConstructorArgs([$this->builderMock, $this->appGlobalsMock, $this->loggerMock])
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
        $connection = $this->createMock(Database::class);
        $connection->expects(self::once())
            ->method('getDatabaseName')
            ->willReturn('test_mongo_db');

        $manager = $this->createMock(DocumentManager::class);
        $manager->expects(self::once())
            ->method('getDocumentDatabase')
            ->willReturn($connection);

        $this->fixtures->expects(self::exactly(3))
            ->method('getIo')
            ->willReturn($this->getSymfonyStyle());

        $this->fixtures->load($manager);
        $this->assertTrue(method_exists($this->fixtures, 'debugFunction'));
    }
}
