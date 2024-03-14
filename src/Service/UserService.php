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

use App\CQRS\Bus\CommandBus;
use App\CQRS\Bus\CommandBusInterface;
use App\CQRS\Bus\QueryBus;
use App\CQRS\Command\CreateUser\CreateUserCommand;
use App\CQRS\Query\FindUserByEmail\FindUserByEmailQuery;
use App\CQRS\Bus\QueryBusInterface;
use App\Entity\EntityInterface;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\Query\QueryException;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;

/**
 * Class UserService
 * @package App\Service
 */
class UserService extends BaseService implements UserServiceInterface
{
    public const PAGINATOR_PER_PAGE = 5;

    /**
     * @param PaginatorInterface $paginator
     * @param CommandBusInterface $commandBus
     * @param QueryBusInterface $queryBus
     * @param UserRepositoryInterface $repository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private PaginatorInterface $paginator,
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
        protected UserRepositoryInterface $repository,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @return CreateUserCommand|null
     */
    public function createFakerUser(): ?CreateUserCommand
    {
        $command = null;
        try {
            $email = $this->getFaker()->email();
            $userName = $this->getFaker()->userName();
            $genderId = User::randomGenderId();
            $gender = $genderId == User::GENDER_MALE_ID ? User::GENDER_MALE : User::GENDER_FEMALE;
            $fullName = $this->getFaker()->name($gender);
            $description = $this->getFaker()->text(1024);
            $statusId = User::randomStatusId();
            $accessId = User::randomAccessId();
            $dateBirthday = $this->getFaker()->dateTimeBetween('-50 years', '-20 years');
            $createdAt = $this->getFaker()->dateTimeBetween('-15 years', 'now');
            $updatedAt = $this->getFaker()->dateTimeBetween($createdAt, 'now');
            $command = new CreateUserCommand($email, $userName, $fullName, $description, $genderId, $statusId, $accessId, $dateBirthday, $createdAt, $updatedAt);

            $this->commandBus->execute($command);
            // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $this->stringException($ex);
            // @codeCoverageIgnoreEnd
        }

        return $command;
    }

    /**
     * @param string $email
     *
     * @return void
     */
    public function findUserByEmailQuery(string $email): void
    {
        try {
            $query = new FindUserByEmailQuery($email);
            $this->queryBus->execute($query);
            // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $this->stringException($ex);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @return Generator
     */
    private function getFaker(): Generator
    {
        return Factory::create();
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     * @param int $page
     *
     * @return mixed
     * @throws QueryException
     */
    public function getUsersLottery(int $page): mixed
    {
        $firstResult = ($page - 1) * self::PAGINATOR_PER_PAGE;
        $query = $this->repository->findUsersLottery($firstResult, self::PAGINATOR_PER_PAGE);

        return $query->getResult();
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     * @param int $page
     * @param string $template
     *
     * @return SlidingPagination|PaginationInterface
     */
    public function getUsersPaginator(int $page, string $template)
    {
        $query = $this->repository->findUsersAccess();
        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate($query, $page, self::PAGINATOR_PER_PAGE);
        $pagination->setTemplate($template);
        $pagination->setPageRange(self::PAGINATOR_PER_PAGE);

        return $pagination;
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     * @return mixed
     */
    public function getLotteryUsers(): mixed
    {
        $query = $this->repository->findUsersAccess();

        return $query->getResult();
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return EntityInterface|null
     */
    public function findOneByField(string $key, string $value): ?EntityInterface
    {
        /** @var EntityInterface|null $result */
        $result = $this->repository->findOneBy([$key => $value]);

        return $result;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return EntityInterface[]
     */
    public function findByField(string $key, string $value): array
    {
        /** @var EntityInterface[] $result */
        $result = $this->repository->findBy([$key => $value]);

        return $result;
    }

    /**
     * @param CommandBus $commandBus
     */
    public function setCommandBus(CommandBus $commandBus): void
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @param QueryBus $queryBus
     */
    public function setQueryBus(QueryBus $queryBus): void
    {
        $this->queryBus = $queryBus;
    }

    /**
     * @param UserRepositoryInterface $repository
     */
    public function setRepository(UserRepositoryInterface $repository): void
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
