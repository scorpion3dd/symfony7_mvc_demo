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

use App\CQRS\Bus\EventBusInterface;
use App\CQRS\Event\CreateLogEvent;
use App\Helper\ApplicationGlobals;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
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
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionNamedType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;

/**
 * @psalm-suppress DeprecatedInterface
 * Class DoctrineOrmSubscriber
 * @package App\EventSubscriber
 */
#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postUpdate, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postRemove, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postFlush, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postLoad, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::prePersist, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::preUpdate, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::preFlush, priority: 500, connection: 'default')]
class DoctrineOrmSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    /**
     * @param EventBusInterface $eventBus
     * @param ContainerInterface $container
     * @param ApplicationGlobals $appGlobals
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly EventBusInterface  $eventBus,
        private readonly ContainerInterface $container,
        private readonly ApplicationGlobals $appGlobals,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush,
            Events::postLoad,
            Events::prePersist,
            Events::preUpdate,
            Events::preFlush,
        ];
    }

    /**
     * @param PostPersistEventArgs $args
     *
     * @return void
     */
    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->debugFunction(self::class, 'postPersist');
        $this->setEntities($args);
    }

    /**
     * @param PostUpdateEventArgs $args
     *
     * @return void
     */
    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $this->debugFunction(self::class, 'postUpdate');
        $this->setEntities($args);
    }

    /**
     * @param PostRemoveEventArgs $args
     *
     * @return void
     */
    public function postRemove(PostRemoveEventArgs $args): void
    {
        $this->debugFunction(self::class, 'postRemove');
        $this->setEntities($args);
    }

    /**
     * @param PostFlushEventArgs $args
     *
     * @return void
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        $this->debugFunction(self::class, 'postFlush');
        $this->debugParameters(self::class, ['args' => $args]);
        foreach ($this->entities as $entity) {
            if ($this->appGlobals->getType() == ApplicationGlobals::TYPE_APP_FIXTURES) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }
            try {
                $message = get_class($entity) . ' postFlush';
                $logEvent = new CreateLogEvent('', [], $message, Carbon::now());
                $this->eventBus->execute($logEvent);
            // @codeCoverageIgnoreStart
            } catch (Exception $ex) {
                $this->exception(self::class . ' postFlush', $ex);
            }
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @param PostLoadEventArgs $args
     *
     * @return void
     */
    public function postLoad(PostLoadEventArgs $args): void
    {
        $this->debugFunction(self::class, 'postLoad');
        $object = $args->getObject();
        $this->debugParameters(self::class, ['object' => $object]);
        $reflect = new ReflectionClass($object);
        foreach ($reflect->getProperties() as $property) {
            /** @var ReflectionNamedType|null $type */
            $type = $property->getType();
            if (is_null($type)
                || ($type instanceof ReflectionNamedType && $type->isBuiltin())
                || $property->isInitialized($object)) {
                continue;
            }
            // @codeCoverageIgnoreStart
            $interfaces = class_implements($type->getName());
            if (isset($interfaces[UserInterface::class])) {
                $property->setValue($object, $this->container->get($type->getName()));
            }
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @param PrePersistEventArgs $eventArgs
     *
     * @return void
     */
    public function prePersist(PrePersistEventArgs $eventArgs): void
    {
        $this->debugFunction(self::class, 'prePersist');
        $object = $eventArgs->getObject();
        $this->debugParameters(self::class, ['object' => $object]);
    }

    /**
     * @param PreUpdateEventArgs $eventArgs
     *
     * @return void
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs): void
    {
        $this->debugFunction(self::class, 'preUpdate');
        $object = $eventArgs->getObject();
        $this->debugParameters(self::class, ['object' => $object]);
    }

    /**
     * @param PreFlushEventArgs $eventArgs
     *
     * @return void
     */
    public function preFlush(PreFlushEventArgs $eventArgs): void
    {
        $this->debugFunction(self::class, 'preFlush');
        $om = $eventArgs->getObjectManager();
        $this->debugParameters(self::class, ['om' => $om]);
    }
}
