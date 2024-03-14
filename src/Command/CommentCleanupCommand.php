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
use App\Service\CommentServiceInterface;
use Blackfire\Bridge\Symfony\MonitorableCommandInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommentCleanupCommand
 * @package App\Command
 */
#[AsCommand(
    name: 'app:comment:cleanup',
    description: 'Deletes rejected and spam comments from the database',
)]
class CommentCleanupCommand extends BaseCommand implements MonitorableCommandInterface
{
    /**
     * @param CommentServiceInterface $commentService
     * @param LoggerInterface $logger
     * @param ApplicationGlobals $appGlobals
     */
    public function __construct(
        private readonly CommentServiceInterface $commentService,
        LoggerInterface $logger,
        ApplicationGlobals $appGlobals
    ) {
        parent::__construct($logger, $appGlobals);
        $this->debugConstruct(self::class);
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run');
    }

    /**
     * @psalm-suppress DeprecatedConstant
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->debugFunction(self::class, 'execute');
        $this->buildIo($input, $output);
        $name = "CommentCleanupCommand";
        $this->getIo()->title($name . ' execute');
        /** @psalm-suppress DeprecatedConstant */
        $this->writeLog($name . ' ' . $this->getName() . ' - started', Logger::NOTICE);
        $this->newLineHatch();
        if ($input->getOption('dry-run')) {
            $this->getIo()->text('Dry mode enabled');
            $count = $this->commentService->countOldRejected();
        } else {
            $count = $this->commentService->deleteOldRejected();
        }
        $this->getIo()->text(sprintf('Deleted "%d" old rejected/spam comments.', $count));

        $this->newLineHatch();
        /** @psalm-suppress DeprecatedConstant */
        $this->writeLog($name . ' ' . $this->getName() . ' - finished', Logger::NOTICE);
        $this->writeLog('ALL EXECUTED SUCCESS');

        return Command::SUCCESS;
    }
}
