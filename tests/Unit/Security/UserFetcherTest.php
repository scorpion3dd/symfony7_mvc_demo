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

use App\Entity\Admin;
use App\Entity\User;
use App\Helper\JwtTokensHelper;
use App\Security\UserFetcher;
use App\Service\AdminServiceInterface;
use App\Service\RefreshTokenServiceInterface;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Class UserFetcherTest - Unit tests for Security UserFetcher
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Security
 */
class UserFetcherTest extends BaseKernelTestCase
{
    /** @var UserFetcher $userFetcher */
    private UserFetcher $userFetcher;

    /** @var Security $securityMock */
    private Security $securityMock;

    /** @var AdminServiceInterface $adminServiceMock */
    private AdminServiceInterface $adminServiceMock;

    /** @var JwtTokensHelper $jwtTokensHelperMock */
    private JwtTokensHelper $jwtTokensHelperMock;

    /** @var RefreshTokenServiceInterface $refreshTokenServiceMock */
    private RefreshTokenServiceInterface $refreshTokenServiceMock;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->securityMock = $this->createMock(Security::class);
        $this->adminServiceMock = $this->createMock(AdminServiceInterface::class);
        $this->jwtTokensHelperMock = $this->createMock(JwtTokensHelper::class);
        $this->refreshTokenServiceMock = $this->createMock(RefreshTokenServiceInterface::class);
        $this->userFetcher = new UserFetcher(
            $this->securityMock,
            $this->adminServiceMock,
            $this->jwtTokensHelperMock,
            $this->refreshTokenServiceMock
        );
    }

    /**
     * @testCase - method getAuthUser - must be a success
     *
     * @return void
     */
    public function testGetAuthUser(): void
    {
        $user = new User();
        $this->securityMock->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        $result = $this->userFetcher->getAuthUser();
        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($user, $result);
    }

    /**
     * @testCase - method logout - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testLogoutAdmin(): void
    {
        $admin = new Admin();
        $this->securityMock->expects($this->once())
            ->method('getUser')
            ->willReturn($admin);

        $token = 'new_token';
        $this->jwtTokensHelperMock->expects($this->once())
            ->method('createJwtToken')
            ->with($admin)
            ->willReturn($token);

        $refreshToken = new RefreshToken();
        $this->jwtTokensHelperMock->expects($this->once())
            ->method('updateJwtRefreshToken')
            ->with($admin)
            ->willReturn($refreshToken);

        $this->refreshTokenServiceMock->expects($this->once())
            ->method('save')
            ->with($refreshToken, true);

        $this->adminServiceMock->expects($this->once())
            ->method('save')
            ->with($admin, true);

        $this->securityMock->expects($this->once())
            ->method('logout')
            ->with(false);

        $result = $this->userFetcher->logout();
        $this->assertNull($result);
    }
}
