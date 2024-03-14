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

use App\Util\LoggerTrait;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Stringable;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Monolog\Logger as LoggerAlias;

/**
 * Class LoggerTraitTest - Unit tests for State LoggerTrait
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Util
 */
class LoggerTraitTest extends TestCase
{
    /**
     * @testCase - method formErrors - must be a success
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testFormErrors(): void
    {
        $message = 'Error message 1';
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->exactly(2))
            ->method('log')
            ->with(LoggerAlias::ERROR, $message, ['FormName' => 'form1']);

        $form = $this->createMock(FormInterface::class);
        $errors = [
            new FormError($message),
            new FormError($message),
        ];
        $errorsForm = new FormErrorIterator($form, $errors);
        $form->expects($this->once())
            ->method('getName')
            ->willReturn('form1');
        $form->expects($this->once())
            ->method('getErrors')
            ->willReturn($errorsForm);

        $loggerTraitInstance = new class($loggerMock) {
            use LoggerTrait;

            /**
             * @param LoggerInterface $logger
             */
            public function __construct(LoggerInterface $logger)
            {
                $this->logger = $logger;
            }

            /**
             * @param FormInterface $form
             *
             * @return void
             */
            public function testFormErrors(FormInterface $form): void
            {
                $this->formErrors($form);
            }
        };
        $loggerTraitInstance->testFormErrors($form);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method arrayException - must be a success
     *
     * @dataProvider provideArrayException
     *
     * @param string $version
     *
     * @return void
     */
    public function testArrayException(string $version): void
    {
        $ex = new Exception('Test exception', 123);
        $loggerTraitInstance = new class() {
            use LoggerTrait;

            /**
             * @param Exception $ex
             * @param bool $stackTrace
             *
             * @return array
             */
            public function testArrayException(Exception $ex, bool $stackTrace = false): array
            {
                return $this->arrayException($ex, $stackTrace);
            }
        };
        if ($version == '1') {
            $result = $loggerTraitInstance->testArrayException($ex);
        } elseif ($version == '2') {
            $result = $loggerTraitInstance->testArrayException($ex, true);
        }
        $this->assertArrayHasKey('File', $result);
        $this->assertArrayHasKey('Line', $result);
        $this->assertArrayHasKey('Message', $result);
        $this->assertSame($ex->getFile(), $result['File']);
        $this->assertSame($ex->getLine(), $result['Line']);
        $this->assertSame($ex->getMessage(), $result['Message']);
        if ($version == '2') {
            $this->assertArrayHasKey('StackTrace', $result);
            $this->assertStringContainsString('LoggerTraitTest->testArrayException(', $result['StackTrace']);
        }
    }

    /**
     * @return iterable
     */
    public static function provideArrayException(): iterable
    {
        $version = '1';
        yield $version => [$version];

        $version = '2';
        yield $version => [$version];
    }

    /**
     * @testCase - method jsonException - must be a success
     *
     * @dataProvider provideJsonException
     *
     * @param string $version
     *
     * @return void
     */
    public function testJsonException(string $version): void
    {
        $ex = new Exception('Test exception', 123);
        $loggerTraitInstance = new class() {
            use LoggerTrait;

            /**
             * @param Exception $ex
             * @param bool $stackTrace
             *
             * @return string
             */
            public function testJsonException(Exception $ex, bool $stackTrace = false): string
            {
                return $this->jsonException($ex, $stackTrace);
            }
        };
        if ($version == '1') {
            $result = $loggerTraitInstance->testJsonException($ex);
        } elseif ($version == '2') {
            $result = $loggerTraitInstance->testJsonException($ex, true);
        }
        $this->assertJson($result);
        $json = json_decode($result, true);
        $this->assertNotEmpty($json['message']);
        $this->assertSame($ex->getMessage(), $json['message']);
        $this->assertNotEmpty($json['code']);
        $this->assertSame($ex->getCode(), $json['code']);
        $this->assertNotEmpty($json['file']);
        $this->assertSame($ex->getFile(), $json['file']);
        $this->assertNotEmpty($json['line']);
        $this->assertSame($ex->getLine(), $json['line']);
        if ($version == '2') {
            $this->assertNotEmpty($json['trace']);
            $this->assertSame($ex->getTraceAsString(), $json['trace']);
        }
    }

    /**
     * @return iterable
     */
    public static function provideJsonException(): iterable
    {
        $version = '1';
        yield $version => [$version];

        $version = '2';
        yield $version => [$version];
    }

    /**
     * @testCase - method log - must be a success
     *
     * @return void
     */
    public function testLog(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('log')
            ->with(LoggerAlias::INFO, 'Test message', ['key' => 'value']);

        $loggerTraitInstance = new class($loggerMock) {
            use LoggerTrait;

            /**
             * @param LoggerInterface $logger
             */
            public function __construct(LoggerInterface $logger)
            {
                $this->logger = $logger;
            }

            /**
             * @param string|Stringable $message
             * @param array $context
             * @param int $priority
             *
             * @return void
             */
            public function testLog(string|Stringable $message, array $context = [], int $priority = LoggerAlias::INFO): void
            {
                $this->log($message, $context, $priority);
            }
        };
        $loggerTraitInstance->testLog('Test message', ['key' => 'value']);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method emergency - must be a success
     *
     * @return void
     */
    public function testEmergency(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('log')
            ->with(LoggerAlias::EMERGENCY, 'Test message', ['key' => 'value']);

        $loggerTraitInstance = new class($loggerMock) {
            use LoggerTrait;

            /**
             * @param LoggerInterface $logger
             */
            public function __construct(LoggerInterface $logger)
            {
                $this->logger = $logger;
            }

            /**
             * @param string|Stringable $message
             * @param array $context
             *
             * @return void
             */
            public function testEmergency(string|Stringable $message, array $context = []): void
            {
                $this->emergency($message, $context);
            }
        };
        $loggerTraitInstance->testEmergency('Test message', ['key' => 'value']);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method alert - must be a success
     *
     * @return void
     */
    public function testAlert(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('log')
            ->with(LoggerAlias::ALERT, 'Test message', ['key' => 'value']);

        $loggerTraitInstance = new class($loggerMock) {
            use LoggerTrait;

            /**
             * @param LoggerInterface $logger
             */
            public function __construct(LoggerInterface $logger)
            {
                $this->logger = $logger;
            }

            /**
             * @param string|Stringable $message
             * @param array $context
             *
             * @return void
             */
            public function testAlert(string|Stringable $message, array $context = []): void
            {
                $this->alert($message, $context);
            }
        };
        $loggerTraitInstance->testAlert('Test message', ['key' => 'value']);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method critical - must be a success
     *
     * @return void
     */
    public function testCritical(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('log')
            ->with(LoggerAlias::CRITICAL, 'Test message', ['key' => 'value']);

        $loggerTraitInstance = new class($loggerMock) {
            use LoggerTrait;

            /**
             * @param LoggerInterface $logger
             */
            public function __construct(LoggerInterface $logger)
            {
                $this->logger = $logger;
            }

            /**
             * @param string|Stringable $message
             * @param array $context
             *
             * @return void
             */
            public function testCritical(string|Stringable $message, array $context = []): void
            {
                $this->critical($message, $context);
            }
        };
        $loggerTraitInstance->testCritical('Test message', ['key' => 'value']);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method error - must be a success
     *
     * @return void
     */
    public function testError(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('log')
            ->with(LoggerAlias::ERROR, 'Test message', ['key' => 'value']);

        $loggerTraitInstance = new class($loggerMock) {
            use LoggerTrait;

            /**
             * @param LoggerInterface $logger
             */
            public function __construct(LoggerInterface $logger)
            {
                $this->logger = $logger;
            }

            /**
             * @param string|Stringable $message
             * @param array $context
             *
             * @return void
             */
            public function testError(string|Stringable $message, array $context = []): void
            {
                $this->error($message, $context);
            }
        };
        $loggerTraitInstance->testError('Test message', ['key' => 'value']);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method warning - must be a success
     *
     * @return void
     */
    public function testWarning(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('log')
            ->with(LoggerAlias::WARNING, 'Test message', ['key' => 'value']);

        $loggerTraitInstance = new class($loggerMock) {
            use LoggerTrait;

            /**
             * @param LoggerInterface $logger
             */
            public function __construct(LoggerInterface $logger)
            {
                $this->logger = $logger;
            }

            /**
             * @param string|Stringable $message
             * @param array $context
             *
             * @return void
             */
            public function testWarning(string|Stringable $message, array $context = []): void
            {
                $this->warning($message, $context);
            }
        };
        $loggerTraitInstance->testWarning('Test message', ['key' => 'value']);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method notice - must be a success
     *
     * @return void
     */
    public function testNotice(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('log')
            ->with(LoggerAlias::NOTICE, 'Test message', ['key' => 'value']);

        $loggerTraitInstance = new class($loggerMock) {
            use LoggerTrait;

            /**
             * @param LoggerInterface $logger
             */
            public function __construct(LoggerInterface $logger)
            {
                $this->logger = $logger;
            }

            /**
             * @param string|Stringable $message
             * @param array $context
             *
             * @return void
             */
            public function testNotice(string|Stringable $message, array $context = []): void
            {
                $this->notice($message, $context);
            }
        };
        $loggerTraitInstance->testNotice('Test message', ['key' => 'value']);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method info - must be a success
     *
     * @return void
     */
    public function testInfo(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('log')
            ->with(LoggerAlias::INFO, 'Test message', ['key' => 'value']);

        $loggerTraitInstance = new class($loggerMock) {
            use LoggerTrait;

            /**
             * @param LoggerInterface $logger
             */
            public function __construct(LoggerInterface $logger)
            {
                $this->logger = $logger;
            }

            /**
             * @param string|Stringable $message
             * @param array $context
             *
             * @return void
             */
            public function testInfo(string|Stringable $message, array $context = []): void
            {
                $this->info($message, $context);
            }
        };
        $loggerTraitInstance->testInfo('Test message', ['key' => 'value']);
        $this->assertTrue(true);
    }
}
