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

use App\Document\DocumentInterface;
use App\Entity\EntityInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;

/**
 * Class BaseRepository
 * @package App\Repository
 * @template T of object
 */
abstract class BaseRepository extends ServiceEntityRepository implements BaseRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry
     * @param class-string<T> $entityClass
     */
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @param EntityInterface|DocumentInterface|RefreshToken $entity
     * @param bool $flush
     *
     * @return void
     */
    public function save(EntityInterface|DocumentInterface|RefreshToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param EntityInterface|DocumentInterface|RefreshTokenInterface $entity
     * @param bool $flush
     *
     * @return void
     */
    public function remove(EntityInterface|DocumentInterface|RefreshTokenInterface $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
