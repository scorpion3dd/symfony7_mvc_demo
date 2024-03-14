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

namespace App\EventSubscriber;

use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EasyAdminSubscriber
 * @package App\EventSubscriber
 */
class EasyAdminSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['onBeforeEntityPersisted'],
            BeforeEntityUpdatedEvent::class => ['onBeforeEntityUpdatedEvent'],
            AfterEntityUpdatedEvent::class => ['onAfterEntityUpdatedEvent'],
            BeforeCrudActionEvent::class => ['onBeforeCrudActionEvent'],
            AfterCrudActionEvent::class => ['onAfterCrudAction'],
        ];
    }

    /**
     * @param BeforeEntityPersistedEvent $event
     * @return void
     */
    public function onBeforeEntityPersisted(BeforeEntityPersistedEvent $event): void
    {
        $this->debugFunction(self::class, 'onBeforeEntityPersisted');
        $entity = $event->getEntityInstance();
        $this->debugParameters(self::class, ['entity' => $entity]);
        if (isset($entity)) {
            $entity = $this->entityPrePersist($entity);
            $this->debugParameters(self::class, ['entity' => $entity]);
        }
    }

    /**
     * @param BeforeEntityUpdatedEvent $event
     * @return void
     */
    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event): void
    {
        $this->debugFunction(self::class, 'BeforeEntityUpdatedEvent');
        $entity = $event->getEntityInstance();
        $this->debugParameters(self::class, ['entity' => $entity]);
        if (isset($entity)) {
            $entity = $this->entityPreUpdate($entity);
            $this->debugParameters(self::class, ['entity' => $entity]);
        }
    }

    /**
     * @param AfterEntityUpdatedEvent $event
     * @return void
     */
    public function onAfterEntityUpdatedEvent(AfterEntityUpdatedEvent $event): void
    {
        $this->debugFunction(self::class, 'onAfterEntityUpdatedEvent');
        $entity = $event->getEntityInstance();
        $this->debugParameters(self::class, ['entity' => $entity]);
        if (isset($entity)) {
            $class = get_class($entity);
            $this->debugParameters(self::class, ['class' => $class]);
        }
    }

    /**
     * @param BeforeCrudActionEvent $event
     * @return void
     */
    public function onBeforeCrudActionEvent(BeforeCrudActionEvent $event): void
    {
        $this->debugFunction(self::class, 'onBeforeCrudActionEvent');
        $adminContext = $event->getAdminContext();
        if (isset($adminContext)) {
            $entityDto = $adminContext->getEntity();
            $entity = $entityDto->getInstance();
            $this->debugParameters(self::class, ['entity' => $entity]);
            if (isset($entity)) {
                $class = get_class($entity);
                $this->debugParameters(self::class, ['class' => $class]);
            }
        }
    }

    /**
     * @param AfterCrudActionEvent $event
     * @return void
     */
    public function onAfterCrudAction(AfterCrudActionEvent $event): void
    {
        $this->debugFunction(self::class, 'onAfterCrudAction');
        $adminContext = $event->getAdminContext();
        if (isset($adminContext)) {
            // @codeCoverageIgnoreStart
            $crudControllers = $adminContext->getCrudControllers();
            $this->debugParameters(self::class, ['crudControllers' => $crudControllers]);
            // @codeCoverageIgnoreEnd
        }
    }
}
