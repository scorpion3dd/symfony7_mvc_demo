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

use App\Document\Log;
use App\Repository\LogRepository;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Hydrator\HydratorException;
use Doctrine\ODM\MongoDB\Hydrator\HydratorFactory;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Query;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PHPUnit\Framework\MockObject\Exception;

/**
 * Class LogsRepositoryTest - Unit tests for Repository LogsRepository
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Repository
 */
class LogsRepositoryTest extends KernelTestCase
{
    protected const HYDRATOR_DIR = 'data/DoctrineMongoODMModule/Hydrator';
    protected const HYDRATOR_NS = 'DoctrineMongoODMModule\Hydrator';

    /**
     * @testCase - method findAllLogs - must be a success
     *
     * Error - Class "Doctrine\ODM\MongoDB\Query\Query" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws HydratorException
     * @throws Exception
     */
    public function testFindAllLogs(): void
    {
        self::markTestSkipped(self::class . ' skipped testFindAllLogs');
        $query = $this->createMock(Query::class);
        $queryBuilder = $this->createMock(Builder::class);
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $documentManager = $this->createMock(DocumentManager::class);
        $documentManager->expects($this->once())
            ->method('createQueryBuilder')
            ->with(Log::class)
            ->willReturn($queryBuilder);
        $evm = new EventManager();
        $hydratorFactory = new HydratorFactory($documentManager, $evm, self::HYDRATOR_DIR, self::HYDRATOR_NS, 1);
        $unitOfWork = new UnitOfWork($documentManager, $evm, $hydratorFactory);
        $documentManager->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);

        $logRepository = $this->getMockBuilder(LogRepository::class)
            ->setConstructorArgs([$documentManager])
            ->onlyMethods(['getDocumentManager'])
            ->getMock();
        $logRepository->expects($this->exactly(2))
            ->method('getDocumentManager')
            ->willReturn($documentManager);

        $result = $logRepository->findAllLogs('filterField', 'filterValue');
        $this->assertInstanceOf(Query::class, $result);
    }

    /**
     * @testCase - method deleteAllLogs - must be a success
     *
     * Error - Class "Doctrine\ODM\MongoDB\Query\Query" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws MongoDBException
     * @throws Exception
     */
    public function testDeleteAllLogs(): void
    {
        self::markTestSkipped(self::class . ' skipped testDeleteAllLogs');
        $query = $this->createMock(Query::class);

        $queryBuilder = $this->createMock(Builder::class);
        $queryBuilder->expects($this->once())
            ->method('remove');
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
        $queryBuilder->expects($this->once())
            ->method('execute');

        $documentManager = $this->createMock(DocumentManager::class);
        $documentManager->expects($this->once())
            ->method('createQueryBuilder')
            ->with(Log::class)
            ->willReturn($queryBuilder);
        $evm = new EventManager();
        $hydratorFactory = new HydratorFactory($documentManager, $evm, self::HYDRATOR_DIR, self::HYDRATOR_NS, 1);
        $unitOfWork = new UnitOfWork($documentManager, $evm, $hydratorFactory);
        $documentManager->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);

        $logRepository = $this->getMockBuilder(LogRepository::class)
            ->setConstructorArgs([$documentManager])
            ->onlyMethods(['getDocumentManager'])
            ->getMock();
        $logRepository->expects($this->exactly(2))
            ->method('getDocumentManager')
            ->willReturn($documentManager);

        $logRepository->deleteAllLogs();
        $this->assertTrue(true);
    }

    /**
     * @testCase - method save - must be a success
     *
     * @return void
     * @throws MongoDBException
     * @throws Exception
     */
    public function testSave(): void
    {
        $documentManager = $this->createMock(DocumentManager::class);
        $evm = new EventManager();
        $hydratorFactory = new HydratorFactory($documentManager, $evm, self::HYDRATOR_DIR, self::HYDRATOR_NS, 1);
        $unitOfWork = new UnitOfWork($documentManager, $evm, $hydratorFactory);
        $documentManager->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);
        $documentManager->expects($this->once())
            ->method('persist');
        $documentManager->expects($this->once())
            ->method('flush');

        $log = new Log();
        $logRepository = new LogRepository($documentManager);
        $logRepository->save($log, true);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method remove - must be a success
     *
     * @return void
     * @throws MongoDBException
     * @throws Exception
     */
    public function testRemove(): void
    {
        $documentManager = $this->createMock(DocumentManager::class);
        $evm = new EventManager();
        $hydratorFactory = new HydratorFactory($documentManager, $evm, self::HYDRATOR_DIR, self::HYDRATOR_NS, 1);
        $unitOfWork = new UnitOfWork($documentManager, $evm, $hydratorFactory);
        $documentManager->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);
        $documentManager->expects($this->once())
            ->method('remove');
        $documentManager->expects($this->once())
            ->method('flush');

        $log = new Log();
        $logRepository = new LogRepository($documentManager);
        $logRepository->remove($log, true);
        $this->assertTrue(true);
    }
}
