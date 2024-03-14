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
use App\Repository\LogRepositoryInterface;
use DateTime;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;

/**
 * Class LogService
 * @package App\Service
 */
class LogService extends BaseService implements LogServiceInterface
{
    public const PAGINATOR_PER_PAGE = 8;

    /**
     * @param PaginatorInterface $paginator
     * @param LogRepositoryInterface $repository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private PaginatorInterface $paginator,
        protected LogRepositoryInterface $repository,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     * @param int $page
     * @param string|null $filterField
     * @param string|null $filterValue
     *
     * @return SlidingPagination|PaginationInterface
     */
    public function getLogsPaginator(int $page, ?string $filterField = null, ?string $filterValue = null): SlidingPagination|PaginationInterface
    {
        $query = $this->repository->findAllLogs($filterField, $filterValue);

        return $this->paginator->paginate($query, $page, self::PAGINATOR_PER_PAGE);
    }

    /**
     * @param string $id
     *
     * @return Log|null
     */
    public function getLog(string $id): ?Log
    {
        /** @var Log|null $log */
        $log = $this->repository->findOneBy(['id' => $id]);

        return $log;
    }

    /**
     * @param Log $log
     * @param mixed $logDbId
     * @param DateTime $logDbTimestamp
     *
     * @return Log
     */
    public function editLog(Log $log, mixed $logDbId, DateTime $logDbTimestamp): Log
    {
        $log->setId($logDbId);
        $log->setTimestamp($logDbTimestamp);
        $priorityList = Log::getPriorities();
        $priorityName = $priorityList[$log->getPriority()];
        $log->setPriorityName($priorityName);

        return $log;
    }

    /**
     * @param LogRepositoryInterface $repository
     */
    public function setRepository(LogRepositoryInterface $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @param PaginatorInterface $paginator
     */
    public function setPaginator(PaginatorInterface $paginator): void
    {
        $this->paginator = $paginator;
    }
}
