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

namespace App\Command;

use App\Helper\ApplicationGlobals;
use App\Message\CommentMessage;
use App\MessageHandler\CommentMessageHandler;
use Blackfire\Bridge\Symfony\MonitorableCommandInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class CommentMessageCommand
 * @package App\Command
 */
#[AsCommand(
    name: 'app:comment:message',
    description: 'comment message sync',
)]
class CommentMessageCommand extends BaseCommand implements MonitorableCommandInterface
{
    /**
     * @param CommentMessageHandler $commentHandler
     * @param LoggerInterface $logger
     * @param ApplicationGlobals $appGlobals
     */
    public function __construct(
        private readonly CommentMessageHandler $commentHandler,
        LoggerInterface $logger,
        ApplicationGlobals $appGlobals
    ) {
        parent::__construct($logger, $appGlobals);
        $this->debugConstruct(self::class);
    }

    /**
     * @psalm-suppress DeprecatedConstant
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->debugFunction(self::class, 'execute');
        $this->buildIo($input, $output);
        $name = "CommentMessageCommand";
        $this->getIo()->title($name . ' execute');
        $this->writeLog($name . ' ' . $this->getName() . ' - started', Logger::NOTICE);
        $this->newLineHatch();
        $context = [
            'user_ip' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36 OPR/99.0.0.0',
            'referrer' => 'http://localhost:81/en/lottery/GARRICK.HAMMES3-01HEQQMH4TY3NEQGDYWZ43PXRE',
            'permalink' => 'http://localhost:81/en/lottery/GARRICK.HAMMES3-01HEQQMH4TY3NEQGDYWZ43PXRE',
        ];
        $message = new CommentMessage(
            5,
            'http://symfony6.myguestbook.os/admin/comment/review/5',
            $context
        );
        $this->getIo()->text('Inputs:');
        $this->getIo()->newLine();
        $this->writeLog('messageId = ' . $message->getId(), Logger::DEBUG);
        try {
            $this->writeLog('Process - ' . $this->getDescription(), Logger::DEBUG);
            ob_start();
            $this->commentHandler->__invoke($message);
            // @codeCoverageIgnoreStart
            $this->writeLog('Includes:', Logger::DEBUG);
            $output = ob_get_clean();
            $output = is_string($output) ? $output : '';
            $this->writeln($output);
        } catch (Exception $ex) {
            $this->writeLog('Exception: ' . $ex->getMessage(), Logger::ERROR);
            ob_end_clean();
        }
        // @codeCoverageIgnoreEnd
        $this->getIo()->newLine();
        $this->newLineHatch();
        $this->writeLog($name . ' ' . $this->getName() . ' - finished', Logger::NOTICE);
        $this->writeLog('ALL EXECUTED SUCCESS');

        return Command::SUCCESS;
    }
}
