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

use AllowDynamicProperties;
use App\Document\DocumentInterface;
use App\Entity\EntityInterface;
use App\Repository\BaseRepositoryInterface;
use App\Util\LoggerTrait;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseService
 * @package App\Service
 * @property BaseRepositoryInterface $repository
 */
#[AllowDynamicProperties]
abstract class BaseService implements BaseServiceInterface
{
    use LoggerTrait;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param EntityInterface|DocumentInterface|RefreshToken $entity
     * @param bool $flush
     *
     * @return void
     */
    public function save(EntityInterface|DocumentInterface|RefreshToken $entity, bool $flush = false): void
    {
        $this->repository->save($entity, $flush);
    }

    /**
     * @param EntityInterface|DocumentInterface|RefreshTokenInterface $entity
     * @param bool $flush
     *
     * @return void
     */
    public function remove(EntityInterface|DocumentInterface|RefreshTokenInterface $entity, bool $flush = false): void
    {
        $this->repository->remove($entity, $flush);
    }
}
