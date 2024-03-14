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

namespace App\CQRS\Event;

use DateTime;

/**
 * Class CreateLogEvent
 * @package App\CQRS\Event
 */
class CreateLogEvent implements EventInterface
{
    /**
     * @param string $id
     * @param array $extra
     * @param string $message
     * @param DateTime $timestamp
     */
    public function __construct(
        public readonly string $id,
        public readonly array $extra,
        public readonly string $message,
        public readonly DateTime $timestamp,
    ) {
    }
}
