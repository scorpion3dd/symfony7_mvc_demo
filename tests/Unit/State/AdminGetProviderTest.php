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

use ApiPlatform\Metadata\Get;
use App\Entity\Admin;
use App\Helper\UriHelper;
use App\Service\AdminServiceInterface;
use App\Service\RefreshTokenServiceInterface;
use App\State\AdminGetProvider;
use App\Tests\Unit\BaseKernelTestCase;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AdminGetProviderTest - Unit tests for State AdminGetProvider
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\State
 */
class AdminGetProviderTest extends BaseKernelTestCase
{
    /** @var AdminServiceInterface $adminServiceMock */
    private AdminServiceInterface $adminServiceMock;

    /** @var RefreshTokenServiceInterface $refreshTokenServiceMock */
    private RefreshTokenServiceInterface $refreshTokenServiceMock;

    /** @var LoggerInterface $loggerMock */
    private LoggerInterface $loggerMock;

    /** @var UriHelper $uriHelperMock */
    private UriHelper $uriHelperMock;

    /** @var AdminGetProvider $provider */
    private AdminGetProvider $provider;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->adminServiceMock = $this->createMock(AdminServiceInterface::class);
        $this->refreshTokenServiceMock = $this->createMock(RefreshTokenServiceInterface::class);
        $this->uriHelperMock = $this->createMock(UriHelper::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->provider = new AdminGetProvider(
            $this->adminServiceMock,
            $this->refreshTokenServiceMock,
            $this->uriHelperMock,
            $this->loggerMock
        );
    }

    /**
     * @testCase - method provide - must be a success, Get operation
     *
     * @return void
     */
    public function testProvide(): void
    {
        $adminId = 1;
        $uriVariables = [];
        $uriVariables['id'] = $adminId;
        $uriTemplate = '/api/admins/' . $adminId . '/';
        $username = 'admin';
        $admin = $this->createAdmin($username, 'password');

        $this->adminServiceMock->expects($this->once())
            ->method('findOneById')
            ->with($adminId)
            ->willReturn($admin);

        $this->uriHelperMock->expects($this->once())
            ->method('isApiAdminsItemProvide')
            ->with($uriTemplate)
            ->willReturn(true);

        $this->refreshTokenServiceMock->expects($this->once())
            ->method('getJwtRefreshToken')
            ->with($username)
            ->willReturn(new RefreshToken());

        $response = $this->provider->provide(new Get($uriTemplate), $uriVariables);
        $this->assertInstanceOf(Admin::class, $response);
        $this->assertEquals($admin, $response);
    }

    /**
     * @testCase - method provide - must be a success, NotFoundHttpException
     * Operation not Get
     *
     * @return void
     */
    public function testProvideWithNonGetCollectionOperation(): void
    {
        $operationMock = $this->createMock(Operation::class);
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Operation not Get.');
        $this->provider->provide($operationMock);
    }

    /**
     * @testCase - method provide - must be a success, NotFoundHttpException
     * Not found Admin
     *
     * @return void
     */
    public function testProvideWithNonAdminOperation(): void
    {
        $adminId = 1;
        $uriVariables = [];
        $uriVariables['id'] = $adminId;
        $uriTemplate = '/api/admins/' . $adminId . '/';

        $this->adminServiceMock->expects($this->once())
            ->method('findOneById')
            ->with($adminId)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Not found Admin.');
        $this->provider->provide(new Get($uriTemplate), $uriVariables);
    }

    /**
     * @testCase - method provide - must be a success, NotFoundHttpException
     * RequestUri not ApiAdminsItemProvide
     *
     * @return void
     */
    public function testProvideWithNonRequestUriOperation(): void
    {
        $adminId = 1;
        $uriVariables = [];
        $uriVariables['id'] = $adminId;
        $uriTemplate = '/api/admins/' . $adminId . '/';
        $username = 'admin';
        $admin = $this->createAdmin($username, 'password');

        $this->adminServiceMock->expects($this->once())
            ->method('findOneById')
            ->with($adminId)
            ->willReturn($admin);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('RequestUri not ApiAdminsItemProvide.');
        $this->provider->provide(new Get($uriTemplate), $uriVariables);
    }
}
