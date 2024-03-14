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

use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;

/**
 * Interface RefreshTokenServiceInterface
 * @package App\Service
 */
interface RefreshTokenServiceInterface extends BaseServiceInterface
{
    /**
     * @param string $username
     *
     * @return RefreshToken|null
     */
    public function getJwtRefreshToken(string $username): ?RefreshToken;

    /**
     * @param string $refreshToken
     *
     * @return RefreshToken|null
     */
    public function getJwtRefreshTokenBy(string $refreshToken): ?RefreshToken;
}
