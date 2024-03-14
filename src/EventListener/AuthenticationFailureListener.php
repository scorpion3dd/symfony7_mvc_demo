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

use App\Util\LoggerTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Psr\Log\LoggerInterface;

/**
 * Class AuthenticationFailureListener
 * @package App\EventListener
 */
class AuthenticationFailureListener
{
    use LoggerTrait;

    /**
     * @param JwtAnAuthorizationTool $authorizationTool
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly JwtAnAuthorizationTool $authorizationTool,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * @param AuthenticationFailureEvent $event
     *
     * @return void
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event): void
    {
        $this->debugFunction(self::class, 'onAuthenticationFailureResponse');
        $event->setResponse($this->authorizationTool->forward());
    }
}
