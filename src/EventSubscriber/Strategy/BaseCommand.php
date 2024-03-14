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

namespace App\EventSubscriber\Strategy;

use App\Helper\UriHelper;
use App\Util\LoggerTrait;
use Psr\Log\LoggerInterface;

/**
 * Abstract class BaseCommand - is part of the Strategy and Command design patterns.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Behavioral/Strategy/README.html
 * @link https://designpatternsphp.readthedocs.io/en/latest/Behavioral/Command/README.html
 * @package App\EventSubscriber\Strategy
 */
abstract class BaseCommand
{
    use LoggerTrait;

    /**
     * @param UriHelper $uriHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected readonly UriHelper $uriHelper,
        LoggerInterface $logger,
    ) {
        $this->logger = $logger;
    }
}
