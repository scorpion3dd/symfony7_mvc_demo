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

use App\Entity\Admin;
use App\Factory\AdminFactory;
use App\Helper\UriHelper;
use App\Service\RefreshTokenServiceInterface;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;

/**
 * Class PaginatorCommand - is part of the Strategy and Command design patterns.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Behavioral/Strategy/README.html
 * @link https://designpatternsphp.readthedocs.io/en/latest/Behavioral/Command/README.html
 * @package App\EventSubscriber\Strategy
 */
class PaginatorCommand extends BaseCommand implements Command
{
    /**
     * @param UriHelper $uriHelper
     * @param LoggerInterface $logger
     * @param RefreshTokenServiceInterface $refreshTokenService
     * @param AdminFactory $adminFactory
     */
    public function __construct(
        UriHelper $uriHelper,
        LoggerInterface $logger,
        protected readonly RefreshTokenServiceInterface $refreshTokenService,
        private readonly AdminFactory $adminFactory
    ) {
        parent::__construct($uriHelper, $logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @param mixed $result
     * @param ViewEvent $event
     *
     * @return ViewEvent
     * @throws Exception
     */
    public function execute(mixed $result, ViewEvent $event): ViewEvent
    {
        $request = $event->getRequest();
        $this->debugParameters(self::class, ['request' => $request]);
        $requestUri = $request->getRequestUri();
        $this->debugParameters(self::class, ['requestUri' => $requestUri]);
        $event = $this->postWriteApiAdmins($requestUri, $result, $event);
        $event = $this->postWriteApiAdminsList($requestUri, $result, $event);

        return $event;
    }

    /**
     * @param string $requestUri
     * @param mixed $result
     * @param ViewEvent $event
     *
     * @return ViewEvent
     * @throws Exception
     */
    private function postWriteApiAdmins(string $requestUri, mixed $result, ViewEvent $event): ViewEvent
    {
        if ($this->uriHelper->isApiAdmins($requestUri)) {
            foreach ($result->getIterator() as &$value1) {
                $refreshTokenNew = '';
                $entity = $this->adminFactory->createEmpty();
                foreach ($value1 as $value2) {
                    if ($value2 instanceof Admin) {
                        $entity = clone $value2;
                    } elseif (is_string($value2)) {
                        $refreshTokenNew = $value2;
                    }
                }
                $refreshToken = $entity->getRefreshToken();
                if (empty($refreshToken) && $refreshTokenNew != '') {
                    $entity->setRefreshToken($refreshTokenNew);
                }
                $value1 = $entity;
            }
            $event->setControllerResult($result);
        }

        return $event;
    }

    /**
     * @param string $requestUri
     * @param mixed $result
     * @param ViewEvent $event
     *
     * @return ViewEvent
     * @throws Exception
     */
    private function postWriteApiAdminsList(string $requestUri, mixed $result, ViewEvent $event): ViewEvent
    {
        if ($this->uriHelper->isApiAdminsList($requestUri, 'list2')) {
            foreach ($result->getIterator() as $entity) {
                if ($entity instanceof Admin) {
                    $refreshToken = $entity->getRefreshToken();
                    if (empty($refreshToken)) {
                        /** @var RefreshToken|null $jwtRefreshToken */
                        $jwtRefreshToken = $this->refreshTokenService->getJwtRefreshToken($entity->getUsername() ?? '');
                        if (isset($jwtRefreshToken)) {
                            $entity->setRefreshToken($jwtRefreshToken->getRefreshToken());
                        }
                    }
                }
            }
            $event->setControllerResult($result);
        }

        return $event;
    }
}
