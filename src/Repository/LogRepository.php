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
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Query\Query;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use App\Document\Log;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;

/**
 * Class LogRepository - This is the custom repository class for Log Document
 * @package App\Repository
 * @extends DocumentRepository<object>
 */
class LogRepository extends DocumentRepository implements LogRepositoryInterface
{
    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata(Log::class);
        parent::__construct($dm, $uow, $classMetaData);
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     * DocumentManager
     * public function createQueryBuilder($documentName = null): Query\Builder
     * Method createQueryBuilder may not return value of type Mock_QueryBuilder_d11fd6f6,
     * its declared return type is "Doctrine\ODM\MongoDB\Query\Builder"
     *
     * @param string|null $filterField
     * @param string|null $filterValue
     *
     * @return object
     */
    public function findAllLogs(?string $filterField = null, ?string $filterValue = null): object
    {
        $dm = $this->getDocumentManager();
        $queryBuilder = $dm->createQueryBuilder(Log::class);
        $queryBuilder->select([
            'id',
            'extra',
            'message',
            'priority',
            'priorityName',
            'timestamp'
        ]);
        if ($filterField && $filterValue) {
            $queryBuilder->field($filterField)->equals($filterValue);
        }

        return $queryBuilder->getQuery();
    }

    /**
     * DocumentManager
     * public function createQueryBuilder($documentName = null): Query\Builder
     * Method createQueryBuilder may not return value of type Mock_QueryBuilder_d11fd6f6,
     * its declared return type is "Doctrine\ODM\MongoDB\Query\Builder"
     *
     * @return void
     * @throws MongoDBException
     */
    public function deleteAllLogs(): void
    {
        $dm = $this->getDocumentManager();
        $dm->createQueryBuilder(Log::class)
            ->remove()
            ->getQuery()
            ->execute();
    }

    /**
     * @param EntityInterface|DocumentInterface|RefreshToken $entity
     * @param bool $flush
     *
     * @return void
     * @throws MongoDBException
     */
    public function save(EntityInterface|DocumentInterface|RefreshToken $entity, bool $flush = false): void
    {
        $dm = $this->getDocumentManager();
        $dm->persist($entity);
        if ($flush) {
            $dm->flush();
        }
    }

    /**
     * @param EntityInterface|DocumentInterface|RefreshTokenInterface $entity
     * @param bool $flush
     *
     * @return void
     * @throws MongoDBException
     */
    public function remove(EntityInterface|DocumentInterface|RefreshTokenInterface $entity, bool $flush = false): void
    {
        $dm = $this->getDocumentManager();
        $dm->remove($entity);
        if ($flush) {
            $dm->flush();
        }
    }
}
