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

use App\Helper\ApplicationGlobals;
use App\Util\ConsoleOutputTrait;
use App\Util\LoggerTrait;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class SnakeGame
 * @package App\Command\GameSnake
 */
class SnakeGame
{
    use LoggerTrait;
    use ConsoleOutputTrait;

    const EMPTY_CELL = ' ';
    const SNAKE_CELL = 'O';
    const FOOD_CELL = '*';

    private int $width;
    private int $height;
    private Snake $snake;
    private Food $food;
    private int $score;

    /**
     * @param int $width
     * @param int $height
     * @param LoggerInterface $logger
     * @param ApplicationGlobals $appGlobals
     */
    public function __construct(int $width, int $height, LoggerInterface $logger, ApplicationGlobals $appGlobals)
    {
        $this->appGlobals = $appGlobals;
        $this->logger = $logger;
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
        $this->buildIo($this->input, $this->output);

        $this->width = $width;
        $this->height = $height;
        $this->score = 0;

        $this->snake = new Snake();
        $this->food = $this->generateFood();
    }

    /**
     * @psalm-suppress DeprecatedConstant
     *
     * @return void
     */
    public function run(): void
    {
        while (true) {
            $this->display();
            $this->processInput();

            if ($this->checkCollision()) {
                $this->echo("Game Over. Your score: {$this->score}", Logger::DEBUG);
                break;
            }

            $this->snake->move();

            if ($this->snake->collidesWithFood($this->food)) {
                // @codeCoverageIgnoreStart
                $this->score++;
                $this->snake->eatFood();
                $this->food = $this->generateFood();
                // @codeCoverageIgnoreEnd
            }

            usleep(100000);
            system('clear');
        }
    }

    /**
     * @psalm-suppress DeprecatedConstant
     *
     * @return void
     */
    private function display(): void
    {
        for ($row = 0; $row < $this->height; $row++) {
            for ($col = 0; $col < $this->width; $col++) {
                $cellContent = self::EMPTY_CELL;
                if ($this->snake->isSnakeCell($col, $row)) {
                    $cellContent = self::SNAKE_CELL;
                } elseif ($this->food->getX() == $col && $this->food->getY() == $row) {
                    $cellContent = self::FOOD_CELL;
                }
                $this->echo($cellContent, Logger::DEBUG);
            }
            $this->echo(PHP_EOL, Logger::DEBUG);
        }
        $this->echo("Score: {$this->score}", Logger::DEBUG);
    }

    /**
     * @param bool $is
     *
     * @return string
     */
    private function getInput(bool $is = false): string
    {
        $input = '';
        if ($is) {
            // @codeCoverageIgnoreStart
            /** @var resource $stdin */
            $stdin = fopen('php://stdin', 'r');
            /** @var string $input1 */
            $input1 = fgets($stdin);
            $input = trim($input1);
            fclose($stdin);
            // @codeCoverageIgnoreEnd
        }

        return $input;
    }

    /**
     * @return void
     */
    private function processInput(): void
    {
        $input = $this->getInput();
        // @codeCoverageIgnoreStart
        if ($input == 'w' && $this->snake->getDirection() != Snake::DOWN) {
            $this->snake->setDirection(Snake::UP);
        } elseif ($input == 's' && $this->snake->getDirection() != Snake::UP) {
            $this->snake->setDirection(Snake::DOWN);
        } elseif ($input == 'a' && $this->snake->getDirection() != Snake::RIGHT) {
            $this->snake->setDirection(Snake::LEFT);
        } elseif ($input == 'd' && $this->snake->getDirection() != Snake::LEFT) {
            $this->snake->setDirection(Snake::RIGHT);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return bool
     */
    private function checkCollision(): bool
    {
        $head = $this->snake->getHead();
        // Check collision with walls
        if ($head['x'] < 0 || $head['x'] >= $this->width || $head['y'] < 0 || $head['y'] >= $this->height) {
            return true;
        }
        // Check collision with self
        if ($this->snake->collidesWithSelf()) {
            // @codeCoverageIgnoreStart
            return true;
            // @codeCoverageIgnoreEnd
        }

        return false;
    }

    /**
     * @return Food
     */
    private function generateFood(): Food
    {
        do {
            $food = new Food(rand(0, $this->width - 1), rand(0, $this->height - 1));
        } while ($this->snake->collidesWithFood($food));

        return $food;
    }
}
