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

namespace App\DataFixtures\Builder;

use App\DataFixtures\Builder\Parts\Fixtures;
use App\Document\Log;
use App\Factory\LogFactory;
use App\Util\LoggerTrait;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Generator;
use Psr\Log\LoggerInterface;

/**
 * Abstract class BaseFixturesBuilder - is part of the Builder design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/Builder/README.html
 * @package App\DataFixtures\Builder
 */
abstract class BaseFixturesBuilder
{
    use LoggerTrait;

    public const COUNT_LOGS = 50;
    public const COUNT_ADMINS = 30;
    public const COUNT_RESIDENT_USERS = 400;
    public const COUNT_NOT_RESIDENT_USERS = 300;

    /** @var Generator $faker */
    protected readonly Generator $faker;

    /** @var Fixtures $appFixtures */
    /** @psalm-suppress PropertyNotSetInConstructor */
    protected Fixtures $appFixtures;

    /**
     * @param LogFactory $logFactory
     * @param int|null $countLogs
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly LogFactory $logFactory,
        private ?int $countLogs,
        LoggerInterface $logger,
    ) {
        $this->logger = $logger;
        $this->faker = \Faker\Factory::create();
        if (empty($this->countLogs)) {
            $this->countLogs = self::COUNT_LOGS;
        }
    }

    /**
     * @param Fixtures $appFixtures
     *
     * @return void
     */
    protected function createFixtures(Fixtures $appFixtures): void
    {
        $this->appFixtures = $appFixtures;
    }

    /**
     * @return Fixtures
     */
    protected function getFixtures(): Fixtures
    {
        return $this->appFixtures;
    }

    /**
     * @param ObjectManager $om
     *
     * @return void
     */
    protected function flush(ObjectManager $om): void
    {
        $om->flush();
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     * @throws Exception
     */
    protected function addLogs(ObjectManager $manager): void
    {
        for ($i = 1; $i <= $this->countLogs; $i++) {
            $priority = Log::getPriorityRandom();
            $priorityList = Log::getPriorities();
            $priorityName = $priorityList[$priority];
            $log = $this->logFactory->create($this->faker->text(124), $priority, $priorityName);
            $manager->persist($log);
        }
        $manager->flush();
        $this->getFixtures()->addElement('Count Logs = ' . $this->countLogs . ';');
    }
}
