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
use App\Service\UserServiceInterface;
use App\Util\LoggerTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserGetCollectionProvider
 * @package App\State
 * @implements ProviderInterface<object>
 */
class UserGetCollectionProvider implements ProviderInterface
{
    use LoggerTrait;

    /**
     * @param UserServiceInterface $userService
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly UserServiceInterface $userService,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * Without Authorization
     * @psalm-suppress ImplementedReturnTypeMismatch
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return array
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $this->debugFunction(self::class, 'provide');
        $message = self::class . ' provide: ';
        $users = $this->userService->getLotteryUsers();
        if ($operation instanceof GetCollection) {
            return $users;
        } else {
            $message .= 'Operation not Get.';
        }
        $exception = new NotFoundHttpException($message);
        $this->exception($message, $exception);
        throw $exception;
    }
}
