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

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use App\Entity\Admin;
use App\Helper\JwtTokensHelper;
use App\Service\AdminServiceInterface;
use App\Service\RefreshTokenServiceInterface;
use App\State\AdminLoginProcessor;
use App\Tests\Unit\BaseKernelTestCase;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AdminLoginProcessorTest - Unit tests for State AdminLoginProcessor
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\State
 */
class AdminLoginProcessorTest extends BaseKernelTestCase
{
    /** @var AdminServiceInterface $adminServiceMock */
    private AdminServiceInterface $adminServiceMock;

    /** @var UserPasswordHasherInterface $userPasswordEncoderMock */
    private UserPasswordHasherInterface $userPasswordEncoderMock;

    /** @var RefreshTokenServiceInterface $refreshTokenServiceMock */
    private RefreshTokenServiceInterface $refreshTokenServiceMock;

    /** @var JwtTokensHelper $jwtTokensHelperMock */
    private JwtTokensHelper $jwtTokensHelperMock;

    /** @var LoggerInterface $loggerMock */
    private LoggerInterface $loggerMock;

    /** @var AdminLoginProcessor $processor */
    private AdminLoginProcessor $processor;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->adminServiceMock = $this->createMock(AdminServiceInterface::class);
        $this->userPasswordEncoderMock = $this->createMock(UserPasswordHasherInterface::class);
        $this->jwtTokensHelperMock = $this->createMock(JwtTokensHelper::class);
        $this->refreshTokenServiceMock = $this->createMock(RefreshTokenServiceInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->processor = new AdminLoginProcessor(
            $this->adminServiceMock,
            $this->userPasswordEncoderMock,
            $this->jwtTokensHelperMock,
            $this->refreshTokenServiceMock,
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
     * @throws NonUniqueResultException
     */
    public function testProcess(): void
    {
        $this->adminServiceMock->expects($this->once())
            ->method('findOneByLogin')
            ->with($this->admin->getUsername())
            ->willReturn($this->admin);

        $this->userPasswordEncoderMock->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(true);

        $response = $this->processor->process($this->admin, new Post());
        $this->assertInstanceOf(Admin::class, $response);
        $this->assertEquals($this->admin, $response);
    }

    /**
     * @testCase - method process - must be a success, NotFoundHttpException
     * Operation not Post
     *
     * @return void
     * @throws NonUniqueResultException
     */
    public function testProcessWithNonPostOperation(): void
    {
        $operationMock = $this->createMock(Operation::class);
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Operation not Post.');
        $this->processor->process($this->admin, $operationMock);
    }

    /**
     * @testCase - method process - must be a success, NotFoundHttpException
     * Not found Admin
     *
     * @return void
     * @throws NonUniqueResultException
     */
    public function testProcessWithNonAdminOperation(): void
    {
        $this->adminServiceMock->expects($this->once())
            ->method('findOneByLogin')
            ->with($this->admin->getUsername())
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Not found Admin.');
        $this->processor->process($this->admin, new Post());
    }

    /**
     * @testCase - method process - must be a success, NotFoundHttpException
     * RequestUri not ApiAdminsItemProcess
     *
     * @return void
     * @throws NonUniqueResultException
     */
    public function testProcessWithNonRequestUriOperation(): void
    {
        $this->adminServiceMock->expects($this->once())
            ->method('findOneByLogin')
            ->with($this->admin->getUsername())
            ->willReturn($this->admin);

        $this->userPasswordEncoderMock->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(false);

        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('');
        $this->processor->process($this->admin, new Post());
    }
}
