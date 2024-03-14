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
use Doctrine\Persistence\ObjectManager;

/**
 * Interface Builder - is part of the Builder design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/Builder/README.html
 * @package App\DataFixtures\Builder
 */
interface Builder
{
    /**
     * @param ObjectManager $om
     *
     * @return Fixtures
     */
    public function build(ObjectManager $om): Fixtures;
}
