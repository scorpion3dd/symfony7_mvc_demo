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

use App\CQRS\Command\CreateUser\CreateUserCommand;
use App\Entity\EntityInterface;
use Doctrine\ORM\Query\QueryException;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface UserServiceInterface
 * @package App\Service
 */
interface UserServiceInterface extends BaseServiceInterface
{
    /**
     * @return CreateUserCommand|null
     */
    public function createFakerUser(): ?CreateUserCommand;

    /**
     * @param string $email
     *
     * @return void
     */
    public function findUserByEmailQuery(string $email): void;

    /**
     * @param int $page
     *
     * @return mixed
     * @throws QueryException
     */
    public function getUsersLottery(int $page): mixed;

    /**
     * @param int $page
     * @param string $template
     *
     * @return SlidingPagination|PaginationInterface
     */
    public function getUsersPaginator(int $page, string $template);

    /**
     * @return mixed
     */
    public function getLotteryUsers(): mixed;

    /**
     * @param string $key
     * @param string $value
     *
     * @return EntityInterface|null
     */
    public function findOneByField(string $key, string $value): ?EntityInterface;

    /**
     * @param string $key
     * @param string $value
     *
     * @return EntityInterface[]
     */
    public function findByField(string $key, string $value): array;
}
