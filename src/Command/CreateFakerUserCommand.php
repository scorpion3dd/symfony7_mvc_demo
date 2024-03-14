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
use App\Service\UserServiceInterface;
use Blackfire\Bridge\Symfony\MonitorableCommandInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateFakerUserCommand
 * @package App\Command
 */
#[AsCommand(
    name: 'app:users:create-faker-user',
    description: 'create user async',
)]
class CreateFakerUserCommand extends BaseCommand implements MonitorableCommandInterface
{
    /**
     * @param UserServiceInterface $userService
     * @param LoggerInterface $logger
     * @param ApplicationGlobals $appGlobals
     */
    public function __construct(
        private readonly UserServiceInterface $userService,
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
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->debugFunction(self::class, 'execute');
        $this->buildIo($input, $output);
        $name = "CreateFakerUserCommand";
        $this->getIo()->title($name . ' execute');
        $this->writeLog($name . ' ' . $this->getName() . ' - started', Logger::NOTICE);
        $this->newLineHatch();
        try {
            $this->writeLog('Process - ' . $this->getDescription(), Logger::DEBUG);
            $createUserCommand = $this->userService->createFakerUser();
            if (! empty($createUserCommand)) {
                $this->getIo()->text('Inputs:');
                $this->getIo()->newLine();
                $this->getIo()->text('email = ' . $createUserCommand->email);
                $this->getIo()->text('userName = ' . $createUserCommand->username);
                $this->getIo()->text('fullName = ' . $createUserCommand->fullName);
                $this->getIo()->text('statusId = ' . $createUserCommand->statusId);
                $this->getIo()->text('accessId = ' . $createUserCommand->accessId);
            }
            $this->writeLog('Results: message "createUserCommand" write to DB messenger_messages', Logger::DEBUG);
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
