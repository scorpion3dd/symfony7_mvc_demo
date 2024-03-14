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

namespace App\CQRS\Command\CreateUser;

use DateTime;

/**
 * Class CreateUserCommand
 * @package App\CQRS\Command\CreateUser
 */
class CreateUserCommand implements CommandInterface
{
    /**
     * @param string $email
     * @param string $username
     * @param string $fullName
     * @param string $description
     * @param int $genderId
     * @param int $statusId
     * @param int $accessId
     * @param DateTime $dateBirthday
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     */
    public function __construct(
        public readonly string $email,
        public readonly string $username,
        public readonly string $fullName,
        public readonly string $description,
        public readonly int $genderId,
        public readonly int $statusId,
        public readonly int $accessId,
        public readonly DateTime $dateBirthday,
        public readonly DateTime $createdAt,
        public readonly DateTime $updatedAt
    ) {
    }
}
