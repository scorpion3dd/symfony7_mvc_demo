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

use App\Service\AdminServiceInterface;
use App\Service\RefreshTokenServiceInterface;
use App\State\AdminDeleteProcessor;
use App\Tests\Unit\BaseKernelTestCase;
use Doctrine\ORM\NonUniqueResultException;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AdminDeleteProcessorTest - Unit tests for State AdminDeleteProcessor
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\State
 */
class AdminDeleteProcessorTest extends BaseKernelTestCase
{
    /** @var AdminServiceInterface $adminServiceMock */
    private AdminServiceInterface $adminServiceMock;

    /** @var RefreshTokenServiceInterface $refreshTokenServiceMock */
    private RefreshTokenServiceInterface $refreshTokenServiceMock;

    /** @var LoggerInterface $loggerMock */
    private LoggerInterface $loggerMock;

    /** @var AdminDeleteProcessor $processor */
    private AdminDeleteProcessor $processor;

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
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->processor = new AdminDeleteProcessor(
            $this->adminServiceMock,
            $this->refreshTokenServiceMock,
            $this->loggerMock
        );
    }

    /**
     * @testCase - method process - must be a success, Delete operation
     *
     * @return void
     * @throws NonUniqueResultException
     */
    public function testProcessWithDeleteOperation(): void
    {
        $username = 'admin';
        $admin = $this->createAdmin($username, 'password');

        $this->adminServiceMock->expects($this->once())
            ->method('findOneByLogin')
            ->with($username)
            ->willReturn($admin);

        $this->refreshTokenServiceMock->expects($this->once())
            ->method('getJwtRefreshToken')
            ->with($username)
            ->willReturn(new RefreshToken());

        $this->refreshTokenServiceMock->expects($this->once())
            ->method('remove');

        $this->adminServiceMock->expects($this->once())
            ->method('remove');

        $response = $this->processor->process($admin, new Delete());
        $content = $response->getContent();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('{"username":"admin","status":"deleted"}', $content);
        $this->assertJson($content);
    }

    /**
     * @testCase - method process - must be a success, NotFoundHttpException
     *
     * @return void
     * @throws NonUniqueResultException
     */
    public function testProcessWithNonDeleteOperation(): void
    {
        $operationMock = $this->createMock(Operation::class);
        $admin = $this->createAdmin('username', 'password');
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Operation not Delete.');
        $this->processor->process($admin, $operationMock);
    }

    /**
     * @testCase - method process - must be a success, Admin empty
     *
     * @return void
     * @throws NonUniqueResultException
     */
    public function testProcessWithNonAdminOperation(): void
    {
        $username = 'admin';
        $admin = $this->createAdmin($username, 'password');

        $this->adminServiceMock->expects($this->once())
            ->method('findOneByLogin')
            ->with($username)
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Not found Admin.');
        $this->processor->process($admin, new Delete());
    }
}
