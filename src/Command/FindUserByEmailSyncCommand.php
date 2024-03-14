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

use App\CQRS\Query\FindUserByEmail\FindUserByEmailQuery;
use App\CQRS\Query\FindUserByEmail\FindUserByEmailQueryHandler;
use App\Helper\ApplicationGlobals;
use Blackfire\Bridge\Symfony\MonitorableCommandInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Class FindUserByEmailSyncCommand
 * @package App\Command
 */
#[AsCommand(
    name: 'app:users:find-user-by-email-sync',
    description: 'find user by email sync',
)]
class FindUserByEmailSyncCommand extends BaseCommand implements MonitorableCommandInterface
{
    /**
     * @param FindUserByEmailQueryHandler $findUserHandler
     * @param string $appDomain
     * @param LoggerInterface $logger
     * @param ApplicationGlobals $appGlobals
     */
    public function __construct(
        private readonly FindUserByEmailQueryHandler $findUserHandler,
        #[Autowire('%app.domain%')] private string $appDomain,
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
        $name = "FindUserByEmailSyncCommand";
        $this->getIo()->title($name . ' execute');
        $this->writeLog($name . ' ' . $this->getName() . ' - started', Logger::NOTICE);
        $this->newLineHatch();
        try {
            $this->writeLog('Process - ' . $this->getDescription(), Logger::DEBUG);
            $this->getIo()->text('Inputs:');
            $this->getIo()->newLine();
            $email = "resident-1@{$this->appDomain}";
            $this->getIo()->text('email = ' . $email);
            $this->getIo()->newLine();
            ob_start();
            $query = new FindUserByEmailQuery($email);
            $userDTO = $this->findUserHandler->__invoke($query);
            $output = ob_get_clean();
            $output = is_string($output) ? $output : '';
            $this->writeln($output);
            $this->writeLog('Results: userDTO id = ' . $userDTO->id, Logger::DEBUG);
        // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $this->writeLog('Exception: ' . $ex->getMessage(), Logger::ERROR);
            ob_end_clean();
        }
        // @codeCoverageIgnoreEnd
        $this->newLineHatch();
        $this->writeLog($name . ' ' . $this->getName() . ' - finished', Logger::NOTICE);
        $this->writeLog('ALL EXECUTED SUCCESS');

        return Command::SUCCESS;
    }
}
