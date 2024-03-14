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

namespace App\EventListener;

use App\Entity\Admin;
use App\Util\LoggerTrait;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Psr\Log\LoggerInterface;

/**
 * Class JWTAuthenticatedListener
 * @package App\EventListener
 */
class JWTAuthenticatedListener
{
    use LoggerTrait;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * @param JWTAuthenticatedEvent $event
     *
     * @return void
     * @throws Exception
     */
    public function onJWTAuthenticated(JWTAuthenticatedEvent $event): void
    {
        $this->debugFunction(self::class, 'onJWTAuthenticated');
        /** @var JWTPostAuthenticationToken $token */
        $token = $event->getToken();
        $tokenPost = $token->getCredentials();
        /** @var Admin $user */
        $user = $token->getUser();
        $tokenUser = $user->getToken() ?? '';
        if ($tokenPost != $tokenUser) {
            $er = new Exception('Invalid JWT Token - no such token exists', 400);
            $this->exception('Invalid JWT Token - no such token exists', $er);
            throw $er;
        }
    }
}
