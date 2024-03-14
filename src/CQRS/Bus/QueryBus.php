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

use App\CQRS\Query\FindUserByEmail\QueryInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class QueryBus
 * @package App\CQRS\Bus
 */
class QueryBus implements QueryBusInterface
{
    use HandleTrait;

    /**
     * @param MessageBusInterface $queryBus
     */
    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    /**
     * @param QueryInterface $query
     *
     * @return mixed
     */
    public function execute(QueryInterface $query): mixed
    {
        return $this->handle($query);
    }
}
