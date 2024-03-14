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

use App\CQRS\Command\CreateUser\CommandInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class CommandBus
 * @package App\CQRS\Bus
 */
class CommandBus implements CommandBusInterface
{
    use HandleTrait;

    /**
     * @param MessageBusInterface $commandBus
     */
    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    /**
     * @param CommandInterface $command
     *
     * @return mixed
     */
    public function execute(CommandInterface $command): mixed
    {
        return $this->handle($command);
    }
}
