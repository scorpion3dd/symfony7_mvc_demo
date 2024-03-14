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

namespace App\Tests;

use App\Util\ConsoleOutputTrait;
use LogicException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Yaml\Yaml;

/**
 * Class TestSuite
 * @package App\Tests
 */
class TestSuite
{
    use ConsoleOutputTrait;

    protected const TEST_SUITE = '--testsuite=';
    protected const TEST_UNIT_ADMIN = 'UnitAdmin';
    protected const TEST_UNIT_ADMIN_FORM = 'UnitAdminForm';
    protected const TEST_UNIT = 'Unit';
    protected const TEST_INTEGRATION = 'Integration';
    protected const TEST_FUNCTIONAL = 'Functional';
    protected const PATH = '/../config/';
    protected const FILE_UNIT = 'services_test_unit.yaml';
    protected const FILE_UNIT_ADMIN = 'services_test_unit_admin.yaml';
    protected const FILE_UNIT_ADMIN_FORM = 'services_test_unit_admin_form.yaml';
    protected const FILE_INTEGRATION = 'services_test_integration.yaml';
    protected const FILE_FUNCTIONAL = 'services_test_functional.yaml';
    protected const FILE_DESTINATION = 'services_test.yaml';
    protected const COMMAND = 'php bin/console cache:clear --env=test --no-warmup --ansi';

    /** @var array|null $arguments */
    private readonly ?array $arguments;

    /**
     * @param array|null $arguments
     */
    public function __construct(?array $arguments = [])
    {
        $this->arguments = $arguments;
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
        $this->buildIo($this->input, $this->output);
        $this->getIo()->write(self::class . " construct" . PHP_EOL);
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $this->getIo()->write(self::class . " execute" . PHP_EOL);
        $bool = false;
        /** @var string $argument */
        foreach ($this->arguments as $argument) {
            if (strpos($argument, self::TEST_SUITE) === 0) {
                $destinationFile = $this->getDestinationFile();
                $config = Yaml::parseFile($destinationFile);
                //file_put_contents($filePath, Yaml::dump($config, 4, 2));
                $testSuiteName = $this->getTestSuiteName($argument);
                $this->getIo()->title("Test Suite = " . $testSuiteName);
                $sourceFile = $this->getSourceFile($testSuiteName);
                if (! $this->comparingContentFiles($sourceFile, $destinationFile)) {
                    $this->copyFile($sourceFile, $destinationFile);
                    $this->execCommandClearCache();
                }
                $bool = true;
                break;
            }
        }
        if (! $bool) {
            $this->getIo()->note('In arguments is not Test Suite.');
        }
    }

    /**
     * @param string $sourceFile
     * @param string $destinationFile
     *
     * @return void
     */
    private function copyFile(string $sourceFile, string $destinationFile): void
    {
        if (! copy($sourceFile, $destinationFile)) {
            throw new LogicException('Failed to copy file.');
        }
        $this->getIo()->comment('File copied.');
    }

    /**
     * @return void
     */
    private function execCommandClearCache(): void
    {
        $this->getIo()->write('> ' . self::COMMAND);
        $this->getIo()->comment('Clearing the cache for the test environment with debug true');
        exec(self::COMMAND, $output, $returnVar);
        if ($returnVar !== 0) {
            throw new LogicException("Command execution error: $returnVar");
        }
        $this->getIo()->success('Cache for the "test" environment (debug=true) was successfully cleared.');
    }

    /**
     * @param string $testSuiteName
     *
     * @return string
     */
    private function getSourceFile(string $testSuiteName): string
    {
        $sourceFile = '';
        if ($testSuiteName === self::TEST_UNIT) {
            $sourceFile = __DIR__ . self::PATH . self::FILE_UNIT;
            if (! is_file($sourceFile)) {
                throw new LogicException('File ' . self::FILE_UNIT . ' is missing.');
            }
        } elseif ($testSuiteName === self::TEST_UNIT_ADMIN) {
            $sourceFile = __DIR__ . self::PATH . self::FILE_UNIT_ADMIN;
            if (! is_file($sourceFile)) {
                throw new LogicException('File ' . self::FILE_UNIT_ADMIN . ' is missing.');
            }
        } elseif ($testSuiteName === self::TEST_UNIT_ADMIN_FORM) {
            $sourceFile = __DIR__ . self::PATH . self::FILE_UNIT_ADMIN_FORM;
            if (! is_file($sourceFile)) {
                throw new LogicException('File ' . self::FILE_UNIT_ADMIN_FORM . ' is missing.');
            }
        } elseif ($testSuiteName === self::TEST_INTEGRATION) {
            $sourceFile = __DIR__ . self::PATH . self::FILE_INTEGRATION;
            if (! is_file($sourceFile)) {
                throw new LogicException('File ' . self::FILE_INTEGRATION . ' is missing.');
            }
        } elseif ($testSuiteName === self::TEST_FUNCTIONAL) {
            $sourceFile = __DIR__ . self::PATH . self::FILE_FUNCTIONAL;
            if (! is_file($sourceFile)) {
                throw new LogicException('File ' . self::FILE_FUNCTIONAL . ' is missing.');
            }
        }

        return $sourceFile;
    }

    /**
     * @return string
     */
    private function getDestinationFile(): string
    {
        $destinationFile = __DIR__ . self::PATH . self::FILE_DESTINATION;
        if (! is_file($destinationFile)) {
            throw new LogicException('File ' . self::FILE_DESTINATION . ' is missing.');
        }

        return $destinationFile;
    }

    /**
     * @param string $argument
     *
     * @return string
     */
    private function getTestSuiteName(string $argument): string
    {
        return substr($argument, strlen(self::TEST_SUITE));
    }

    /**
     * @param string $sourceFile
     * @param string $destinationFile
     *
     * @return bool
     */
    private function comparingContentFiles(string $sourceFile, string $destinationFile): bool
    {
        if (file_get_contents($sourceFile) === file_get_contents($destinationFile)) {
            return true;
        }

        return false;
    }
}
