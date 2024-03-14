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

namespace App\Util;

use App\Helper\ApplicationGlobals;
use Monolog\Logger;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Trait ConsoleOutputTrait
 * @package App\Util
 */
trait ConsoleOutputTrait
{
    /** @var ApplicationGlobals $appGlobals */
    protected ApplicationGlobals $appGlobals;

    /** @var InputInterface $input */
    protected InputInterface $input;

    /** @var OutputInterface $output */
    protected OutputInterface $output;

    /** @var SymfonyStyle $io */
    /** @psalm-suppress PropertyNotSetInConstructor */
    protected SymfonyStyle $io;

    /** @var ProgressBar $progressBar */
    /** @psalm-suppress PropertyNotSetInConstructor */
    protected ProgressBar $progressBar;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function buildIo(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @psalm-suppress DeprecatedConstant
     * @param string $message
     * @param int $priority
     *
     * @return void
     */
    protected function writeLog(string $message, int $priority = Logger::INFO): void
    {
        $this->logger->log($priority, $message);
        switch ($priority) {
            case Logger::ERROR:
                $this->io->error($message);
                break;
            case Logger::WARNING:
                $this->io->warning($message);
                break;
            case Logger::DEBUG:
                $this->io->comment($message);
                break;
            case Logger::NOTICE:
                $this->io->note($message);
                break;
            case Logger::INFO:
            default:
                $this->io->success($message);
                break;
        }
    }

    /**
     * @param string $message
     * @return void
     */
    protected function writeln(string $message): void
    {
        $this->io->writeln($message);
    }

    /**
     * @param string $message
     * @return void
     */
    protected function write(string $message): void
    {
        $this->io->write($message);
    }

    /**
     * @psalm-suppress DeprecatedConstant
     * @param string $message
     * @param int $priority
     *
     * @return void
     */
    protected function echo(string $message, int $priority = Logger::INFO): void
    {
        $this->logger->log($priority, $message);
        switch ($priority) {
            case Logger::ERROR:
                $message = '[ERROR] ' . $message;
                break;
            case Logger::WARNING:
                $message = '[WARNING] ' . $message;
                break;
            case Logger::DEBUG:
                $message = '// ' . $message;
                break;
            case Logger::NOTICE:
                $message = '! [NOTE] ' . $message;
                break;
            case Logger::INFO:
            default:
                $message = ' ' . $message;
                break;
        }
        if ($this->appGlobals->getType() != ApplicationGlobals::TYPE_APP_TESTS_HIDE) {
            echo $message . PHP_EOL;
        }
    }

    /**
     * @param OutputInterface $output
     * @param int $count
     *
     * @return void
     */
    protected function buildProgressBar(OutputInterface $output, int $count): void
    {
        $this->progressBar = new ProgressBar($output, $count);
        $this->progressBar->setBarCharacter('<fg=magenta>=</>');
        $this->progressBar->setProgressCharacter("\xF0\x9F\x8D\xBA");
    }

    /**
     * @return SymfonyStyle
     */
    protected function getIo(): SymfonyStyle
    {
        return $this->io;
    }

    /**
     * @return ProgressBar
     */
    protected function getProgressBar(): ProgressBar
    {
        return $this->progressBar;
    }

    /**
     * @param string $symbol
     * @param int $count
     *
     * @return void
     */
    protected function newLineHatch(string $symbol = '=', int $count = 30): void
    {
        $this->io->write(str_repeat($symbol, $count));
    }
}
