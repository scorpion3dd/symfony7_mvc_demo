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

namespace App\Api;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use App\Entity\Admin;
use App\Entity\Comment;
use App\Entity\User;
use App\Helper\UriHelper;
use App\Util\LoggerTrait;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Log\LoggerInterface;

/**
 * Class FilterQueryCollectionExtension
 * @package App\Api
 */
class FilterQueryCollectionExtension implements QueryCollectionExtensionInterface
{
    use LoggerTrait;

    private const API_USERS_LOTTERY = '/api/users/lottery';
    private const API_ADMINS = '/api/admins';
    private const STATE_PUBLISHED = 'published';

    /**
     * @param UriHelper $uriHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly UriHelper $uriHelper,
        LoggerInterface            $logger
    ) {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param Operation|null $operation
     * @param array $context
     *
     * @return void
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->debugFunction(self::class, 'applyToCollection');
        $this->debugParameters(
            self::class,
            ['queryNameGenerator' => $queryNameGenerator instanceof QueryNameGeneratorInterface]
        );
        $aliases = $queryBuilder->getRootAliases();
        $aliasesFirst = '';
        if (isset($aliases[0])) {
            $aliasesFirst = $aliases[0];
        }
        if ($operation instanceof GetCollection) {
            $this->debugMessage(self::class, 'operation GetCollection');
            $this->queryComment($resourceClass, $context, $queryBuilder, $aliasesFirst);
            $this->queryAdmin($resourceClass, $context, $queryBuilder, $aliasesFirst);
            $this->queryUser($resourceClass, $context, $queryBuilder, $aliasesFirst);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param mixed $values
     *
     * @return void
     */
    private function queryBuilderUserLottery(QueryBuilder $queryBuilder, mixed $values): void
    {
        $this->debugParameters(self::class, ['request_uri' => self::API_USERS_LOTTERY]);
        $this->sqlBefore($queryBuilder);
        $access = 1;
        $status = 1;
        $state = self::STATE_PUBLISHED;
        $queryBuilder
//                        ->addSelect('c.author')
//                        ->addSelect('c.text')
//                        ->addSelect('count(c.user) as commentsCount')
            ->leftJoin(
                sprintf("%s.comments", $values),
                'c',
                Join::WITH,
                sprintf("c.user = %s.id and c.state = :state", $values)
            )
            ->where(sprintf("%s.access = :access", $values))
            ->andWhere(sprintf("%s.status = :status", $values))
            ->setParameter('access', $access)
            ->setParameter('status', $status)
            ->setParameter('state', $state)
//                        ->addGroupBy(sprintf("%s.id", $aliases[0]))
        ;
        $this->sqlAfter($queryBuilder);
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return void
     */
    private function sqlBefore(QueryBuilder $queryBuilder): void
    {
        $sqlBefore = $queryBuilder->getQuery()->getSQL();
        if (is_string($sqlBefore)) {
            $this->debugParameters(self::class, ['sqlBefore' => $sqlBefore]);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return void
     */
    private function sqlAfter(QueryBuilder $queryBuilder): void
    {
        $sqlAfter = $queryBuilder->getQuery()->getSQL();
        if (is_string($sqlAfter)) {
            $this->debugParameters(self::class, ['sqlAfter' => $sqlAfter]);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param mixed $values
     *
     * @return void
     */
    private function queryBuilderAdmins(QueryBuilder $queryBuilder, mixed $values): void
    {
        $this->debugParameters(self::class, ['request_uri' => self::API_ADMINS]);
        $this->sqlBefore($queryBuilder);
        $queryBuilder
            ->addSelect("rt.refreshToken as refreshToken")
            ->leftJoin(
                RefreshToken::class,
                'rt',
                Join::WITH,
                sprintf("rt.username = %s.username", $values)
            );
        $this->sqlAfter($queryBuilder);
    }

    /**
     * @param array $context
     * @param QueryBuilder $queryBuilder
     * @param mixed $values
     *
     * @return void
     */
    private function queryBuilderComment(array $context, QueryBuilder $queryBuilder, mixed $values): void
    {
        $this->debugParameters(self::class, ['request_uri' => $context['request_uri']]);
        $userId = (int)$context['filters']['userId'];
        $this->debugParameters(self::class, ['userId' => $userId]);
        $this->sqlBefore($queryBuilder);
        $state = self::STATE_PUBLISHED;
        $queryBuilder
            ->andWhere(sprintf("%s.state = :state", $values))
            ->andWhere(sprintf("%s.user = :userId", $values))
            ->setParameter('userId', $userId)
            ->setParameter('state', $state);
        $this->sqlAfter($queryBuilder);
    }

    /**
     * @param string $resourceClass
     * @param array $context
     * @param QueryBuilder $queryBuilder
     * @param string $aliasesFirst
     *
     * @return void
     */
    private function queryComment(string $resourceClass, array $context, QueryBuilder $queryBuilder, string $aliasesFirst): void
    {
        if ($resourceClass === Comment::class) {
            if (isset($context['request_uri']) && $this->uriHelper->isApiCommentsItem($context['request_uri'])
                && isset($context['filters']) && isset($context['filters']['userId'])
                && is_numeric($context['filters']['userId'])
            ) {
                $this->queryBuilderComment($context, $queryBuilder, $aliasesFirst);
            } elseif (isset($context['request_uri']) && $context['request_uri'] == self::API_USERS_LOTTERY) {
                // @codeCoverageIgnoreStart
                $this->queryBuilderUserLottery($queryBuilder, $aliasesFirst);
                // @codeCoverageIgnoreEnd
            }
        }
    }

    /**
     * @param string $resourceClass
     * @param array $context
     * @param QueryBuilder $queryBuilder
     * @param string $aliasesFirst
     *
     * @return void
     */
    private function queryAdmin(string $resourceClass, array $context, QueryBuilder $queryBuilder, string $aliasesFirst): void
    {
        if ($resourceClass === Admin::class) {
            if (isset($context['request_uri']) && $context['request_uri'] == self::API_ADMINS) {
                $this->queryBuilderAdmins($queryBuilder, $aliasesFirst);
            }
        }
    }

    /**
     * @param string $resourceClass
     * @param array $context
     * @param QueryBuilder $queryBuilder
     * @param string $aliasesFirst
     *
     * @return void
     */
    private function queryUser(string $resourceClass, array $context, QueryBuilder $queryBuilder, string $aliasesFirst): void
    {
        if (User::class === $resourceClass) {
            if (isset($context['request_uri']) && $context['request_uri'] == self::API_USERS_LOTTERY) {
                $this->queryBuilderUserLottery($queryBuilder, $aliasesFirst);
            }
        }
    }
}
