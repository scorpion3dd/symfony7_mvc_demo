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
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Admin;
use App\Helper\JwtTokensHelper;
use App\Service\AdminServiceInterface;
use App\Service\RefreshTokenServiceInterface;
use App\Util\LoggerTrait;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AdminLoginProcessor
 * @package App\State
 * @implements ProcessorInterface<Admin, Request>
 */
class AdminLoginProcessor implements ProcessorInterface
{
    use LoggerTrait;

    /**
     * @param AdminServiceInterface $adminService
     * @param UserPasswordHasherInterface $userPasswordEncoder
     * @param JwtTokensHelper $jwtTokensHelper
     * @param RefreshTokenServiceInterface $refreshTokenService
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly AdminServiceInterface $adminService,
        private readonly UserPasswordHasherInterface $userPasswordEncoder,
        private readonly JwtTokensHelper $jwtTokensHelper,
        private readonly RefreshTokenServiceInterface $refreshTokenService,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * Without Authorization
     * @param mixed $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return Admin|Request
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->debugFunction(self::class, 'process');
        $admin = $this->adminService->findOneByLogin($data->getUsername());
        $message = self::class . ' process: ';
        if ($operation instanceof Post) {
            if (isset($admin)) {
                if (! $this->userPasswordEncoder->isPasswordValid($admin, $data->getPlainPassword())) {
                    throw new AccessDeniedHttpException();
                }
                $token = $this->jwtTokensHelper->createJwtToken($data);
                $admin->setToken($token);

                $refreshToken = $this->jwtTokensHelper->updateJwtRefreshToken($admin);
                $this->refreshTokenService->save($refreshToken, true);

                $admin->setRefreshToken($refreshToken->getRefreshToken() ?? '');

                $this->adminService->save($admin, true);

                return  $admin;
            } else {
                $message .= 'Not found Admin.';
            }
        } else {
            $message .= 'Operation not Post.';
        }
        $exception = new NotFoundHttpException($message);
        $this->exception($message, $exception);
        throw $exception;
    }
}
