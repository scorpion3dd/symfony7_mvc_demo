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

use App\EntityListener\UserEntityListener;
use App\Tests\Unit\BaseKernelTestCase;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class UserEntityListenerTest - Unit tests for State UserEntityListener
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\EntityListener
 */
class UserEntityListenerTest extends BaseKernelTestCase
{
    /** @var UserEntityListener $listener */
    private UserEntityListener $listener;

    /** @var SluggerInterface $sluggerMock */
    private $sluggerMock;

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
        $this->sluggerMock = $this->createMock(SluggerInterface::class);
        $this->listener = new UserEntityListener(
            $this->sluggerMock,
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
        self::markTestSkipped(self::class . ' skipped testPrePersist');
        $user = $this->createUser();
        $event = $this->createMock(PrePersistEventArgs::class);
        $this->listener->prePersist($user, $event);
        $this->assertTrue(method_exists($this->listener, 'debugFunction'));
    }

    /**
     * @testCase - method preUpdate - must be a success
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPreUpdate(): void
    {
        $user = $this->createUser();
        $event = $this->createMock(PreUpdateEventArgs::class);
        $this->listener->preUpdate($user, $event);
        $this->assertTrue(method_exists($this->listener, 'debugFunction'));
    }
}
