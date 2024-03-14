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

use App\EventListener\AuthenticationFailureListener;
use App\EventListener\JwtAnAuthorizationTool;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AuthenticationFailureListenerTest - Unit tests for State AuthenticationFailureListener
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\EventListener
 */
class AuthenticationFailureListenerTest extends BaseKernelTestCase
{
    /** @var AuthenticationFailureListener $listener */
    private AuthenticationFailureListener $listener;

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
        $this->listener = new AuthenticationFailureListener(
            $this->authorizationToolMock,
            $this->loggerMock
        );
    }

    /**
     * @testCase - method onAuthenticationFailureResponse - must be a success
     *
     * Class "App\EventListener\JwtAnAuthorizationTool" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testOnAuthenticationFailureResponse(): void
    {
        self::markTestSkipped(self::class . ' skipped testOnAuthenticationFailureResponse');
        $event = $this->createMock(AuthenticationFailureEvent::class);
        $this->authorizationToolMock->expects($this->once())
            ->method('forward');

        $this->listener->onAuthenticationFailureResponse($event);
        $this->assertTrue(method_exists($this->listener, 'debugFunction'));
    }
}
