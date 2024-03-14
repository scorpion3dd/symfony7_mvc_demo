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

namespace App\CQRS\DTO;

use App\Entity\User;

/**
 * Class UserDTO
 * @package App\CQRS\DTO
 */
class UserDTO
{
    /**
     * @param int $id
     * @param string $uid
     * @param string $username
     * @param string $email
     * @param string $fullName
     * @param string $description
     * @param int $status
     * @param int $access
     */
    public function __construct(
        public readonly int $id,
        public readonly string $uid,
        public readonly string $username,
        public readonly string $email,
        public readonly string $fullName,
        public readonly string $description,
        public readonly int $status,
        public readonly int $access,
    ) {
    }

    /**
     * @psalm-suppress MismatchingDocblockReturnType
     * @param User $user
     *
     * @return UserDTO|static
     */
    public static function fromEntity(User $user): UserDTO|static
    {
        return new self(
            $user->getId() ?? 0,
            $user->getUid() ?? '',
            $user->getUsername() ?? '',
            $user->getEmail() ?? '',
            $user->getFullName() ?? '',
            $user->getDescription() ?? '',
            $user->getStatus() ?? 0,
            $user->getAccess() ?? 0,
        );
    }
}
