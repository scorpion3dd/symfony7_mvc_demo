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
use App\Util\ConsoleOutputTrait;
use App\Util\LoggerTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class BaseCommand
 * @package App\Command
 */
class BaseCommand extends Command
{
    use LoggerTrait;
    use ConsoleOutputTrait;

    /**
     * @param LoggerInterface $logger
     * @param ApplicationGlobals $appGlobals
     * @param string $name
     */
    public function __construct(
        LoggerInterface $logger,
        ApplicationGlobals $appGlobals,
        string $name = 'app',
    ) {
        $this->logger = $logger;
        $this->appGlobals = $appGlobals;
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
        parent::__construct($name);
    }
}
