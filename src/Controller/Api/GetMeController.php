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

namespace App\Controller\Api;

use App\Entity\Admin;
use App\Security\UserFetcherInterface;
use App\Util\LoggerTrait;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetMeController
 * @package App\Controller\Api
 */
#[Route('/api/me', name: 'users_me', methods: ['GET'])]
#[AsController]
class GetMeController extends AbstractController
{
    use LoggerTrait;

    /**
     * @param UserFetcherInterface $userFetcher
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(
        private readonly UserFetcherInterface $userFetcher,
        LoggerInterface $logger,
        ContainerInterface $container
    ) {
        $this->logger = $logger;
        $this->container = $container;
        $this->debugConstruct(self::class);
    }

    /**
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        $this->debugFunction(self::class, 'invoke');
        /** @var Admin $user */
        $user = $this->userFetcher->getAuthUser();

        return new JsonResponse([
            'ulid' => $user->getId(),
            'identifier' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }
}
