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

namespace App\EntityListener;

use App\Entity\Role;
use App\Util\LoggerTrait;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

/**
 * Class RoleEntityListener
 * @package App\EntityListener
 */
#[AsEntityListener(event: Events::prePersist, entity: Role::class)]
class RoleEntityListener
{
    use LoggerTrait;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * @param Role $role
     * @param LifecycleEventArgs $event
     *
     * @return void
     */
    public function prePersist(Role $role, LifecycleEventArgs $event): void
    {
        $this->debugFunction(self::class, 'prePersist');
        $role->setDateCreated(Carbon::now());
        $this->debugParameters(self::class, ['event' => $event]);
    }
}
