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

use App\Entity\Admin;
use App\Helper\JwtTokensHelper;
use App\Service\AdminServiceInterface;
use App\Service\RefreshTokenServiceInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Webmozart\Assert\Assert;

/**
 * Class UserFetcher
 * @package App\Security
 */
class UserFetcher implements UserFetcherInterface
{
    /**
     * @param Security $security
     * @param AdminServiceInterface $adminService
     * @param JwtTokensHelper $jwtTokensHelper
     * @param RefreshTokenServiceInterface $refreshTokenService
     */
    public function __construct(
        private readonly Security $security,
        private readonly AdminServiceInterface $adminService,
        private readonly JwtTokensHelper $jwtTokensHelper,
        private readonly RefreshTokenServiceInterface $refreshTokenService,
    ) {
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     * @return UserInterface
     */
    public function getAuthUser(): UserInterface
    {
        /** @var UserInterface|null $user */
        $user = $this->security->getUser();

        Assert::notNull($user, 'Current user not found check security access list');
        Assert::isInstanceOf($user, UserInterface::class, sprintf('Invalid user type %s', \get_class($user)));

        return $user;
    }

    /**
     * @return Response|null
     * @throws Exception
     */
    public function logout(): ?Response
    {
        $admin = $this->getAuthUser();
        if ($admin instanceof Admin) {
            $token = $this->jwtTokensHelper->createJwtToken($admin);
            $admin->setToken($token);

            $refreshToken = $this->jwtTokensHelper->updateJwtRefreshToken($admin);
            $this->refreshTokenService->save($refreshToken, true);

            $admin->setRefreshToken($refreshToken->getRefreshToken() ?? '');

            $this->adminService->save($admin, true);
        }

        return $this->security->logout(false);
    }
}
