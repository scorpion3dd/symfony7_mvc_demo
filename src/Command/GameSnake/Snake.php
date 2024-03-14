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

namespace App\Command\GameSnake;

/**
 * Class Snake
 * @package App\Command\GameSnake
 */
class Snake
{
    const UP = 'up';
    const DOWN = 'down';
    const LEFT = 'left';
    const RIGHT = 'right';

    private array $body;
    private string $direction;

    public function __construct()
    {
        $this->body = [
            ['x' => 5, 'y' => 5],
            ['x' => 5, 'y' => 6],
            ['x' => 5, 'y' => 7],
        ];
        $this->direction = self::UP;
    }

    /**
     * @return void
     */
    public function move(): void
    {
        $head = $this->getHead();
        switch ($this->direction) {
            case self::UP:
                array_unshift($this->body, ['x' => $head['x'], 'y' => $head['y'] - 1]);
                break;
            case self::DOWN:
            // @codeCoverageIgnoreStart
                array_unshift($this->body, ['x' => $head['x'], 'y' => $head['y'] + 1]);
                break;
            case self::LEFT:
                array_unshift($this->body, ['x' => $head['x'] - 1, 'y' => $head['y']]);
                break;
            case self::RIGHT:
                array_unshift($this->body, ['x' => $head['x'] + 1, 'y' => $head['y']]);
                break;
            // @codeCoverageIgnoreEnd
        }

        array_pop($this->body);
    }

    /**
     * @return void
     */
    public function eatFood(): void
    {
        // @codeCoverageIgnoreStart
        $tail = $this->getTail();
        array_push($this->body, $tail);
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param Food $food
     *
     * @return bool
     */
    public function collidesWithFood(Food $food): bool
    {
        return $this->getHead() == ['x' => $food->getX(), 'y' => $food->getY()];
    }

    /**
     * @return bool
     */
    public function collidesWithSelf(): bool
    {
        $head = $this->getHead();

        foreach ($this->body as $key => $segment) {
            if ($key !== 0 && $segment == $head) {
                // @codeCoverageIgnoreStart
                return true;
                // @codeCoverageIgnoreEnd
            }
        }

        return false;
    }

    /**
     * @param int $x
     * @param int $y
     *
     * @return bool
     */
    public function isSnakeCell(int $x, int $y): bool
    {
        return in_array(['x' => $x, 'y' => $y], $this->body);
    }

    /**
     * @return int[]
     */
    public function getHead(): array
    {
        return $this->body[0];
    }

    /**
     * @return false|int[]
     */
    private function getTail(): array|bool
    {
        // @codeCoverageIgnoreStart
        return end($this->body);
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $direction
     *
     * @return void
     */
    public function setDirection(string $direction): void
    {
        // @codeCoverageIgnoreStart
        $this->direction = $direction;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        // @codeCoverageIgnoreStart
        return $this->direction;
        // @codeCoverageIgnoreEnd
    }
}
