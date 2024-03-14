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

use App\Entity\EntityInterface;
use App\EventSubscriber\EasyAdminSubscriber;
use App\Tests\Unit\BaseKernelTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * Class EasyAdminSubscriberTest - Unit tests for State EasyAdminSubscriber
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\EventSubscriber
 */
class EasyAdminSubscriberTest extends BaseKernelTestCase
{
    /** @var EasyAdminSubscriber $subscriber */
    private EasyAdminSubscriber $subscriber;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->subscriber = new EasyAdminSubscriber($this->logger);
    }

    /**
     * @testCase - method getSubscribedEvents - must be a success
     *
     * @return void
     */
    public function testGetSubscribedEvents(): void
    {
        $expectedEvents = [
            BeforeEntityPersistedEvent::class => ['onBeforeEntityPersisted'],
            BeforeEntityUpdatedEvent::class => ['onBeforeEntityUpdatedEvent'],
            AfterEntityUpdatedEvent::class => ['onAfterEntityUpdatedEvent'],
            BeforeCrudActionEvent::class => ['onBeforeCrudActionEvent'],
            AfterCrudActionEvent::class => ['onAfterCrudAction'],
        ];
        $this->assertEquals($expectedEvents, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param string $version
     *
     * @return EntityInterface
     * @throws Exception
     */
    private function getEntity(string $version): EntityInterface
    {
        $entity = $this->createRole();
        switch ($version) {
            case '1':
                $entity = $this->createUser();
                break;
            case '2':
                $entity = $this->createPermission();
                break;
            case '3':
                $entity = $this->createRole();
                break;
        }

        return $entity;
    }

    /**
     * @testCase - method onBeforeEntityPersisted - must be a success, Odm
     *
     * Class "EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent" is declared "final" and cannot be doubled
     *
     * @dataProvider provideEntity
     *
     * @param string $version
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testOnBeforeEntityPersisted(string $version): void
    {
        self::markTestSkipped(self::class . ' skipped testOnBeforeEntityPersisted');
        $event = $this->createMock(BeforeEntityPersistedEvent::class);
        $event->expects($this->once())
            ->method('getEntityInstance')
            ->willReturn($this->getEntity($version));

        $this->subscriber->onBeforeEntityPersisted($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @return iterable
     */
    public static function provideEntity(): iterable
    {
        $version = '1';
        yield $version => [$version];

        $version = '2';
        yield $version => [$version];

        $version = '3';
        yield $version => [$version];
    }

    /**
     * @testCase - method onBeforeEntityUpdatedEvent - must be a success, Odm
     *
     * Class "EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent" is declared "final" and cannot be doubled
     *
     * @dataProvider provideEntity
     *
     * @param string $version
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testOnBeforeEntityUpdatedEvent(string $version): void
    {
        self::markTestSkipped(self::class . ' skipped testOnBeforeEntityUpdatedEvent');
        $event = $this->createMock(BeforeEntityUpdatedEvent::class);
        $event->expects($this->once())
            ->method('getEntityInstance')
            ->willReturn($this->getEntity($version));

        $this->subscriber->onBeforeEntityUpdatedEvent($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method onAfterEntityUpdatedEvent - must be a success, Odm
     *
     * Class "EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testOnAfterEntityUpdatedEvent(): void
    {
        self::markTestSkipped(self::class . ' skipped testOnAfterEntityUpdatedEvent');
        $entity = new stdClass();
        $event = $this->createMock(AfterEntityUpdatedEvent::class);
        $event->expects($this->once())
            ->method('getEntityInstance')
            ->willReturn($entity);

        $this->subscriber->onAfterEntityUpdatedEvent($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method onBeforeCrudActionEvent - must be a success, Odm
     *
     * Class "EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testOnBeforeCrudActionEvent(): void
    {
        self::markTestSkipped(self::class . ' skipped testOnBeforeCrudActionEvent');
        $entity = new stdClass();
        $entityDto = $this->createMock(EntityDto::class);
        $entityDto->expects($this->once())
            ->method('getInstance')
            ->willReturn($entity);

        $adminContext = $this->createMock(AdminContext::class);
        $adminContext->expects($this->once())
            ->method('getEntity')
            ->willReturn($entityDto);

        $event = $this->createMock(BeforeCrudActionEvent::class);
        $event->expects($this->once())
            ->method('getAdminContext')
            ->willReturn($adminContext);

        $this->subscriber->onBeforeCrudActionEvent($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method onAfterCrudAction - must be a success, Odm
     *
     * Class "EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testOnAfterCrudAction(): void
    {
        self::markTestSkipped(self::class . ' skipped testOnAfterCrudAction');
        $event = $this->createMock(AfterCrudActionEvent::class);
        $this->subscriber->onAfterCrudAction($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }
}
