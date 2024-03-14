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

use Doctrine\ORM\Query;

/**
 * Interface UserRepositoryInterface
 * @package App\Repository
 */
interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param int $access
     * @param int $status
     * @param string $state
     *
     * @return Query
     */
    public function findUsersAccess(
        int $access = 1,
        int $status = 1,
        string $state = 'published'
    );

    /**
     * @param int $firstResult
     * @param int $itemsPerPage
     * @param int $access
     * @param int $status
     * @param string $state
     *
     * @return Query
     * @throws Query\QueryException
     */
    public function findUsersLottery(
        int $firstResult,
        int $itemsPerPage,
        int $access = 1,
        int $status = 1,
        string $state = 'published'
    );

    /**
     * @param int $access
     * @param int $status
     *
     * @return mixed
     */
    public function findUsersAccessed(int $access = 1, int $status = 1): mixed;
}
