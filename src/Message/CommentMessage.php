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

namespace App\Message;

/**
 * Class CommentMessage
 * @package App\Message
 */
class CommentMessage
{
    /**
     * @param int $id
     * @param string $reviewUrl
     * @param array $context
     */
    public function __construct(
        private int $id,
        private string $reviewUrl,
        private array $context = [],
    ) {
    }

    /**
     * @return string
     */
    public function getReviewUrl(): string
    {
        return $this->reviewUrl;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
