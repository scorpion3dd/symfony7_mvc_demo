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

namespace App\DataFixtures;

use App\DataFixtures\Builder\AppMongoFixturesBuilder;
use App\DataFixtures\Builder\Director;
use App\Document\Log;
use App\Helper\ApplicationGlobals;
use App\Util\ConsoleOutputTrait;
use App\Util\LoggerTrait;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture as MongoFixture;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class AppMongoFixtures
 * @package App\DataFixtures
 */
class AppMongoFixtures extends MongoFixture
{
    use LoggerTrait;
    use ConsoleOutputTrait;

    /**
     * @param AppMongoFixturesBuilder $builder
     * @param ApplicationGlobals $appGlobals
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly AppMongoFixturesBuilder $builder,
        ApplicationGlobals $appGlobals,
        LoggerInterface $logger,
    ) {
        $this->logger = $logger;
        $this->appGlobals = $appGlobals;
        $this->appGlobals->setType(ApplicationGlobals::TYPE_APP_FIXTURES);
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
        $this->buildIo($this->input, $this->output);
        $this->debugConstruct(self::class);
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $this->debugFunction(self::class, 'load');
        /** @var DocumentManager $manager */
        $dbName = $manager->getDocumentDatabase(Log::class)->getDatabaseName();
        $this->getIo()->title('AppMongoFixtures added items to MongoDB ' . $dbName . ':');
        $elements = (new Director())
            ->build($this->builder, $manager)
            ->getElements();
        $this->getIo()->listing($elements);
        $this->getIo()->success('ALL EXECUTED SUCCESS');
    }
}
