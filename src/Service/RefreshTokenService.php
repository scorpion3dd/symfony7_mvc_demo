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

use App\Repository\RefreshTokenRepository;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Log\LoggerInterface;

/**
 * Class RefreshTokenService
 * @package App\Service
 */
class RefreshTokenService extends BaseService implements RefreshTokenServiceInterface
{
    /**
     * @param RefreshTokenRepository $repository
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected RefreshTokenRepository $repository,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     * @param string $username
     *
     * @return RefreshToken|null
     */
    public function getJwtRefreshToken(string $username): ?RefreshToken
    {
        return $this->repository->findOneBy(['username' => $username]);
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     * @param string $refreshToken
     *
     * @return RefreshToken|null
     */
    public function getJwtRefreshTokenBy(string $refreshToken): ?RefreshToken
    {
        return $this->repository->findOneBy(['refreshToken' => $refreshToken]);
    }

    /**
     * @param RefreshTokenRepository $repository
     */
    public function setRepository(RefreshTokenRepository $repository): void
    {
        $this->repository = $repository;
    }
}
