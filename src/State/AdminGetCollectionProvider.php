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

namespace App\State;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\DocumentInterface;
use App\Entity\Admin;
use App\Entity\EntityInterface;
use App\Helper\UriHelper;
use App\Service\AdminServiceInterface;
use App\Service\RefreshTokenServiceInterface;
use App\Util\LoggerTrait;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AdminGetCollectionProvider
 * @package App\State
 * @implements ProviderInterface<object>
 */
class AdminGetCollectionProvider implements ProviderInterface
{
    use LoggerTrait;

    /**
     * @param AdminServiceInterface $adminService
     * @param RefreshTokenServiceInterface $refreshTokenService
     * @param UriHelper $uriHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly AdminServiceInterface $adminService,
        private readonly RefreshTokenServiceInterface $refreshTokenService,
        private readonly UriHelper $uriHelper,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * With Authorization
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return EntityInterface[]|DocumentInterface[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $this->debugFunction(self::class, 'provide');
        $message = self::class . ' provide: ';
        if ($operation instanceof GetCollection) {
            $admins = $this->adminService->findAll();
            if (! empty($admins)) {
                foreach ($admins as $key => $admin) {
                    if ($admin instanceof Admin) {
                        $refreshToken = $admin->getRefreshToken();
                        if (empty($refreshToken)) {
                            $requestUri = $operation->getUriTemplate() ?? '';
                            if ($this->uriHelper->isApiAdminsListProvide($requestUri)) {
                                /** @var RefreshToken|null $jwtRefreshToken */
                                $jwtRefreshToken = $this->refreshTokenService->getJwtRefreshToken($admin->getUsername() ?? '');
                                if (isset($jwtRefreshToken)) {
                                    $admin->setRefreshToken($jwtRefreshToken->getRefreshToken());
                                    $admins[$key] = $admin;
                                }
                            } else {
                                $message .= 'RequestUri not ApiAdminsItemProvide.';
                            }
                        }
                    }
                }
            }

            return $admins ?? [];
        } else {
            $message .= 'Operation not Get.';
        }
        $exception = new NotFoundHttpException($message);
        $this->exception($message, $exception);
        throw $exception;
    }
}
