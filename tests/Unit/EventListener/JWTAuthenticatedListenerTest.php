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

use App\EventListener\JWTAuthenticatedListener;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Class JWTAuthenticatedListenerTest - Unit tests for State JWTAuthenticatedListener
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\EventListener
 */
class JWTAuthenticatedListenerTest extends BaseKernelTestCase
{
    /** @var JWTAuthenticatedListener $listener */
    private JWTAuthenticatedListener $listener;

    /** @var LoggerInterface $loggerMock */
    private $loggerMock;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->listener = new JWTAuthenticatedListener(
            $this->loggerMock
        );
    }

    /**
     * @testCase - method onJWTAuthenticated - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testOnJWTAuthenticated(): void
    {
        $admin = $this->createAdmin('username1', 'password1');
        $admin->setToken(self::AUTH_TOKEN);

        $token = $this->createMock(JWTPostAuthenticationToken::class);
        $token->expects($this->once())
            ->method('getCredentials')
            ->willReturn(self::TOKEN);
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($admin);

        $event = $this->createMock(JWTAuthenticatedEvent::class);
        $event->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid JWT Token - no such token exists');
        $this->listener->onJWTAuthenticated($event);
    }
}
