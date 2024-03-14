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

use App\Document\DocumentInterface;
use App\Entity\EntityInterface;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;

/**
 * Interface BaseServiceInterface
 * @package App\Service
 */
interface BaseServiceInterface
{
    /**
     * @param EntityInterface|DocumentInterface|RefreshToken $entity
     * @param bool $flush
     *
     * @return void
     */
    public function save(EntityInterface|DocumentInterface|RefreshToken $entity, bool $flush = false): void;

    /**
     * @param EntityInterface|DocumentInterface|RefreshTokenInterface $entity
     * @param bool $flush
     *
     * @return void
     */
    public function remove(EntityInterface|DocumentInterface|RefreshTokenInterface $entity, bool $flush = false): void;
}
