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

use Exception;
use Monolog\Logger as LoggerAlias;
use Psr\Log\LoggerInterface;
use Stringable;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Throwable;

/**
 * Trait LoggerTrait
 * @package App\Util
 */
trait LoggerTrait
{
    protected LoggerInterface $logger;

    /**
     * @psalm-suppress DeprecatedConstant
     * @param FormInterface $form
     *
     * @return void
     */
    protected function formErrors(FormInterface $form): void
    {
        $formName = $form->getName();
        $errors = $form->getErrors();
        foreach ($errors as $error) {
            /** @var FormError $error */
            $this->logger->log(LoggerAlias::ERROR, $error->getMessage(), ['FormName' => $formName]);
        }
    }

    /**
     * @param Exception $ex
     *
     * @return string
     */
    protected function stringException(Exception $ex): string
    {
        return 'Exception message - ' . $ex->getMessage()
            . ', in file - ' . $ex->getFile()
            . ', in line - ' . $ex->getLine();
    }

    /**
     * @param Exception $ex
     * @param bool $stackTrace
     *
     * @return array
     */
    protected static function arrayException(Exception $ex, bool $stackTrace = false): array
    {
        $return = [
            'File' => $ex->getFile(),
            'Line' => $ex->getLine(),
            'Message' => $ex->getMessage(),
        ];
        if ($stackTrace) {
            $return['StackTrace'] = $ex->getTraceAsString();
        }

        return $return;
    }

    /**
     * @param Exception|Throwable $exception
     * @param bool $stackTrace
     *
     * @return string|false
     */
    protected function jsonException(Exception|Throwable $exception, bool $stackTrace = false): string|false
    {
        $return = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
        if ($stackTrace) {
            $return['trace'] = $exception->getTraceAsString();
        }

        return json_encode($return);
    }

    /**
     * @psalm-suppress DeprecatedConstant
     * @param string|Stringable $message
     * @param array $context
     * @param int $priority
     *
     * @return void
     */
    protected function log(string|Stringable $message, array $context = [], int $priority = LoggerAlias::INFO): void
    {
        $this->logger->log($priority, $message, $context);
    }

    /**
     * @psalm-suppress DeprecatedConstant
     * @param string|Stringable $message
     * @param Exception $ex
     *
     * @return void
     */
    protected function exception(string|Stringable $message, Exception $ex): void
    {
        $this->logger->log(LoggerAlias::ERROR, 'Exception ' . $message, $this->arrayException($ex));
    }

    /**
     * System is unusable.
     *
     * @psalm-suppress DeprecatedConstant
     * @param string|Stringable $message
     * @param array  $context
     *
     * @return void
     */
    protected function emergency(string|Stringable $message, array $context = []): void
    {
        $this->logger->log(LoggerAlias::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @psalm-suppress DeprecatedConstant
     * @param string|Stringable $message
     * @param array  $context
     *
     * @return void
     */
    protected function alert(string|Stringable $message, array $context = []): void
    {
        $this->logger->log(LoggerAlias::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @psalm-suppress DeprecatedConstant
     * @param string|Stringable $message
     * @param array $context
     *
     * @return void
     */
    protected function critical(string|Stringable $message, array $context = []): void
    {
        $this->logger->log(LoggerAlias::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @psalm-suppress DeprecatedConstant
     * @param string|Stringable $message
     * @param array $context
     *
     * @return void
     */
    protected function error(string|Stringable $message, array $context = []): void
    {
        $this->logger->log(LoggerAlias::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @psalm-suppress DeprecatedConstant
     * @param string|Stringable $message
     * @param array  $context
     *
     * @return void
     */
    protected function warning(string|Stringable $message, array $context = []): void
    {
        $this->logger->log(LoggerAlias::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @psalm-suppress DeprecatedConstant
     * @param string|Stringable $message
     * @param array  $context
     *
     * @return void
     */
    protected function notice(string|Stringable $message, array $context = []): void
    {
        $this->logger->log(LoggerAlias::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @psalm-suppress DeprecatedConstant
     * @param string|Stringable $message
     * @param array  $context
     *
     * @return void
     */
    protected function info(string|Stringable $message, array $context = []): void
    {
        $this->logger->log(LoggerAlias::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @psalm-suppress DeprecatedConstant
     * @param string|Stringable $message
     * @param array  $context
     *
     * @return void
     */
    protected function debug(string|Stringable $message, array $context = []): void
    {
        $this->logger->log(LoggerAlias::DEBUG, $message, $context);
    }

    /**
     * @param string $class
     *
     * @return void
     */
    protected function debugConstruct(string $class): void
    {
        $this->debug($class . ' construct');
    }

    /**
     * @param string $class
     * @param string $function
     *
     * @return void
     */
    protected function debugFunction(string $class, string $function): void
    {
        $this->debug($class . ' function ' . $function);
    }

    /**
     * @param string $class
     * @param array $parameters
     *
     * @return void
     */
    protected function debugParameters(string $class, array $parameters): void
    {
        $this->debug($class, $parameters);
    }

    /**
     * @param string $class
     * @param string $message
     *
     * @return void
     */
    protected function debugMessage(string $class, string $message): void
    {
        $this->debug($class . ' message: ' . $message);
    }
}
