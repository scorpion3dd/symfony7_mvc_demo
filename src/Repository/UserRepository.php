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

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class UserRepository
 * @package App\Repository
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Retrieves all users
     * @param int $access
     * @param int $status
     * @param string $state
     *
     * @return Query
     */
    public function findUsersAccess(int $access = 1, int $status = 1, string $state = 'published')
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select(
            'u.id',
            'u.uid',
            'u.email',
            'u.fullName',
            'u.dateBirthday',
            'u.gender',
            'u.slug',
            'count(c.user) as commentsCount'
        )
            ->from(User::class, 'u')
            ->leftJoin(Comment::class, 'c', 'WITH', 'c.user = u.id and c.state = :state')
            ->where("u.access = :access")
            ->andWhere("u.status = :status")
            ->setParameter('access', $access)
            ->setParameter('status', $status)
            ->setParameter('state', $state)
            ->groupBy('u.id');

        return $queryBuilder->getQuery();
    }

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
    ) {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select(
            'u.id',
            'u.uid',
            'u.email',
            'u.fullName',
            'u.dateBirthday',
            'u.gender',
            'u.slug',
            'count(c.user) as commentsCount'
        )
            ->from(User::class, 'u')
            ->leftJoin(Comment::class, 'c', 'WITH', 'c.user = u.id and c.state = :state')
            ->where("u.access = :access")
            ->andWhere("u.status = :status")
            ->setParameter('access', $access)
            ->setParameter('status', $status)
            ->setParameter('state', $state)
            ->groupBy('u.id');
        $criteria = Criteria::create()
            ->setFirstResult($firstResult)
            ->setMaxResults($itemsPerPage);
        $queryBuilder->addCriteria($criteria);

        return $queryBuilder->getQuery();
    }

    /**
     * @param int $access
     * @param int $status
     *
     * @return mixed
     */
    public function findUsersAccessed(int $access = 1, int $status = 1): mixed
    {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select(
            'count(u.createdAt) as countCreatedAt',
            'count(u.updatedAt) as countUpdatedAt',
            'YEAR(u.createdAt) as yearAt',
        )
            ->from(User::class, 'u')
            ->where('u.createdAt IS NOT NULL')
            ->andWhere('u.updatedAt IS NOT NULL')
            ->andWhere("u.access = :access")
            ->andWhere("u.status = :status")
            ->setParameter('access', $access)
            ->setParameter('status', $status)
            ->groupBy('yearAt')
            ->orderBy('yearAt');

        return $queryBuilder->getQuery()->getResult();
    }
}
