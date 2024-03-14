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

namespace App\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class CommentDTO
 * @package App\DTO
 */
class CommentDTO
{
    /**
     * @param int $userId
     * @param string $author
     * @param string $email
     * @param string $text
     * @param UploadedFile $uploadedFile
     */
    public function __construct(
        public readonly int $userId,
        public readonly string $author,
        public readonly string $email,
        public readonly string $text,
        public readonly UploadedFile $uploadedFile,
    ) {
    }
}
