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

namespace App\Factory;

use App\Document\Log;
use Carbon\Carbon;
use Monolog\Logger;

/**
 * Class LogFactory - is the Factory Method design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/FactoryMethod/README.html
 * @package App\Factory
 */
class LogFactory
{
    public function __construct()
    {
    }

    /**
     * @psalm-suppress DeprecatedConstant
     * @param string $message
     * @param int $priority
     * @param string $priorityName
     * @param int $currentUserId
     *
     * @return Log
     */
    public function create(
        string $message = '',
        int $priority = Logger::DEBUG,
        string $priorityName = '',
        int $currentUserId = 0
    ): Log {
        $log = new Log();
        $log->setMessage($message);
        $log->setPriority($priority);
        if ($priorityName == '') {
            $priorityList = Log::getPriorities();
            $priorityName = $priorityList[$priority];
        }
        $log->setPriorityName($priorityName);
        $log->setExtra(['currentUserId=' . $currentUserId]);
        $log->setTimestamp(Carbon::parse('2023-01-01'));

        return $log;
    }
}
