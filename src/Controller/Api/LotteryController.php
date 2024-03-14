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

use App\Service\UserServiceInterface;
use App\Util\LoggerTrait;
use Doctrine\ORM\Query\QueryException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LotteryAjaxController
 * @package App\Controller\Api
 */
#[AsController]
class LotteryController extends AbstractController
{
    use LoggerTrait;

    /**
     * @param UserServiceInterface $userService
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(
        private readonly UserServiceInterface $userService,
        LoggerInterface $logger,
        ContainerInterface $container
    ) {
        $this->logger = $logger;
        $this->container = $container;
        $this->debugConstruct(self::class);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws QueryException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $this->debugFunction(self::class, 'invoke');
        $page = max(1, $request->query->getInt('page', 1));

        return new JsonResponse([
            'users_lottery' => $this->userService->getUsersLottery($page)
        ]);
    }
}
