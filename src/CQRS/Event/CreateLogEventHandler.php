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

use App\Document\Log;
use App\Factory\LogFactory;
use App\Helper\ApplicationGlobals;
use App\Service\LogServiceInterface;
use App\Util\ConsoleOutputTrait;
use App\Util\LoggerTrait;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Class CreateLogEventHandler
 * @package App\CQRS\Event
 */
#[AsMessageHandler]
class CreateLogEventHandler implements EventHandlerInterface
{
    use LoggerTrait;
    use ConsoleOutputTrait;

    /**
     * @param LogServiceInterface $logService
     * @param LogFactory $logFactory
     * @param ApplicationGlobals $appGlobals
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly LogServiceInterface $logService,
        private readonly LogFactory $logFactory,
        ApplicationGlobals $appGlobals,
        LoggerInterface $logger
    ) {
        $this->appGlobals = $appGlobals;
        $this->logger = $logger;
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
        $this->buildIo($this->input, $this->output);
        $this->debugConstruct(self::class);
    }

    /**
     * @psalm-suppress DeprecatedConstant
     * @param CreateLogEvent $createLogEvent
     *
     * @return string
     */
    public function __invoke(CreateLogEvent $createLogEvent): string
    {
        $this->debugFunction(self::class, 'invoke');
        $name = self::class . ' invoke';
        $this->debugMessage(self::class, 'start');
        $this->echo($name . ' - started', Logger::NOTICE);

        /** @psalm-suppress DeprecatedConstant */
        $priority = Logger::DEBUG;
        $priorityList = Log::getPriorities();
        $priorityName = $priorityList[$priority];
        $log = $this->logFactory->create($createLogEvent->message, $priority, $priorityName);
        $this->logService->save($log, true);

        $logId = $log->getId() ?? '';
        $this->debugMessage(self::class, 'log saved');
        $this->debugParameters(self::class, ['logId' => $logId]);
        $this->echo('log saved, log-id = ' . $logId, Logger::DEBUG);

        $this->debugMessage(self::class, 'finish');
        $this->echo($name . ' - finished', Logger::NOTICE);

        return $logId;
    }
}
