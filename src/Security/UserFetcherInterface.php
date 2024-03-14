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

namespace App\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * interface UserFetcherInterface
 * @package App\Security
 */
interface UserFetcherInterface
{
    /**
     * @return UserInterface
     */
    public function getAuthUser(): UserInterface;

    /**
     * @return Response|null
     */
    public function logout(): ?Response;
}
