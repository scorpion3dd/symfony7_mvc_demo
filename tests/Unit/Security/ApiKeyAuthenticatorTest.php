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

namespace App\Tests\Unit\Security;

use App\Security\ApiKeyAuthenticator;
use App\Tests\Unit\BaseKernelTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Class ApiKeyAuthenticatorTest - Unit tests for Security ApiKeyAuthenticator
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Security
 */
class ApiKeyAuthenticatorTest extends BaseKernelTestCase
{
    public const TOKEN = 'sg57asf4dg4dfg4sa6d74af5';

    /** @var ApiKeyAuthenticator $apiKeyAuthenticator */
    private ApiKeyAuthenticator $apiKeyAuthenticator;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->apiKeyAuthenticator = new ApiKeyAuthenticator();
    }

    /**
     * @testCase - method supports - must be a success, true
     *
     * @return void
     */
    public function testSupportsTrue(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_' . ApiKeyAuthenticator::HEADER_AUTH_TOKEN => self::TOKEN]);
        $result = $this->apiKeyAuthenticator->supports($request);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method supports - must be a success, false
     *
     * @return void
     */
    public function testSupportsFalse(): void
    {
        $request = new Request();
        $result = $this->apiKeyAuthenticator->supports($request);
        $this->assertFalse($result);
    }

    /**
     * @testCase - method authenticate - must be a success, Exception
     *
     * @return void
     */
    public function testAuthenticateException(): void
    {
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Auth token not found (header: "{{ header }}")');
        $request = new Request();
        $this->apiKeyAuthenticator->authenticate($request);
    }

    /**
     * @testCase - method authenticate - must be a success
     *
     * @return void
     */
    public function testAuthenticate(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_' . ApiKeyAuthenticator::HEADER_AUTH_TOKEN => self::TOKEN]);
        $passport = $this->apiKeyAuthenticator->authenticate($request);
        $this->assertInstanceOf(SelfValidatingPassport::class, $passport);
    }

    /**
     * @testCase - method onAuthenticationSuccess - must be a success
     *
     * @return void
     */
    public function testOnAuthenticationSuccess(): void
    {
        $request = new Request();
        $token = $this->createMock(TokenInterface::class);
        $firewallName = 'your_firewall_name';
        $response = $this->apiKeyAuthenticator->onAuthenticationSuccess($request, $token, $firewallName);
        $this->assertNull($response);
    }

    /**
     * @testCase - method onAuthenticationFailure - must be a success
     *
     * @return void
     */
    public function testOnAuthenticationFailure(): void
    {
        $request = new Request();
        $exception = new AuthenticationException('Authentication failed.');
        $this->expectExceptionObject($exception);
        $this->apiKeyAuthenticator->onAuthenticationFailure($request, $exception);
    }
}
