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

use App\DataFixtures\Builder\AppMainFixturesBuilder;
use App\DataFixtures\Builder\Director;
use App\Helper\ApplicationGlobals;
use App\Util\ConsoleOutputTrait;
use App\Util\LoggerTrait;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    use LoggerTrait;
    use ConsoleOutputTrait;

    /**
     * @param AppMainFixturesBuilder $builder
     * @param ApplicationGlobals $appGlobals
     * @param LoggerInterface $logger
     * @param KernelInterface $kernel
     *
     * @throws Exception
     */
    public function __construct(
        private readonly AppMainFixturesBuilder $builder,
        ApplicationGlobals $appGlobals,
        LoggerInterface $logger,
        KernelInterface $kernel
    ) {
        $this->builder->setEnvironment($kernel->getEnvironment());
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
        /** @var EntityManagerInterface $manager */
        $dbName = $manager->getConnection()->getDatabase();
        $this->getIo()->title('AppFixtures added items to MySql DB ' . $dbName . ' and to MongoDB learn:');
        $elements = (new Director())
            ->build($this->builder, $manager)
            ->getElements();
        $this->getIo()->listing($elements);
        $this->getIo()->success('ALL EXECUTED SUCCESS');
    }
}
