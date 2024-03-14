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

namespace App\Helper;

use App\Entity\Admin;
use DateTime;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

/**
 * Class JwtTokensHelper
 * @package App\Helper
 */
class JwtTokensHelper
{
    /** @var JWTTokenManagerInterface $jwtManager */
    private JWTTokenManagerInterface $jwtManager;

    /** @var RefreshTokenGeneratorInterface $refreshJwtManager */
    public RefreshTokenGeneratorInterface $refreshJwtManager;

    /** @var int $ttl */
    protected int $ttl = 3600 * 24 * 7;

    /**
     * @param JWTTokenManagerInterface     $jwtManager
     * @param RefreshTokenGeneratorInterface $refreshJwtManager
     */
    public function __construct(JWTTokenManagerInterface $jwtManager, RefreshTokenGeneratorInterface $refreshJwtManager)
    {
        $this->jwtManager = $jwtManager;
        $this->refreshJwtManager = $refreshJwtManager;
    }

    /**
     * @param Admin $admin
     *
     * @return string
     */
    public function createJwtToken(Admin $admin): string
    {
        return $this->jwtManager->create($admin);
    }

    /**
     * @param Admin $admin
     *
     * @return RefreshToken
     *
     * @throws Exception
     */
    public function createJwtRefreshToken(Admin $admin): RefreshToken
    {
        /** @var RefreshToken $refreshToken */
        $refreshToken = $this->refreshJwtManager->createForUserWithTtl($admin, $this->ttl);
        $refreshToken->setUsername($admin->getUsername());
        $datetime = new DateTime();
        $datetime->modify('+' . $this->ttl . ' seconds');
        $refreshToken->setValid($datetime);

        return $refreshToken;
    }

    /**
     * @param Admin $admin
     *
     * @return RefreshToken
     *
     * @throws Exception
     */
    public function updateJwtRefreshToken(Admin $admin): RefreshToken
    {
        /** @var RefreshToken $refreshToken */
        $refreshToken = $this->refreshJwtManager->createForUserWithTtl($admin, $this->ttl);
        $datetime = new DateTime();
        $datetime->modify('+' . $this->ttl . ' seconds');
        $refreshToken->setValid($datetime);

        return $refreshToken;
    }

    /**
     * @param RefreshTokenGeneratorInterface $refreshJwtManager
     */
    public function setRefreshJwtManager(RefreshTokenGeneratorInterface $refreshJwtManager): void
    {
        $this->refreshJwtManager = $refreshJwtManager;
    }
}
