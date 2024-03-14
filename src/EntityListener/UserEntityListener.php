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

use App\Entity\User;
use App\Util\LoggerTrait;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class UserEntityListener
 * @package App\EntityListener
 */
#[AsEntityListener(event: Events::prePersist, entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, entity: User::class)]
class UserEntityListener
{
    use LoggerTrait;

    /**
     * @param SluggerInterface $slugger
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly SluggerInterface $slugger,
        LoggerInterface                   $logger
    ) {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * @param User $user
     * @param PrePersistEventArgs $event
     *
     * @return void
     */
    public function prePersist(User $user, PrePersistEventArgs $event): void
    {
        $this->debugFunction(self::class, 'prePersist');
        $user->computeSlug($this->slugger);
        $this->debugParameters(self::class, ['event' => $event]);
    }

    /**
     * @param User $user
     * @param PreUpdateEventArgs $event
     *
     * @return void
     */
    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        $this->debugFunction(self::class, 'preUpdate');
        $user->computeSlug($this->slugger);
        $this->debugParameters(self::class, ['event' => $event]);
    }
}
