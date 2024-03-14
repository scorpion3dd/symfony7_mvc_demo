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

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs as OrmPreUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs as OrmPrePersistEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs as OrmPreFlushEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs as OrmPostLoadEventArgs;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs as OdmPreUpdateEventArgs;
use Doctrine\ODM\MongoDB\Event\PreFlushEventArgs as OdmPreFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PostCollectionLoadEventArgs as OdmPostCollectionLoadEventArgs;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs as OdmLifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Psr\Log\LoggerInterface;

/**
 * Class DoctrineOdmSubscriber
 * @package App\EventSubscriber
 */
class DoctrineOdmSubscriber extends BaseSubscriber implements EventSubscriber
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
     * @return array<array-key, string>
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad,
            Events::prePersist,
            Events::preUpdate,
            Events::preFlush,
        ];
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @param OrmPostLoadEventArgs|OdmLifecycleEventArgs $eventArgs
     *
     * @return void
     */
    public function postLoad($eventArgs): void
    {
        $this->debugFunction(self::class, 'postLoad');
        if ($eventArgs instanceof OdmLifecycleEventArgs) {
            $document = $eventArgs->getDocument();
            $this->debugParameters(self::class, ['document' => $document]);
            $dm = $eventArgs->getDocumentManager();
            $class = $dm->getClassMetadata(get_class($document));
            $this->debugParameters(self::class, ['class' => $class]);
        } elseif ($eventArgs instanceof OrmPostLoadEventArgs) {
            $object = $eventArgs->getObject();
            $this->debugParameters(self::class, ['object' => $object]);
            $om = $eventArgs->getObjectManager();
            $class = $om->getClassMetadata(get_class($object));
            $this->debugParameters(self::class, ['class' => $class]);
        }
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @param OrmPrePersistEventArgs|OdmLifecycleEventArgs $eventArgs
     *
     * @return void
     */
    public function prePersist($eventArgs): void
    {
        $this->debugFunction(self::class, 'prePersist');
        if ($eventArgs instanceof OdmLifecycleEventArgs) {
            $document = $eventArgs->getDocument();
            $this->debugParameters(self::class, ['document' => $document]);
            $dm = $eventArgs->getDocumentManager();
            $class = $dm->getClassMetadata(get_class($document));
            $this->debugParameters(self::class, ['class' => $class]);
//        $dm->getUnitOfWork()->recomputeSingleDocumentChangeSet($class, $document);
        } elseif ($eventArgs instanceof OrmPrePersistEventArgs) {
            $object = $eventArgs->getObject();
            $this->debugParameters(self::class, ['object' => $object]);
            $om = $eventArgs->getObjectManager();
            $class = $om->getClassMetadata(get_class($object));
            $this->debugParameters(self::class, ['class' => $class]);
        }
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @param OrmPreUpdateEventArgs|OdmLifecycleEventArgs $eventArgs
     *
     * @return void
     */
    public function preUpdate($eventArgs): void
    {
        $this->debugFunction(self::class, 'preUpdate');
        if ($eventArgs instanceof OdmLifecycleEventArgs) {
            $document = $eventArgs->getDocument();
            $this->debugParameters(self::class, ['document' => $document]);
            $dm = $eventArgs->getDocumentManager();
            $class = $dm->getClassMetadata(get_class($document));
            $this->debugParameters(self::class, ['class' => $class]);
        } elseif ($eventArgs instanceof OrmPreUpdateEventArgs) {
            $object = $eventArgs->getObject();
            $this->debugParameters(self::class, ['object' => $object]);
            $om = $eventArgs->getObjectManager();
            $class = $om->getClassMetadata(get_class($object));
            $this->debugParameters(self::class, ['class' => $class]);
        }
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @param OrmPreFlushEventArgs|OdmLifecycleEventArgs $eventArgs
     *
     * @return void
     */
    public function preFlush($eventArgs): void
    {
        $this->debugFunction(self::class, 'preFlush');
        if ($eventArgs instanceof OdmLifecycleEventArgs) {
            $dm = $eventArgs->getDocumentManager();
            $this->debugParameters(self::class, ['dm' => $dm]);
        } elseif ($eventArgs instanceof OrmPreFlushEventArgs) {
            $om = $eventArgs->getObjectManager();
            $this->debugParameters(self::class, ['om' => $om]);
        }
    }
}
