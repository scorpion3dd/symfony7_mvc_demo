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

namespace App\Tests\Unit\Helper;

use App\Helper\JwtTokensHelper;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class JwtTokensHelperTest - Unit tests for helper JwtTokensHelper
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Helper
 */
class JwtTokensHelperTest extends BaseKernelTestCase
{
    protected const REFRESH_TOKEN = 'b4f0001b2be7cc77f5f67658584ea169f840873526819663d6c36df3b95296918bd5194d4e86124cf4b8f3b80b450ef34863e4863a38abb36d68ba244cd2f6c7';
    protected const REFRESH_TOKEN_CLASS = 'Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken';

    /** @var JwtTokensHelper $jwtTokensHelper */
    public JwtTokensHelper $jwtTokensHelper;

    /** @var JWTTokenManagerInterface $jwtManager */
    public JWTTokenManagerInterface $jwtManager;

    /** @var RefreshTokenGeneratorInterface $refreshJwtManager */
    public RefreshTokenGeneratorInterface $refreshJwtManager;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->jwtManager = $this->container->get(JWTTokenManagerInterface::class);
        $this->refreshJwtManager = $this->container->get(RefreshTokenGeneratorInterface::class);
        $this->jwtTokensHelper = new JwtTokensHelper($this->jwtManager, $this->refreshJwtManager);
    }

    /**
     * @testCase - method createJwtToken - must be a success
     *
     * @return void
     */
    public function testCreateJwtToken(): void
    {
        $admin = $this->adminFactory->createEmpty();
        $JwtToken = $this->jwtTokensHelper->createJwtToken($admin);
        $prefixJwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.';
        $this->assertStringStartsWith($prefixJwtToken, $JwtToken);
    }

    /**
     * @testCase - method createJwtRefreshToken - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testCreateJwtRefreshToken(): void
    {
        $refreshJwtManager = $this->createMock(RefreshTokenGeneratorInterface::class);
        $class = self::REFRESH_TOKEN_CLASS;
        $refreshTokenClass = new $class();
        $refreshTokenClass->setRefreshToken(self::REFRESH_TOKEN);
        $refreshJwtManager->expects(self::once())->method('createForUserWithTtl')->willReturn($refreshTokenClass);
        $this->jwtTokensHelper->setRefreshJwtManager($refreshJwtManager);

        $admin = $this->createAdmin('username', 'password');
        $refreshToken = $this->jwtTokensHelper->createJwtRefreshToken($admin);
        $this->assertInstanceOf(RefreshToken::class, $refreshToken);
        $this->assertIsString($refreshToken->getRefreshToken());
        $this->assertGreaterThan(10, strlen($refreshToken->getRefreshToken()));
    }

    /**
     * @testCase - method updateJwtRefreshToken - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testUpdateJwtRefreshToken(): void
    {
        $refreshJwtManager = $this->createMock(RefreshTokenGeneratorInterface::class);
        $class = self::REFRESH_TOKEN_CLASS;
        $refreshTokenClass = new $class();
        $refreshTokenClass->setRefreshToken(self::REFRESH_TOKEN);
        $refreshJwtManager->expects(self::once())->method('createForUserWithTtl')->willReturn($refreshTokenClass);
        $this->jwtTokensHelper->setRefreshJwtManager($refreshJwtManager);

        $admin = $this->createAdmin('username', 'password');
        $refreshToken = $this->jwtTokensHelper->updateJwtRefreshToken($admin);
        $this->assertInstanceOf(RefreshToken::class, $refreshToken);
        $this->assertIsString($refreshToken->getRefreshToken());
        $this->assertGreaterThan(10, strlen($refreshToken->getRefreshToken()));
    }
}
