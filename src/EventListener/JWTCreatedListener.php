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
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Psr\Log\LoggerInterface;

/**
 * Class JWTCreatedListener
 * @package App\EventListener
 */
class JWTCreatedListener
{
    use LoggerTrait;

    /**
     * @param LoggerInterface   $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        try {
            $this->debugFunction(self::class, 'onJWTCreated');
            /** @var Admin|null $user */
            $user = $event->getUser();
            if (isset($user)) {
                $payload = $event->getData();
                $payload['user']['profile'] = $user->getProxyForTokens();
                $event->setData($payload);
            }
        // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $mess = self::class . ' onJWTCreated ' . $ex->getMessage();
            $this->exception($mess, $ex);
        }
        // @codeCoverageIgnoreEnd
    }
}
