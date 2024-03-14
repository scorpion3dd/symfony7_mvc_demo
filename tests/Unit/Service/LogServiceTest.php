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

namespace App\Tests\Unit\Service;

use App\Document\Log;
use App\Repository\LogRepository;
use App\Repository\LogRepositoryInterface;
use App\Service\LogService;
use App\Tests\Unit\BaseKernelTestCase;
use DateTime;
use Doctrine\ORM\AbstractQuery;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Class LogServiceTest - Unit tests for service LogService
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Service
 */
class LogServiceTest extends BaseKernelTestCase
{
    /** @var LogService $logService */
    private LogService $logService;

    /** @var LogRepositoryInterface|null $repository */
    private ?LogRepositoryInterface $repository;

    /** @var LoggerInterface|null $logger */
    private ?LoggerInterface $logger;

    /** @var PaginatorInterface|null $paginator */
    private ?PaginatorInterface $paginator;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->get(LogRepositoryInterface::class);
        $this->paginator = $this->container->get(PaginatorInterface::class);
        $this->logger = $this->container->get(LoggerInterface::class);
        $this->logService = new LogService(
            $this->paginator,
            $this->repository,
            $this->logger
        );
    }

    /**
     * @testCase - method find - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testFind(): void
    {
        $page = 1;
        $queryMock = $this->createMock(AbstractQuery::class);
        $repositoryMock = $this->getMockBuilder(LogRepository::class)
            ->onlyMethods(['findAllLogs'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findAllLogs')
            ->willReturn($queryMock);
        $this->logService->setRepository($repositoryMock);

        $paginatorMock = $this->getMockBuilder(PaginatorInterface::class)
            ->onlyMethods(['paginate'])
            ->disableOriginalConstructor()
            ->getMock();
        $paginatorMock->expects($this->exactly(1))
            ->method('paginate')
            ->with(
                $this->equalTo($queryMock),
                $this->equalTo($page),
                $this->equalTo(LogService::PAGINATOR_PER_PAGE),
            );
        $this->logService->setPaginator($paginatorMock);

        $paginate = $this->logService->getLogsPaginator($page);
        $this->assertIsObject($paginate);
    }

    /**
     * @testCase - method getLog - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetLog(): void
    {
        $id = '658b2401e5051814f803c7f3';
        $log = $this->createLog();
        $repositoryMock = $this->getMockBuilder(LogRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->willReturn($log);
        $this->logService->setRepository($repositoryMock);

        $logNew = $this->logService->getLog($id);
        $this->assertInstanceOf(Log::class, $logNew);
    }

    /**
     * @testCase - method editLog - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testEditLog(): void
    {
        $logDbId = '658b2401e5051814f803c7f3';
        $log = $this->createLog();
        $logDbTimestamp = new DateTime();
        $logNew = $this->logService->editLog($log, $logDbId, $logDbTimestamp);
        $this->assertInstanceOf(Log::class, $logNew);
    }
}
