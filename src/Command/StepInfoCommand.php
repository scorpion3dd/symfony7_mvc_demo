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
use Blackfire\Bridge\Symfony\MonitorableCommandInterface;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Class StepInfoCommand
 * @package App\Command
 */
#[AsCommand(
    name: 'app:step:info',
    description: 'step info',
)]
class StepInfoCommand extends BaseCommand implements MonitorableCommandInterface
{
    /**
     * @param CacheInterface $cache
     * @param LoggerInterface $logger
     * @param ApplicationGlobals $appGlobals
     */
    public function __construct(
        private readonly CacheInterface $cache,
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
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->debugFunction(self::class, 'execute');
        $this->buildIo($input, $output);
        $name = "StepInfoCommand";
        $this->getIo()->title($name . ' execute');
        $this->writeLog($name . ' ' . $this->getName() . ' - started', Logger::NOTICE);
        $this->newLineHatch();
        $this->getIo()->newLine();
        try {
            $step = $this->cache->get('app.current_step', function ($item) {
                $process = new Process(['git', 'tag', '-l', '--points-at', 'HEAD']);
                $process->mustRun();
                // @codeCoverageIgnoreStart
                $item->expiresAfter(30);

                return $process->getOutput();
            });
            $output->writeln($step);
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
