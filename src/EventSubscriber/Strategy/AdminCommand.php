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

namespace App\EventSubscriber\Strategy;

use App\Helper\UriHelper;
use App\Service\RefreshTokenServiceInterface;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;

/**
 * Class AdminCommand - is part of the Strategy and Command design patterns.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Behavioral/Strategy/README.html
 * @link https://designpatternsphp.readthedocs.io/en/latest/Behavioral/Command/README.html
 * @package App\EventSubscriber\Strategy
 */
class AdminCommand extends BaseCommand implements Command
{
    /**
     * @param UriHelper $uriHelper
     * @param LoggerInterface $logger
     * @param RefreshTokenServiceInterface $refreshTokenService
     */
    public function __construct(
        UriHelper $uriHelper,
        LoggerInterface $logger,
        protected readonly RefreshTokenServiceInterface $refreshTokenService,
    ) {
        parent::__construct($uriHelper, $logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @param mixed $result
     * @param ViewEvent $event
     *
     * @return ViewEvent
     */
    public function execute(mixed $result, ViewEvent $event): ViewEvent
    {
        $refreshToken = $result->getRefreshToken();
        if (empty($refreshToken)) {
            $request = $event->getRequest();
            $this->debugParameters(self::class, ['request' => $request]);
            $requestUri = $request->getRequestUri();
            $this->debugParameters(self::class, ['requestUri' => $requestUri]);
            if ($this->uriHelper->isApiAdminsItem($requestUri, 'item')) {
                /** @var RefreshToken|null $jwtRefreshToken */
                $jwtRefreshToken = $this->refreshTokenService->getJwtRefreshToken($result->getUsername() ?? '');
                if (isset($jwtRefreshToken)) {
                    $result->setRefreshToken($jwtRefreshToken->getRefreshToken());
                    $event->setControllerResult($result);
                }
            }
        }

        return $event;
    }
}
