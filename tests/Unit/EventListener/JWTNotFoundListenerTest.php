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

namespace App\Tests\Unit\EventListener;

use App\EventListener\JWTNotFoundListener;
use App\EventListener\JwtAnAuthorizationTool;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Class JWTNotFoundListenerTest - Unit tests for State JWTNotFoundListener
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\EventListener
 */
class JWTNotFoundListenerTest extends BaseKernelTestCase
{
    /** @var JWTNotFoundListener $listener */
    private JWTNotFoundListener $listener;

    /** @var JwtAnAuthorizationTool $authorizationToolMock */
    private $authorizationToolMock;

    /** @var LoggerInterface $loggerMock */
    private $loggerMock;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        self::markTestSkipped(self::class . ' skipped setUp');
        parent::setUp();
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->authorizationToolMock = $this->createMock(JwtAnAuthorizationTool::class);
        $this->listener = new JWTNotFoundListener(
            $this->authorizationToolMock,
            $this->loggerMock
        );
    }

    /**
     * @testCase - method onJWTNotFound - must be a success
     *
     * Class "App\EventListener\JwtAnAuthorizationTool" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testOnJWTNotFound(): void
    {
        self::markTestSkipped(self::class . ' skipped testOnJWTNotFound');
        $event = $this->createMock(JWTNotFoundEvent::class);
        $this->authorizationToolMock->expects($this->once())
            ->method('forward');

        $this->listener->onJWTNotFound($event);
        $this->assertTrue(method_exists($this->listener, 'debugFunction'));
    }
}
