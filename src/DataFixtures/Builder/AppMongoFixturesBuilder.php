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

use App\DataFixtures\Builder\Parts\AppMongoFixture;
use App\DataFixtures\Builder\Parts\Fixtures;
use App\Factory\LogFactory;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Class AppMongoFixturesBuilder - is part of the Builder design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/Builder/README.html
 * @package App\DataFixtures\Builder
 */
class AppMongoFixturesBuilder extends BaseFixturesBuilder implements Builder
{
    /**
     * @param LogFactory $logFactory
     * @param LoggerInterface $logger
     * @param int|null $countLogs
     */
    public function __construct(
        LogFactory $logFactory,
        LoggerInterface $logger,
        #[Autowire('%app.countLogs%')] ?int $countLogs,
    ) {
        parent::__construct($logFactory, $countLogs, $logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @param ObjectManager $om
     *
     * @return Fixtures
     * @throws Exception
     */
    public function build(ObjectManager $om): Fixtures
    {
        $this->createFixtures(new AppMongoFixture());
        $this->addLogs($om);

        return $this->getFixtures();
    }
}
