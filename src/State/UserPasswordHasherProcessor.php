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
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Admin;
use App\Helper\JwtTokensHelper;
use App\Service\RefreshTokenServiceInterface;
use App\Util\LoggerTrait;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserPasswordHasherProcessor
 * @package App\State
 * @implements ProcessorInterface<mixed, Request>
 */
class UserPasswordHasherProcessor implements ProcessorInterface
{
    use LoggerTrait;

    /**
     * @param ProcessorInterface $processor
     * @param UserPasswordHasherInterface $passwordHasher
     * @param RefreshTokenServiceInterface $refreshTokenService
     * @param JwtTokensHelper $jwtTokensHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ProcessorInterface          $processor,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly RefreshTokenServiceInterface $refreshTokenService,
        private JwtTokensHelper                      $jwtTokensHelper,
        LoggerInterface                              $logger
    ) {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * With Authorization
     * @param mixed $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return Request
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->debugFunction(self::class, 'provide');
        if ($data instanceof Admin) {
            if (! $data->getPlainPassword()) {
                return $this->processor->process($data, $operation, $uriVariables, $context);
            }
            /** @phpstan-ignore-next-line */
            $plainPassword = $data->getPlainPassword() ?? '';
            $hashedPassword = $this->passwordHasher->hashPassword($data, $plainPassword);
            $data->setPassword($hashedPassword);
            $data->eraseCredentials();
            // If this is an operation to create a new user, then generate a token and assign a default role
            if ($operation instanceof Post || $operation instanceof Patch) {
                $token = $this->jwtTokensHelper->createJwtToken($data);
                $data->setToken($token);

                $refreshToken = $this->jwtTokensHelper->createJwtRefreshToken($data);
                $this->refreshTokenService->save($refreshToken, true);

                $data->setRefreshToken($refreshToken->getRefreshToken() ?? '');

                $data->setRoles($data->getRoles());
            }
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
