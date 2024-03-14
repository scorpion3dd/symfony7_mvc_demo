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

namespace App\Tests\Unit\EntityListener;

use App\EntityListener\PermissionEntityListener;
use App\Tests\Unit\BaseKernelTestCase;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Class PermissionEntityListenerTest - Unit tests for State UserEntityListener
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\EntityListener
 */
class PermissionEntityListenerTest extends BaseKernelTestCase
{
    /** @var PermissionEntityListener $listener */
    private PermissionEntityListener $listener;

    /** @var LoggerInterface $loggerMock */
    private $loggerMock;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->listener = new PermissionEntityListener(
            $this->loggerMock
        );
    }

    /**
     * @testCase - method prePersist - must be a success
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPrePersist(): void
    {
        $permission = $this->createPermission();
        $event = $this->createMock(LifecycleEventArgs::class);
        $this->listener->prePersist($permission, $event);
        $this->assertTrue(method_exists($this->listener, 'debugFunction'));
    }
}
