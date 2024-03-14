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

namespace App\CQRS\Bus;

use App\CQRS\Event\EventInterface;

/**
 * Interface EventBusInterface
 * @package App\CQRS\Bus
 */
interface EventBusInterface
{
    /**
     * @param EventInterface $event
     *
     * @return mixed
     */
    public function execute(EventInterface $event): mixed;
}
