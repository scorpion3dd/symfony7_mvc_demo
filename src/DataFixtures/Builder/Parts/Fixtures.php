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

namespace App\DataFixtures\Builder\Parts;

/**
 * Abstract class Fixtures - is part of the Builder design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/Builder/README.html
 * @package App\DataFixtures\Builder\Parts
 */
abstract class Fixtures
{
    /** @var array $elements */
    private array $elements = [];

    /**
     * @param string $value
     *
     * @return void
     */
    public function addElement(string $value): void
    {
        $this->elements[] = $value;
    }

    /**
     * @return array
     */
    public function getElements(): array
    {
        return $this->elements;
    }
}
