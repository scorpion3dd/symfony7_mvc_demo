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

use PHPUnit\Framework\MockObject\Exception;
use ApiPlatform\Metadata\Post;
use App\Entity\Admin;
use App\Factory\AdminFactory;
use App\Helper\JwtTokensHelper;
use App\Service\AdminServiceInterface;
use App\Service\RefreshTokenServiceInterface;
use App\State\AdminRefreshTokenProcessor;
use App\Tests\Unit\BaseKernelTestCase;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class AdminRefreshTokenProcessorTest - Unit tests for State AdminRefreshTokenProcessor
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\State
 */
class AdminRefreshTokenProcessorTest extends BaseKernelTestCase
{
    /** @var AdminServiceInterface $adminServiceMock */
    private AdminServiceInterface $adminServiceMock;

    /** @var RefreshTokenServiceInterface $refreshTokenServiceMock */
    private RefreshTokenServiceInterface $refreshTokenServiceMock;

    /** @var JwtTokensHelper $jwtTokensHelperMock */
    private JwtTokensHelper $jwtTokensHelperMock;

    /** @var LoggerInterface $loggerMock */
    private LoggerInterface $loggerMock;

    /** @var RequestStack $requestStackMock */
    private RequestStack $requestStackMock;

    /** @var AdminRefreshTokenProcessor $processor */
    private AdminRefreshTokenProcessor $processor;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->adminServiceMock = $this->createMock(AdminServiceInterface::class);
        $this->adminFactory = $this->createMock(AdminFactory::class);
        $this->requestStackMock = $this->createMock(RequestStack::class);
        $this->jwtTokensHelperMock = $this->createMock(JwtTokensHelper::class);
        $this->refreshTokenServiceMock = $this->createMock(RefreshTokenServiceInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->processor = new AdminRefreshTokenProcessor(
            $this->adminServiceMock,
            $this->refreshTokenServiceMock,
            $this->adminFactory,
            $this->jwtTokensHelperMock,
            $this->requestStackMock,
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
        $content = '{"refreshToken":"' . self::TOKEN . '"}';
        $currentRequest = Request::create('/', 'POST', [], [], [], [], $content);
        $this->requestStackMock->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($currentRequest);

        $jwtRefreshToken = $this->createMock(RefreshToken::class);
        $jwtRefreshToken->setUsername($this->admin->getUsername());
        $this->refreshTokenServiceMock->expects($this->once())
            ->method('getJwtRefreshTokenBy')
            ->with(self::TOKEN)
            ->willReturn($jwtRefreshToken);

        $jwtRefreshToken->setUsername($this->admin->getUsername());
        $this->adminServiceMock->expects($this->once())
            ->method('findOneByLogin')
            ->with($jwtRefreshToken->getUsername())
            ->willReturn($this->admin);

        $token = 'dfgs87afas';
        $this->jwtTokensHelperMock->expects($this->once())
            ->method('createJwtToken')
            ->with($this->admin)
            ->willReturn($token);

        $this->adminServiceMock->expects($this->once())
            ->method('save')
            ->with($this->admin, true);

        $response = $this->processor->process($this->admin, new Post());
        $this->assertInstanceOf(Admin::class, $response);
        $this->assertEquals($this->admin, $response);
    }

    /**
     * @testCase - method process - must be a success, Post operation
     * findOneByLogin - Admin Null
     *
     * @return void
     * @throws Exception
     */
    public function testProcessAdminNull(): void
    {
        $content = '{"refreshToken":"' . self::TOKEN . '"}';
        $currentRequest = Request::create('/', 'POST', [], [], [], [], $content);
        $this->requestStackMock->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($currentRequest);

        $jwtRefreshToken = $this->createMock(RefreshToken::class);
        $jwtRefreshToken->setUsername($this->admin->getUsername());
        $this->refreshTokenServiceMock->expects($this->once())
            ->method('getJwtRefreshTokenBy')
            ->with(self::TOKEN)
            ->willReturn($jwtRefreshToken);

        $jwtRefreshToken->setUsername($this->admin->getUsername());
        $this->adminServiceMock->expects($this->once())
            ->method('findOneByLogin')
            ->with($jwtRefreshToken->getUsername())
            ->willReturn(null);

        $response = $this->processor->process($this->admin, new Post());
        $this->assertInstanceOf(Admin::class, $response);
        $this->assertEquals($this->admin, $response);
    }

    /**
     * @testCase - method process - must be a success, Exception
     *
     * @return void
     */
    public function testProcessException(): void
    {
        $content = '{"refreshToken""' . self::TOKEN . '"}';
        $currentRequest = Request::create('/', 'POST', [], [], [], [], $content);
        $this->requestStackMock->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($currentRequest);

        $response = $this->processor->process($this->admin, new Post());
        $this->assertInstanceOf(Admin::class, $response);
        $this->assertEquals($this->admin, $response);
    }

    /**
     * @testCase - method process - must be a success, BadRequestHttpException
     *
     * @return void
     */
    public function testProcessBadRequestHttpException(): void
    {
        $response = $this->processor->process($this->admin, new Post());
        $this->assertInstanceOf(Admin::class, $response);
        $this->assertEquals($this->admin, $response);
    }
}
