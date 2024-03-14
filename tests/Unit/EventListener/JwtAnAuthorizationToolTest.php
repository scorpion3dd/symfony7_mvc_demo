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

use App\EventListener\JwtAnAuthorizationTool;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class JwtAnAuthorizationToolTest - Unit tests for State JwtAnAuthorizationTool
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\EventListener
 */
class JwtAnAuthorizationToolTest extends BaseKernelTestCase
{
    /** @var JwtAnAuthorizationTool $listener */
    private JwtAnAuthorizationTool $listener;

    /** @var RequestStack $requestStackMock */
    private $requestStackMock;

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
        $this->requestStackMock = $this->createMock(RequestStack::class);
        $this->listener = new JwtAnAuthorizationTool(
            $this->requestStackMock,
            $this->loggerMock
        );
    }

    /**
     * @testCase - method forward - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testForward(): void
    {
        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag();
        $request->attributes->set('_route', 'api-check-exist-email');

        $this->requestStackMock->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $response = $this->listener->forward();
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
