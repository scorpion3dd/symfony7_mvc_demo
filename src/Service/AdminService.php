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

use App\Entity\Admin;
use App\Repository\AdminRepositoryInterface;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Log\LoggerInterface;

/**
 * Class AdminService
 * @package App\Service
 */
class AdminService extends BaseService implements AdminServiceInterface
{
    /**
     * @param AdminRepositoryInterface $repository
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected AdminRepositoryInterface $repository,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @param int $adminId
     *
     * @return Admin|null
     */
    public function findOneById(int $adminId): ?Admin
    {
        /** @var Admin|null $admin */
        $admin = $this->repository->find($adminId);

        return $admin;
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     * @param string $username
     *
     * @return Admin|null
     * @throws NonUniqueResultException
     */
    public function findOneByLogin(string $username): ?Admin
    {
        return $this->repository->findOneByLogin($username);
    }

    /**
     * @return array|null
     */
    public function findAll(): ?array
    {
        return $this->repository->findAll();
    }

    /**
     * @param AdminRepositoryInterface $repository
     */
    public function setRepository(AdminRepositoryInterface $repository): void
    {
        $this->repository = $repository;
    }
}
