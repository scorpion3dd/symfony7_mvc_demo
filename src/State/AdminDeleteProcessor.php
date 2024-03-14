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

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\EntityInterface;
use App\Service\AdminServiceInterface;
use App\Service\RefreshTokenServiceInterface;
use App\Util\LoggerTrait;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AdminDeleteProcessor
 * @package App\State
 * @implements ProcessorInterface<JsonResponse, Request>
 */
class AdminDeleteProcessor implements ProcessorInterface
{
    use LoggerTrait;

    /**
     * @param AdminServiceInterface $adminService
     * @param RefreshTokenServiceInterface $refreshTokenService
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly AdminServiceInterface $adminService,
        private readonly RefreshTokenServiceInterface $refreshTokenService,
        LoggerInterface $logger
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
     * @return JsonResponse|Request
     * @throws NonUniqueResultException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->debugFunction(self::class, 'process');
        $username = $data->getUsername();
        $admin = $this->adminService->findOneByLogin($username);
        $message = self::class . ' provide: ';
        if ($operation instanceof Delete) {
            if (isset($admin)) {
                /** @var EntityInterface|null $jwtRefreshToken */
                $jwtRefreshToken = $this->refreshTokenService->getJwtRefreshToken($username);
                if (isset($jwtRefreshToken)) {
                    try {
                        $this->refreshTokenService->remove($jwtRefreshToken, true);
                        $this->adminService->remove($admin, true);
                    // @codeCoverageIgnoreStart
                    } catch (Exception $ex) {
                        $this->exception(self::class . ' process', $ex);
                    }
                    // @codeCoverageIgnoreEnd
                }

                return new JsonResponse([
                    'username' => $username,
                    'status' => 'deleted',
                ]);
            } else {
                $message .= 'Not found Admin.';
            }
        } else {
            $message .= 'Operation not Delete.';
        }
        $exception = new NotFoundHttpException($message);
        $this->exception($message, $exception);
        throw $exception;
    }
}
