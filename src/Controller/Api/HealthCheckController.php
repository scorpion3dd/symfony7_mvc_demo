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

use App\Util\LoggerTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HealthCheckController
 * @package App\Controller\Api
 */
#[Route('/api/health-check', name: 'health_check', methods: ['GET'])]
class HealthCheckController
{
    use LoggerTrait;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    public function __invoke(): Response
    {
        $this->debugFunction(self::class, 'invoke');

        return new JsonResponse(['status' => 'ok']);
    }
}
