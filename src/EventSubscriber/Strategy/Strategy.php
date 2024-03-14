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

namespace App\EventSubscriber\Strategy;

use Symfony\Component\HttpKernel\Event\ViewEvent;

/**
 * Class Strategy - is part of the Strategy and Dependency Injection design patterns.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Behavioral/Strategy/README.html
 * @link https://designpatternsphp.readthedocs.io/en/latest/Structural/DependencyInjection/README.html
 * @package App\EventSubscriber\Strategy
 */
class Strategy
{
    /** @var Command $command */
    private Command $command;

    /**
     * @param Command $command - Dependency Injection
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * @param mixed $result
     * @param ViewEvent $event
     *
     * @return ViewEvent
     */
    public function postWrite(mixed $result, ViewEvent $event): ViewEvent
    {
        return $this->command->execute($result, $event);
    }
}
