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

namespace App\Tests\UnitAdmin\Controller;

use App\Factory\LogFactory;
use App\Repository\UserRepositoryInterface;
use App\Service\LogServiceInterface;
use App\Tests\BaseCrudController;
use App\Tests\Unit\Interface\SlidingPaginationInterfaceMock;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Orm\EntityPaginatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Factory\PaginatorFactory;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Base class BaseCrudControllerAdmin - for all unit tests
 * in Admin CrudControllers by EasyAdminBundle
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @link https://symfony.com/doc/current/testing.html#types-of-tests
 *
 * @package App\Tests\UnitAdmin\Controller
 */
class BaseCrudControllerAdmin extends BaseCrudController
{
    /** @var LogFactory $logFactory */
    protected LogFactory $logFactory;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->prepareDbMySqlMock();
        $this->logFactory = $this->container->get(LogFactory::class);
    }

    /**
     * @param string $id
     *
     * @return void
     */
    protected function logServiceGetLogMock(string $id): void
    {
        $logServiceMock = $this->logServiceMock();
        $logServiceMock->expects($this->exactly(1))
            ->method('getLog')
            ->with(
                $this->equalTo($id)
            )
            ->willReturn(null);
        $this->container->set(LogServiceInterface::class, $logServiceMock);
    }

    /**
     * @param bool $getLog
     *
     * @return void
     */
    protected function logServiceEditSaveLogMock(bool $getLog = true): void
    {
        $logServiceMock = $this->logServiceMock();
        $this->log->setId($this->logId);
        if ($getLog) {
            $logServiceMock->expects($this->exactly(1))
                ->method('getLog')
                ->with(
                    $this->equalTo($this->log->getId())
                )
                ->willReturn($this->log);
        }
        $logDbTimestamp = $this->log->getTimestamp() ?: new DateTime();
        $logServiceMock->expects($this->exactly(1))
            ->method('editLog')
            ->with(
                $this->equalTo($this->log),
                $this->equalTo($this->log->getId()),
                $this->equalTo($logDbTimestamp)
            )
            ->willReturn($this->log);
        $logServiceMock->expects($this->exactly(1))
            ->method('save')
            ->with(
                $this->equalTo($this->log),
                $this->equalTo(true)
            );
        $this->container->set(LogServiceInterface::class, $logServiceMock);
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function paginatorLogMock(): void
    {
        $page = 1;
        $paginatorMock = $this->createMock(SlidingPaginationInterfaceMock::class);
        $paginatorMock->expects($this->exactly(1))
            ->method('setTemplate');
        $paginatorMock->expects($this->exactly(1))
            ->method('setPageRange');

        $logServiceMock = $this->logServiceMock();
        $logServiceMock->expects($this->exactly(1))
            ->method('getLogsPaginator')
            ->with(
                $this->equalTo($page)
            )
            ->willReturn($paginatorMock);
        $this->container->set(LogServiceInterface::class, $logServiceMock);
    }

    /**
     * @param array $results
     *
     * @return void
     * @throws Exception
     */
    protected function paginatorMock(array $results = []): void
    {
        $paginatorMock = $this->createMock(EntityPaginatorInterface::class);
        $paginatorMock->expects($this->exactly(1))
            ->method('isOutOfRange')
            ->willReturn(false);
        $paginatorMock->expects($this->exactly(1))
            ->method('getResults')
            ->willReturn($results);

        $paginatorFactory = $this->createMock(PaginatorFactory::class);
        $paginatorFactory->expects($this->exactly(1))
            ->method('create')
            ->willReturn($paginatorMock);
        $this->container->set(PaginatorFactory::class, $paginatorFactory);
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function userChartHelperMock(): void
    {
        $users = [];
        $userRepositoryMock = $this->userRepositoryMock();
        $userRepositoryMock->expects($this->exactly(1))
            ->method('findUsersAccessed')
            ->willReturn($users);
        $this->container->set(UserRepositoryInterface::class, $userRepositoryMock);
    }
}
