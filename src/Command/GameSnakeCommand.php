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

use App\Command\GameSnake\SnakeGame;
use App\Helper\ApplicationGlobals;
use Blackfire\Bridge\Symfony\MonitorableCommandInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GameSnakeCommand
 * @package App\Command
 */
#[AsCommand(
    name: 'app:game:snake',
    description: 'game snake',
)]
class GameSnakeCommand extends BaseCommand implements MonitorableCommandInterface
{
    /**
     * @param LoggerInterface $logger
     * @param ApplicationGlobals $appGlobals
     */
    public function __construct(LoggerInterface $logger, ApplicationGlobals $appGlobals)
    {
        parent::__construct($logger, $appGlobals);
        $this->debugConstruct(self::class);
    }

    /**
     * @psalm-suppress DeprecatedConstant
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->debugFunction(self::class, 'execute');
        $this->buildIo($input, $output);
        $name = "GameSnakeCommand";
        $this->getIo()->title($name . ' execute');
        $this->writeLog($name . ' ' . $this->getName() . ' - started', Logger::NOTICE);
        $this->newLineHatch();
        try {
            $this->writeLog('Process - ' . $this->getDescription(), Logger::DEBUG);
            ob_start();
            $game = new SnakeGame(20, 10, $this->logger, $this->appGlobals);
            $game->run();
            $this->writeLog('Includes:', Logger::DEBUG);
            $output = ob_get_clean();
            $output = is_string($output) ? $output : '';
            $this->writeln($output);
        // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $this->writeLog('Exception: ' . $ex->getMessage(), Logger::ERROR);
        }
        // @codeCoverageIgnoreEnd
        $this->newLineHatch();
        $this->writeLog($name . ' ' . $this->getName() . ' - finished', Logger::NOTICE);
        $this->writeLog('ALL EXECUTED SUCCESS');

        return Command::SUCCESS;
    }
}
