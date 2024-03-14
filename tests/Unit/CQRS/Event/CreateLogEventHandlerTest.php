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

namespace App\Tests\Unit\CQRS\Event;

use App\CQRS\Event\CreateLogEvent;
use App\Factory\LogFactory;
use App\Helper\ApplicationGlobals;
use App\CQRS\Event\CreateLogEventHandler;
use App\Service\LogServiceInterface;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CreateLogEventHandlerTest - Unit tests for State CreateLogEventHandler
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\CQRS\Event
 */
class CreateLogEventHandlerTest extends BaseKernelTestCase
{
    /** @var CreateLogEventHandler $handler */
    private CreateLogEventHandler $handler;

    /** @var LogServiceInterface $logServiceMock */
    private LogServiceInterface $logServiceMock;

    /** @var LogFactory $logFactoryMock */
    private LogFactory $logFactoryMock;

    /** @var LoggerInterface $loggerMock */
    private LoggerInterface $loggerMock;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->appGlobals->setType(ApplicationGlobals::TYPE_APP_TESTS_HIDE);
        $this->logServiceMock = $this->createMock(LogServiceInterface::class);
        $this->logFactoryMock = $this->createMock(LogFactory::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->handler = new CreateLogEventHandler(
            $this->logServiceMock,
            $this->logFactoryMock,
            $this->appGlobals,
            $this->loggerMock
        );
    }

    /**
     * @testCase - method invoke - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $log = $this->createLog();
        $log->setId('65ca40763e0da355c00b06d0');
        $createLogEvent = new CreateLogEvent($log->getId(), $log->getExtra(), $log->getMessage(), $log->getTimestamp());

        $this->logFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($log);

        $this->logServiceMock->expects($this->once())
            ->method('save')
            ->with($log, true);

        $this->handler->__invoke($createLogEvent);
        $this->assertTrue(method_exists($this->handler, 'debugFunction'));
    }
}
