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

namespace App\EventListener;

use App\Util\LoggerTrait;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Final Class JwtAnAuthorizationTool
 * @package App\EventListener
 */
final class JwtAnAuthorizationTool
{
    use LoggerTrait;

    private array $acceptableRoutes = [
        'api-check-exist-email',
        'api-confirm-email',
        'api-send-email-reset',
        'api-change-password',
    ];

    /**
     * @param RequestStack $requestStack
     * @param LoggerInterface $logger
     */
    public function __construct(private readonly RequestStack $requestStack, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * @return JsonResponse
     */
    public function forward(): JsonResponse
    {
        $route = '';
        try {
            $request = $this->requestStack->getCurrentRequest();
            if (isset($request)) {
                $attributes = $request->attributes->all();
                if (in_array($attributes['_route'], $this->acceptableRoutes)) {
                    $route = $attributes['_route'];
                }
            }
        // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $route = $ex->getMessage();
        }
        // @codeCoverageIgnoreEnd
        $this->debugMessage(self::class, 'route ' . $route . ' in acceptable routes');
        $this->debugParameters(self::class, ['route' => $route]);

        return new JWTAuthenticationFailureResponse('Invalid JWT Token');
    }
}
