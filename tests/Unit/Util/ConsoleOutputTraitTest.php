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

namespace App\Tests\Unit\Util;

use App\Helper\ApplicationGlobals;
use App\Util\ConsoleOutputTrait;
use PHPUnit\Framework\TestCase;
use Monolog\Logger;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ConsoleOutputTraitTest - Unit tests for State ConsoleOutputTrait
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Util
 */
class ConsoleOutputTraitTest extends TestCase
{
    /**
     * @testCase - method writeLog - must be a success
     *
     * @return void
     */
    public function testWriteLog(): void
    {
        $loggerMock = $this->createMock(Logger::class);
        $loggerMock->expects($this->once())
            ->method('log')
            ->with(Logger::WARNING, 'Test message');

        $ioMock = $this->createMock(SymfonyStyle::class);

        $traitInstance = new class($loggerMock, $ioMock) {
            use ConsoleOutputTrait;

            /**
             * @param Logger $logger
             * @param SymfonyStyle $io
             */
            public function __construct(Logger $logger, SymfonyStyle $io)
            {
                $this->logger = $logger;
                $this->io = $io;
            }

            /**
             * @param string $message
             * @param int $priority
             *
             * @return void
             */
            public function testWriteLog(string $message, int $priority = Logger::INFO): void
            {
                $this->writeLog($message, $priority);
            }
        };

        $traitInstance->testWriteLog('Test message', Logger::WARNING);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method write - must be a success
     *
     * @return void
     */
    public function testWrite(): void
    {
        $ioMock = $this->createMock(SymfonyStyle::class);
        $ioMock->expects($this->once())
            ->method('write')
            ->with('Test message');

        $traitInstance = new class($ioMock) {
            use ConsoleOutputTrait;

            /**
             * @param SymfonyStyle $io
             */
            public function __construct(SymfonyStyle $io)
            {
                $this->io = $io;
            }

            /**
             * @param string $message
             *
             * @return void
             */
            public function testWrite(string $message): void
            {
                $this->write($message);
            }
        };
        $traitInstance->testWrite('Test message');
    }

    /**
     * @testCase - method echo - must be a success
     *
     * @dataProvider provideEcho
     *
     * @param string $version
     * @param int $priority
     *
     * @return void
     */
    public function testEcho(string $version, int $priority): void
    {
        $messageTest = 'Test message';
        $logger = $this->createMock(Logger::class);
        $logger->expects($this->once())
            ->method('log')
            ->with($priority, $messageTest);

        $appGlobals = $this->createMock(ApplicationGlobals::class);
        $appGlobals->expects($this->once())
            ->method('getType')
            ->willReturn(ApplicationGlobals::TYPE_APP_TESTS);

        $traitInstance = new class($logger, $appGlobals) {
            use ConsoleOutputTrait;

            /**
             * @param Logger $logger
             * @param ApplicationGlobals $appGlobals
             */
            public function __construct(Logger $logger, ApplicationGlobals $appGlobals)
            {
                $this->logger = $logger;
                $this->appGlobals = $appGlobals;
            }

            /**
             * @param string $message
             * @param int $priority
             *
             * @return void
             */
            public function testEcho(string $message, int $priority = Logger::INFO): void
            {
                $this->echo($message, $priority);
            }
        };
        ob_start();
        $traitInstance->testEcho($messageTest, $priority);
        $output = ob_get_clean();
        $this->assertStringContainsString($messageTest, $output);
    }

    /**
     * @return iterable
     */
    public static function provideEcho(): iterable
    {
        $version = '1';
        $priority = Logger::ERROR;
        yield $version => [$version, $priority];

        $version = '2';
        $priority = Logger::WARNING;
        yield $version => [$version, $priority];

        $version = '3';
        $priority = Logger::DEBUG;
        yield $version => [$version, $priority];

        $version = '4';
        $priority = Logger::NOTICE;
        yield $version => [$version, $priority];

        $version = '5';
        $priority = Logger::INFO;
        yield $version => [$version, $priority];
    }

    /**
     * @testCase - method buildProgressBar - must be a success
     *
     * @return void
     */
    public function testBuildProgressBar(): void
    {
        $outputMock = $this->createMock(OutputInterface::class);
        $count = 10;

        $traitInstance = new class($outputMock, $count) {
            use ConsoleOutputTrait;

            /**
             * @param OutputInterface $output
             * @param int $count
             */
            public function __construct(OutputInterface $output, int $count)
            {
                $this->output = $output;
                $this->count = $count;
            }

            /**
             * @return void
             */
            public function testBuildProgressBar(): void
            {
                $this->buildProgressBar($this->output, $this->count);
            }

            /**
             * @return ProgressBar
             */
            public function testGetProgressBar(): ProgressBar
            {
                return $this->getProgressBar();
            }
        };
        $traitInstance->testBuildProgressBar();
        $progressBar = $traitInstance->testGetProgressBar();
        $this->assertInstanceOf(ProgressBar::class, $progressBar);
        $this->assertSame($count, $progressBar->getMaxSteps());
        $this->assertSame('<fg=magenta>=</>', $progressBar->getBarCharacter());
        $this->assertSame("\xF0\x9F\x8D\xBA", $progressBar->getProgressCharacter());
    }
}
