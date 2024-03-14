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

namespace App\Tests\Unit\EventSubscriber;

use App\CQRS\Bus\EventBusInterface;
use App\EventSubscriber\DoctrineOrmSubscriber;
use App\Tests\Unit\BaseKernelTestCase;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use ReflectionNamedType;
use ReflectionProperty;

/**
 * Class DoctrineOrmSubscriberTest - Unit tests for State DoctrineOrmSubscriber
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\EventSubscriber
 */
class DoctrineOrmSubscriberTest extends BaseKernelTestCase
{
    /** @var DoctrineOrmSubscriber $subscriber */
    private DoctrineOrmSubscriber $subscriber;

    /** @var EventBusInterface $eventBus */
    private EventBusInterface $eventBus;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->eventBus = $this->createMock(EventBusInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->subscriber = new DoctrineOrmSubscriber($this->eventBus, $this->container, $this->appGlobals, $this->logger);
    }

    /**
     * @testCase - method getSubscribedEvents - must be a success
     *
     * @return void
     */
    public function testGetSubscribedEvents(): void
    {
        $expectedEvents = [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush,
            Events::postLoad,
            Events::prePersist,
            Events::preUpdate,
            Events::preFlush,
        ];
        $this->assertEquals($expectedEvents, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @testCase - method postPersist - must be a success
     *
     * Class "Doctrine\ORM\Event\PostPersistEventArgs" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPostPersist(): void
    {
        self::markTestSkipped(self::class . ' skipped testPostPersist');
        $args = $this->createMock(PostPersistEventArgs::class);
        $this->subscriber->postPersist($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method postUpdate - must be a success
     *
     * Class "Doctrine\ORM\Event\PostUpdateEventArgs" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPostUpdate(): void
    {
        self::markTestSkipped(self::class . ' skipped testPostUpdate');
        $args = $this->createMock(PostUpdateEventArgs::class);
        $this->subscriber->postUpdate($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method postRemove - must be a success
     *
     * Class "Doctrine\ORM\Event\PostRemoveEventArgs" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPostRemove(): void
    {
        self::markTestSkipped(self::class . ' skipped testPostRemove');
        $args = $this->createMock(PostRemoveEventArgs::class);
        $this->subscriber->postRemove($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method postFlush - must be a success
     *
     * Class "Doctrine\ORM\Event\PostPersistEventArgs" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPostFlush(): void
    {
        self::markTestSkipped(self::class . ' skipped testPostFlush');
        $user = $this->createUser();
        $args = $this->createMock(PostPersistEventArgs::class);
        $args->expects($this->once())
            ->method('getObject')
            ->willReturn($user);
        $this->subscriber->setEntities($args);

        $args2 = $this->createMock(PostFlushEventArgs::class);
        $this->subscriber->postFlush($args2);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method postLoad - must be a success
     *
     * Class "Doctrine\ORM\Event\PostLoadEventArgs" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPostLoad(): void
    {
        self::markTestSkipped(self::class . ' skipped testPostLoad');
        $user = $this->createUser();
        $args = $this->createMock(PostLoadEventArgs::class);
        $args->expects($this->once())
            ->method('getObject')
            ->willReturn($user);

        $property = $this->createMock(ReflectionProperty::class);
        $property->expects($this->any())
            ->method('getType')
            ->willReturn($this->createMock(ReflectionNamedType::class));

        $type = $property->getType();
        $this->assertInstanceOf(ReflectionNamedType::class, $type);
        $this->assertFalse($type->isBuiltin());

        $property->expects($this->any())
            ->method('isInitialized')
            ->willReturn(false);

        $this->subscriber->postLoad($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method prePersist - must be a success
     *
     * Class "Doctrine\ORM\Event\PrePersistEventArgs" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPrePersist(): void
    {
        self::markTestSkipped(self::class . ' skipped testPrePersist');
        $args = $this->createMock(PrePersistEventArgs::class);
        $this->subscriber->prePersist($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method preUpdate - must be a success
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPreUpdate(): void
    {
        $args = $this->createMock(PreUpdateEventArgs::class);
        $this->subscriber->preUpdate($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method preFlush - must be a success
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPreFlush(): void
    {
        $args = $this->createMock(PreFlushEventArgs::class);
        $this->subscriber->preFlush($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }
}
