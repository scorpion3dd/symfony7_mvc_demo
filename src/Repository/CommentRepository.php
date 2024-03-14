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
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Exception;

/**
 * Class CommentRepository
 * @package App\Repository
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public const DAYS_BEFORE_REJECTED_REMOVAL = 7;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @return int
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countOldRejected(): int
    {
        return (int)$this->getOldRejectedQueryBuilder()
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int
     * @throws Exception
     */
    public function deleteOldRejected(): int
    {
        return $this->getOldRejectedQueryBuilder()->delete()->getQuery()->execute();
    }

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
    ): mixed {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->andWhere('c.state = :state')
            ->setParameter('user', $user)
            ->setParameter('state', $state)
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($offset)
            ->getQuery();
    }

    /**
     * @return QueryBuilder
     * @throws Exception
     */
    private function getOldRejectedQueryBuilder(): QueryBuilder
    {
        $dateTime = new DateTimeImmutable('-' . self::DAYS_BEFORE_REJECTED_REMOVAL . ' days');
        $dateTime = $dateTime->setTime(0, 0, 0);
        $parametersArray = [];
        $parametersArray[] = new Parameter('state_rejected', 'rejected');
        $parametersArray[] = new Parameter('state_spam', 'spam');
        $parametersArray[] = new Parameter('date', $dateTime);
        $parameters = new ArrayCollection($parametersArray);

        return $this->createQueryBuilder('c')
            ->andWhere('c.state = :state_rejected or c.state = :state_spam')
            ->andWhere('c.createdAt < :date')
            ->setParameters($parameters);
    }
}
