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

use ApiPlatform\Metadata\GetCollection;
use App\Helper\UriHelper;
use App\Service\AdminServiceInterface;
use App\Service\RefreshTokenServiceInterface;
use App\State\AdminGetCollectionProvider;
use App\Tests\Unit\BaseKernelTestCase;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AdminGetCollectionProviderTest - Unit tests for State AdminGetCollectionProvider
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\State
 */
class AdminGetCollectionProviderTest extends BaseKernelTestCase
{
    public const REFRESH_TOKEN = 'sg57asf4dg4dfg4sa6d74af5';

    /** @var AdminServiceInterface $adminServiceMock */
    private AdminServiceInterface $adminServiceMock;

    /** @var RefreshTokenServiceInterface $refreshTokenServiceMock */
    private RefreshTokenServiceInterface $refreshTokenServiceMock;

    /** @var LoggerInterface $loggerMock */
    private LoggerInterface $loggerMock;

    /** @var UriHelper $uriHelperMock */
    private UriHelper $uriHelperMock;

    /** @var AdminGetCollectionProvider $provider */
    private AdminGetCollectionProvider $provider;

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
        $this->provider = new AdminGetCollectionProvider(
            $this->adminServiceMock,
            $this->refreshTokenServiceMock,
            $this->uriHelperMock,
            $this->loggerMock
        );
    }

    /**
     * @testCase - method provide - must be a success, GetCollection operation
     *
     * @return void
     */
    public function testProvide(): void
    {
        $uriTemplate = '/admins/list1';
        $username = 'admin';
        $admins = [];
        $admin = $this->createAdmin($username, 'password');
        $admins[] = $admin;

        $this->adminServiceMock->expects($this->once())
            ->method('findAll')
            ->willReturn($admins);

        $this->uriHelperMock->expects($this->once())
            ->method('isApiAdminsListProvide')
            ->with($uriTemplate)
            ->willReturn(true);

        $this->refreshTokenServiceMock->expects($this->once())
            ->method('getJwtRefreshToken')
            ->with($username)
            ->willReturn(new RefreshToken());

        $response = $this->provider->provide(new GetCollection($uriTemplate));
        $this->assertIsArray($response);
        $this->assertEquals($admins, $response);
    }

    /**
     * @testCase - method provide - must be a success, NotFoundHttpException
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
     * @testCase - method provide - must be a success, RequestUri
     *
     * @return void
     */
    public function testProvideWithNonRequestUriOperation(): void
    {
        $uriTemplate = '/en/admins/list1/2';
        $username = 'admin';
        $admins = [];
        $admin = $this->createAdmin($username, 'password');
        $admins[] = $admin;

        $this->adminServiceMock->expects($this->once())
            ->method('findAll')
            ->willReturn($admins);

        $this->uriHelperMock->expects($this->once())
            ->method('isApiAdminsListProvide')
            ->with($uriTemplate)
            ->willReturn(false);

        $response = $this->provider->provide(new GetCollection($uriTemplate));
        $this->assertIsArray($response);
        $this->assertEquals($admins, $response);
    }
}
