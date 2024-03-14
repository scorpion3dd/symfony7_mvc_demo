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
 * Class Director - is part of the Builder design pattern.
 * It knows the interface of the builder and builds a complex object
 * with the help of the builder.
 * You can also inject many builders instead of one to build more complex objects
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/Builder/README.html
 * @package App\DataFixtures\Builder
 */
class Director
{
    /**
     * @param Builder $builder
     * @param ObjectManager $om
     *
     * @return Fixtures
     */
    public function build(Builder $builder, ObjectManager $om): Fixtures
    {
        return $builder->build($om);
    }
}
