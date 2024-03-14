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

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Admin;
use App\Helper\UriHelper;
use App\Service\AdminServiceInterface;
use App\Service\RefreshTokenServiceInterface;
use App\Util\LoggerTrait;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AdminGetProvider
 * @package App\State
 * @implements ProviderInterface<Admin>
 */
class AdminGetProvider implements ProviderInterface
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
     * @return Admin
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Admin
    {
        $this->debugFunction(self::class, 'provide');
        $adminId = $uriVariables['id'] ?? 0;
        $admin = $this->adminService->findOneById($adminId);
        $message = self::class . ' provide: ';
        if ($operation instanceof Get) {
            if (isset($admin)) {
                $refreshToken = $admin->getRefreshToken();
                if (empty($refreshToken)) {
                    $requestUri = $operation->getUriTemplate() ?? '';
                    if ($this->uriHelper->isApiAdminsItemProvide($requestUri)) {
                        /** @var RefreshToken|null $jwtRefreshToken */
                        $jwtRefreshToken = $this->refreshTokenService->getJwtRefreshToken($admin->getUsername() ?? '');
                        if (isset($jwtRefreshToken)) {
                            $admin->setRefreshToken($jwtRefreshToken->getRefreshToken());
                        }

                        return $admin;
                    } else {
                        $message .= 'RequestUri not ApiAdminsItemProvide.';
                    }
                }
            } else {
                $message .= 'Not found Admin.';
            }
        } else {
            $message .= 'Operation not Get.';
        }
        $exception = new NotFoundHttpException($message);
        $this->exception($message, $exception);
        throw $exception;
    }
}
