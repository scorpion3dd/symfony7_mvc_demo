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

namespace App\Tests\Unit\State;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Helper\JwtTokensHelper;
use App\Service\RefreshTokenServiceInterface;
use App\State\UserPasswordHasherProcessor;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserPasswordHasherProcessorTest - Unit tests for State UserPasswordHasherProcessor
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\State
 */
class UserPasswordHasherProcessorTest extends BaseKernelTestCase
{
    /** @var ProcessorInterface $processorMock */
    private ProcessorInterface $processorMock;

    /** @var UserPasswordHasherInterface $passwordHasherMock */
    private UserPasswordHasherInterface $passwordHasherMock;

    /** @var RefreshTokenServiceInterface $refreshTokenServiceMock */
    private RefreshTokenServiceInterface $refreshTokenServiceMock;

    /** @var JwtTokensHelper $jwtTokensHelperMock */
    private JwtTokensHelper $jwtTokensHelperMock;

    /** @var LoggerInterface $loggerMock */
    private LoggerInterface $loggerMock;

    /** @var UserPasswordHasherProcessor $processor */
    private UserPasswordHasherProcessor $processor;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->processorMock = $this->createMock(ProcessorInterface::class);
        $this->passwordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $this->jwtTokensHelperMock = $this->createMock(JwtTokensHelper::class);
        $this->refreshTokenServiceMock = $this->createMock(RefreshTokenServiceInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->processor = new UserPasswordHasherProcessor(
            $this->processorMock,
            $this->passwordHasherMock,
            $this->refreshTokenServiceMock,
            $this->jwtTokensHelperMock,
            $this->loggerMock
        );
        $adminId = 1;
        $username = 'admin';
        $password = 'password';
        $this->admin = $this->createAdmin($username, $password);
        $this->admin->setId($adminId);
        $this->admin->setPlainPassword($password);
    }

    /**
     * @testCase - method process - must be a success, Post operation
     *
     * @return void
     * @throws Exception
     */
    public function testProcess(): void
    {
        $token = 'dfgs87afas';
        $this->jwtTokensHelperMock->expects($this->once())
            ->method('createJwtToken')
            ->with($this->admin)
            ->willReturn($token);

        $refreshToken = $this->createMock(RefreshToken::class);
        $refreshToken->setUsername($this->admin->getUsername());
        $refreshToken->setRefreshToken(self::TOKEN);
        $this->jwtTokensHelperMock->expects($this->once())
            ->method('createJwtRefreshToken')
            ->with($this->admin)
            ->willReturn($refreshToken);

        $this->refreshTokenServiceMock->expects($this->once())
            ->method('save')
            ->with($refreshToken, true);

        $response = $this->processor->process($this->admin, new Post());
        $this->assertNull($response);
    }

    /**
     * @testCase - method process - must be a success, Post operation
     * PlainPassword Null
     *
     * @return void
     * @throws Exception
     */
    public function testProcessPlainPasswordNull(): void
    {
        $this->admin->setPlainPassword(null);
        $response = $this->processor->process($this->admin, new Post());
        $this->assertNull($response);
    }
}
