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

use App\Repository\RefreshTokenRepository;
use App\Service\RefreshTokenService;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Class RefreshTokenServiceTest - Unit tests for service RefreshTokenService
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Service
 */
class RefreshTokenServiceTest extends BaseKernelTestCase
{
    /** @var RefreshTokenService $refreshTokenService */
    private RefreshTokenService $refreshTokenService;

    /** @var RefreshTokenRepository|null $repository */
    private ?RefreshTokenRepository $repository;

    /** @var LoggerInterface|null $logger */
    private ?LoggerInterface $logger;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->get(RefreshTokenRepository::class);
        $this->logger = $this->container->get(LoggerInterface::class);
        $this->refreshTokenService = new RefreshTokenService($this->repository, $this->logger);
    }

    /**
     * @testCase - method getJwtRefreshToken - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetJwtRefreshToken(): void
    {
        $username = 'username';
        $refreshToken = $this->createRefreshToken();
        $repositoryMock = $this->getMockBuilder(RefreshTokenRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['username' => $username]))
            ->willReturn($refreshToken);
        $this->refreshTokenService->setRepository($repositoryMock);

        $refreshToken = $this->refreshTokenService->getJwtRefreshToken($username);
        $this->assertInstanceOf(RefreshToken::class, $refreshToken);
    }

    /**
     * @testCase - method getJwtRefreshTokenBy - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetJwtRefreshTokenBy(): void
    {
        $refreshToken = 'refreshToken';
        $refreshTokenObj = $this->createRefreshToken();
        $repositoryMock = $this->getMockBuilder(RefreshTokenRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['refreshToken' => $refreshToken]))
            ->willReturn($refreshTokenObj);
        $this->refreshTokenService->setRepository($repositoryMock);

        $refreshTokenNew = $this->refreshTokenService->getJwtRefreshTokenBy($refreshToken);
        $this->assertInstanceOf(RefreshToken::class, $refreshTokenNew);
    }
}
