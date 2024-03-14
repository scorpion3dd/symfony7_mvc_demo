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

use App\Security\AppAuthenticator;
use App\Tests\Unit\BaseKernelTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

/**
 * Class AppAuthenticatorTest - Unit tests for Security AppAuthenticator
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Security
 */
class AppAuthenticatorTest extends BaseKernelTestCase
{
    /** @var AppAuthenticator $appAuthenticator */
    private AppAuthenticator $appAuthenticator;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $urlGenerator = $this->container->get(UrlGeneratorInterface::class);
        $this->appAuthenticator = new AppAuthenticator($urlGenerator);
    }

    /**
     * @testCase - method authenticate - must be a success
     *
     * @return void
     */
    public function testAuthenticate(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $request = new Request([], ['username' => 'username']);
        $request->setSession($session);
        $passport = $this->appAuthenticator->authenticate($request);
        $this->assertInstanceOf(Passport::class, $passport);
    }

    /**
     * @testCase - method onAuthenticationSuccess - must be a success
     *
     * @return void
     */
    public function testOnAuthenticationSuccess(): void
    {
        $firewallName = 'your_firewall_name';
        $locale = 'en';
        $path = '/' . $locale . '/admin';
        $session = new Session(new MockArraySessionStorage());
        $request = new Request();
        $request->setSession($session);
        $request->setLocale($locale);
        $token = $this->createMock(TokenInterface::class);
        $response = $this->appAuthenticator->onAuthenticationSuccess($request, $token, $firewallName);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($path, $response->getTargetUrl());
    }

    /**
     * @testCase - method onAuthenticationSuccess - must be a success, TargetPath
     *
     * @return void
     */
    public function testOnAuthenticationSuccessTargetPath(): void
    {
        $firewallName = 'main';
        $locale = 'en';
        $path = '/' . $locale . '/admin';
        $request = new Request();
        $request->setLocale($locale);
        $session = new Session(new MockArraySessionStorage());
        $session->set('_security.' . $firewallName . '.target_path', $path);
        $request->setSession($session);
        $token = $this->createMock(TokenInterface::class);
        $response = $this->appAuthenticator->onAuthenticationSuccess($request, $token, $firewallName);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($path, $response->getTargetUrl());
    }
}
