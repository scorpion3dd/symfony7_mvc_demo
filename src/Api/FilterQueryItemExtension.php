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

use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use App\Entity\Admin;
use App\Entity\Comment;
use App\Helper\UriHelper;
use App\Util\LoggerTrait;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;

/**
 * Class FilterQueryItemExtension
 * @package App\Api
 */
class FilterQueryItemExtension implements QueryItemExtensionInterface
{
    use LoggerTrait;

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
     * @param array $identifiers
     * @param Operation|null $operation
     * @param array $context
     *
     * @return void
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->debugFunction(self::class, 'applyToItem');
        $this->debugParameters(
            self::class,
            ['queryNameGenerator' => $queryNameGenerator instanceof QueryNameGeneratorInterface]
        );
        $this->debugParameters(self::class, ['identifiers' => $identifiers]);
        $aliases = $queryBuilder->getRootAliases();
        if (Comment::class === $resourceClass) {
            $queryBuilder->andWhere(sprintf("%s.state = 'published'", $aliases[0]));
        } elseif (Admin::class === $resourceClass) {
            if ($operation instanceof Get) {
                $this->debugMessage(self::class, 'operation Get');
                if (isset($context['request_uri']) && $this->uriHelper->isApiAdmins($context['request_uri'])) {
                    // @codeCoverageIgnoreStart
                    $this->debugParameters(self::class, ['request_uri' => $context['request_uri']]);
                    // @codeCoverageIgnoreEnd
                }
            }
        }
    }
}
