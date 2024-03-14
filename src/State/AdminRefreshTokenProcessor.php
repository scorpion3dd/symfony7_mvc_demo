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

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Admin;
use App\Enum\CodeAnswer;
use App\Factory\AdminFactory;
use App\Helper\JwtTokensHelper;
use App\Service\AdminServiceInterface;
use App\Service\RefreshTokenServiceInterface;
use App\Util\LoggerTrait;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class AdminRefreshTokenProcessor
 * @package App\State
 * @implements ProcessorInterface<Admin, Request>
 */
class AdminRefreshTokenProcessor implements ProcessorInterface
{
    use LoggerTrait;

    /**
     * @param AdminServiceInterface $adminService
     * @param RefreshTokenServiceInterface $refreshTokenService
     * @param AdminFactory $adminFactory
     * @param JwtTokensHelper $jwtTokensHelper
     * @param RequestStack $requestStack
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly AdminServiceInterface $adminService,
        private readonly RefreshTokenServiceInterface $refreshTokenService,
        private readonly AdminFactory $adminFactory,
        private JwtTokensHelper $jwtTokensHelper,
        private RequestStack $requestStack,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * Change only token for user with refreshToken without Authorization
     * @param mixed $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return Admin|Request
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $admin = $this->adminFactory->createEmpty();
        try {
            $this->debugFunction(self::class, 'process');
            $currentRequest = $this->requestStack->getCurrentRequest();
            if (isset($currentRequest)) {
                $content = $currentRequest->getContent();
                $json = json_decode($content);
                if (isset($json)) {
                    $refreshToken = $json->refreshToken;
                }
            }
            if (! empty($refreshToken)) {
                /** @var RefreshToken|null $jwtRefreshToken */
                $jwtRefreshToken = $this->refreshTokenService->getJwtRefreshTokenBy($refreshToken);
                if (isset($jwtRefreshToken)) {
                    $admin = $this->adminService->findOneByLogin($jwtRefreshToken->getUsername() ?? '');
                    if (isset($admin)) {
                        $admin->setRefreshToken($refreshToken);
                        $tokenOld = $admin->getToken();
                        $token = $this->jwtTokensHelper->createJwtToken($admin);
                        if ($tokenOld != $token) {
                            $admin->setToken($token);
                            $this->adminService->save($admin, true);
                        }
                    } else {
                        $admin = $this->adminFactory->createEmpty();
                    }
                }
            }
        // @codeCoverageIgnoreStart
        } catch (BadRequestHttpException $ex) {
            $this->exception(self::class . ' process: ', $ex);
            $this->logger->error(CodeAnswer::BAD_REQUEST['message']);
        } catch (Exception $ex) {
            $this->exception(self::class . ' process: ', $ex);
            $this->logger->error(CodeAnswer::UNKNOWN_ERROR['message']);
        }
        // @codeCoverageIgnoreEnd

        return  $admin;
    }
}
