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

use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Query\Query;

/**
 * Interface LogRepositoryInterface
 * @package App\Repository
 */
interface LogRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @return Query
     */
    public function findAllLogs(?string $filterField = null, ?string $filterValue = null): object;

    /**
     * @return void
     * @throws MongoDBException
     */
    public function deleteAllLogs(): void;
}
