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

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Interface CommentRepositoryInterface
 * @package App\Repository
 */
interface CommentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @return int
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countOldRejected(): int;

    /**
     * @return int
     * @throws Exception
     */
    public function deleteOldRejected(): int;

    /**
     * @param User $user
     * @param int $offset
     * @param int $perPage
     * @param string $state
     *
     * @return mixed
     */
    public function getComment(
        User $user,
        int $offset,
        int $perPage,
        string $state = 'published'
    ): mixed;
}
