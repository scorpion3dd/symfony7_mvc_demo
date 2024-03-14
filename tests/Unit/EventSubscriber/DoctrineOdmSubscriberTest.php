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

use App\EventSubscriber\DoctrineOdmSubscriber;
use App\Tests\Unit\BaseKernelTestCase;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs as OdmLifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostLoadEventArgs as OrmPostLoadEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs as OrmPreFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs as OrmPrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs as OrmPreUpdateEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use stdClass;
use PHPUnit\Framework\MockObject\Exception;

/**
 * Class DoctrineOdmSubscriberTest - Unit tests for State DoctrineOdmSubscriber
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\EventSubscriber
 */
class DoctrineOdmSubscriberTest extends BaseKernelTestCase
{
    /** @var DoctrineOdmSubscriber $subscriber */
    private DoctrineOdmSubscriber $subscriber;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->subscriber = new DoctrineOdmSubscriber($this->logger);
    }

    /**
     * @testCase - method getSubscribedEvents - must be a success
     *
     * @return void
     */
    public function testGetSubscribedEvents(): void
    {
        $expectedEvents = [
            Events::postLoad,
            Events::prePersist,
            Events::preUpdate,
            Events::preFlush,
        ];
        $this->assertEquals($expectedEvents, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @testCase - method postLoad - must be a success, Odm
     *
     * @return void
     * @throws Exception
     */
    public function testPostLoadOdm(): void
    {
        $args = $this->createMock(OdmLifecycleEventArgs::class);
        $this->subscriber->postLoad($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method postLoad - must be a success, Orm
     *
     * @return void
     * @throws Exception
     */
    public function testPostLoadOrm(): void
    {
        self::markTestSkipped(self::class . ' skipped testPostLoadOrm');
        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn(new ClassMetadata(stdClass::class));

        $args = $this->createMock(OrmPostLoadEventArgs::class);
        $args->expects($this->once())
            ->method('getObject')
            ->willReturn(new stdClass());
        $args->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($objectManager);

        $this->subscriber->postLoad($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method prePersist - must be a success, Odm
     *
     * @return void
     * @throws Exception
     */
    public function testPrePersistOdm(): void
    {
        $args = $this->createMock(OdmLifecycleEventArgs::class);
        $this->subscriber->prePersist($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method prePersist - must be a success, Orm
     *
     * @return void
     * @throws Exception
     */
    public function testPrePersistOrm(): void
    {
        self::markTestSkipped(self::class . ' skipped testPrePersistOrm');
        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn(new ClassMetadata(stdClass::class));

        $args = $this->createMock(OrmPrePersistEventArgs::class);
        $args->expects($this->once())
            ->method('getObject')
            ->willReturn(new stdClass());
        $args->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($objectManager);

        $this->subscriber->prePersist($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method preUpdate - must be a success, Odm
     *
     * @return void
     * @throws Exception
     */
    public function testPreUpdateOdm(): void
    {
        $args = $this->createMock(OdmLifecycleEventArgs::class);
        $this->subscriber->preUpdate($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method preUpdate - must be a success, Orm
     *
     * @return void
     * @throws Exception
     */
    public function testPreUpdateOrm(): void
    {
        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn(new ClassMetadata(stdClass::class));

        $args = $this->createMock(OrmPreUpdateEventArgs::class);
        $args->expects($this->once())
            ->method('getObject')
            ->willReturn(new stdClass());
        $args->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($objectManager);

        $this->subscriber->preUpdate($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method preFlush - must be a success, Odm
     *
     * @return void
     * @throws Exception
     */
    public function testPreFlushOdm(): void
    {
        $args = $this->createMock(OdmLifecycleEventArgs::class);
        $this->subscriber->preFlush($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method preFlush - must be a success, Orm
     *
     * @return void
     * @throws Exception
     */
    public function testPreFlushOrm(): void
    {
        $args = $this->createMock(OrmPreFlushEventArgs::class);
        $this->subscriber->preFlush($args);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }
}
