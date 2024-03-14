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
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Psr\Log\LoggerInterface;

/**
 * Class JWTInvalidListener
 * @package App\EventListener
 */
class JWTInvalidListener
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
     * @param JWTInvalidEvent $event
     *
     * @return void
     */
    public function onJWTInvalid(JWTInvalidEvent $event): void
    {
        $this->debugFunction(self::class, 'onJWTInvalid');
        $event->setResponse($this->authorizationTool->forward());
    }
}
