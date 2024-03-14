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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Class ApiKeyAuthenticator
 * @package App\Security
 */
class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public const HEADER_AUTH_TOKEN = 'AUTH-TOKEN';

    /**
     * @inheritDoc
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has(self::HEADER_AUTH_TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get(self::HEADER_AUTH_TOKEN);
        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('Auth token not found (header: "{{ header }}")', [
                '{{ header }}' => self::HEADER_AUTH_TOKEN,
            ]);
        }

        return new SelfValidatingPassport(new UserBadge($apiToken));
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw $exception;
    }
}
