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

namespace App\Service;

use App\Document\Log;
use DateTime;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface LogServiceInterface
 * @package App\Service
 */
interface LogServiceInterface extends BaseServiceInterface
{
    /**
     * @param int $page
     * @param string|null $filterField
     * @param string|null $filterValue
     *
     * @return SlidingPagination|PaginationInterface
     */
    public function getLogsPaginator(int $page, ?string $filterField = null, ?string $filterValue = null): SlidingPagination|PaginationInterface;

    /**
     * @param string $id
     *
     * @return Log|null
     */
    public function getLog(string $id): ?Log;

    /**
     * @param Log $log
     * @param mixed $logDbId
     * @param DateTime $logDbTimestamp
     *
     * @return Log
     */
    public function editLog(Log $log, mixed $logDbId, DateTime $logDbTimestamp): Log;
}
