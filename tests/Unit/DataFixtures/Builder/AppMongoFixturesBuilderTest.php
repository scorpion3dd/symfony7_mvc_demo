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

namespace App\Tests\Unit\DataFixtures\Builder;

use App\DataFixtures\Builder\AppMongoFixturesBuilder;
use App\DataFixtures\Builder\BaseFixturesBuilder;
use App\DataFixtures\Builder\Parts\Fixtures;
use App\Factory\LogFactory;
use App\Tests\Unit\DataFixtures\BaseTestAppFixtures;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AppMongoFixturesBuilderTest - Unit tests for AppMongoFixturesBuilder
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\DataFixtures\Builder
 */
class AppMongoFixturesBuilderTest extends BaseTestAppFixtures
{
    /** @var AppMongoFixturesBuilder $builder */
    private AppMongoFixturesBuilder $builder;

    /** @var LogFactory $logFactoryMock */
    private $logFactoryMock;

    /** @var ObjectManager $objectManagerMock */
    private $objectManagerMock;

    /** @var int|null $countLogs */
    private ?int $countLogs = null;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->logFactoryMock = $this->createMock(LogFactory::class);
        $this->objectManagerMock = $this->createMock(ObjectManager::class);
        $this->builder = new AppMongoFixturesBuilder($this->logFactoryMock, $this->loggerMock, $this->countLogs);
    }

    /**
     * @testCase - method build - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testBuild(): void
    {
        $this->objectManagerMock->expects($this->exactly(50))
            ->method('persist');
        $this->objectManagerMock->expects($this->once())
            ->method('flush');

        $fixtures = $this->builder->build($this->objectManagerMock);
        $this->assertInstanceOf(Fixtures::class, $fixtures);
        $this->assertEquals(['Count Logs = ' . BaseFixturesBuilder::COUNT_LOGS . ';'], $fixtures->getElements());
    }
}
