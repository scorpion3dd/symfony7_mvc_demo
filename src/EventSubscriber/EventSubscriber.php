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

namespace App\EventSubscriber;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Admin;
use App\Entity\Comment;
use App\EventSubscriber\Strategy\AdminCommand;
use App\EventSubscriber\Strategy\CommentCommand;
use App\EventSubscriber\Strategy\PaginatorCommand;
use App\EventSubscriber\Strategy\Strategy;
use App\Factory\AdminFactory;
use App\Helper\ApplicationGlobals;
use App\Helper\UriHelper;
use App\Service\RefreshTokenServiceInterface;
use App\Util\LoggerTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EventSubscriber
 * @package App\EventSubscriber
 */
class EventSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    use LoggerTrait;

    /**
     * @param RefreshTokenServiceInterface $refreshTokenService
     * @param UriHelper $uriHelper
     * @param AdminFactory $adminFactory
     * @param ApplicationGlobals $appGlobals
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly RefreshTokenServiceInterface $refreshTokenService,
        private readonly UriHelper $uriHelper,
        private readonly AdminFactory $adminFactory,
        private readonly ApplicationGlobals $appGlobals,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents()
    {
        $listeners = [
            KernelEvents::TERMINATE => 'onTerminate',
            KernelEvents::RESPONSE => 'onResponse',
            KernelEvents::EXCEPTION => 'onKernelException',
            KernelEvents::VIEW => ['postWrite', EventPriorities::POST_WRITE],
            KernelEvents::REQUEST => ['postRead', EventPriorities::POST_READ],
        ];
        if (class_exists('Symfony\Component\Console\ConsoleEvents')) {
            $listeners[ConsoleEvents::TERMINATE] = 'onTerminate';
        }

        return $listeners;
    }

    /**
     * @param ViewEvent $event
     *
     * @return void
     */
    public function preWrite(ViewEvent $event): void
    {
        $this->debugFunction(self::class, 'preWrite');
        $response = $event->getResponse();
        $this->debugParameters(self::class, ['response' => $response]);
        $method = $event->getRequest()->getMethod();
        $this->debugParameters(self::class, ['method' => $method]);
    }

    /**
     * @param ViewEvent $event
     *
     * @return void
     * @throws Exception
     */
    public function postWrite(ViewEvent $event): void
    {
        $this->debugFunction(self::class, 'postWrite');
        $method = $event->getRequest()->getMethod();
        $this->debugParameters(self::class, ['method' => $method]);
        $result = $event->getControllerResult();
        $this->debugParameters(self::class, ['result' => $result]);
        if ($method == Request::METHOD_GET) {
            if ($result instanceof Admin) {
                $command = new AdminCommand($this->uriHelper, $this->logger, $this->refreshTokenService);
            }
            if ($result instanceof KeyValueStore) {
                $pageName = $result->get('pageName');
                $this->debugParameters(self::class, ['pageName' => $pageName]);
                if ($this->appGlobals->getType() == ApplicationGlobals::TYPE_APP_TESTS) {
                    // @codeCoverageIgnoreStart
                    $templateName = $result->get('templateName');
                    $this->debugParameters(self::class, ['templateName' => $templateName]);
                    // @codeCoverageIgnoreEnd
                }
            }
            if ($result instanceof Paginator) {
                $command = new PaginatorCommand(
                    $this->uriHelper,
                    $this->logger,
                    $this->refreshTokenService,
                    $this->adminFactory
                );
            }
        } elseif ($method == Request::METHOD_POST) {
            if ($result instanceof Comment) {
                $command = new CommentCommand($this->uriHelper, $this->logger);
            }
        }
        if (! empty($command)) {
            $event = (new Strategy($command))->postWrite($result, $event);
        }
    }

    /**
     * @param RequestEvent $event
     *
     * @return void
     */
    public function preRead(RequestEvent $event): void
    {
        $this->debugFunction(self::class, 'preRead');
        $response = $event->getResponse();
        $this->debugParameters(self::class, ['response' => $response]);
        $method = $event->getRequest()->getMethod();
        $this->debugParameters(self::class, ['method' => $method]);
    }

    /**
     * @param RequestEvent $event
     *
     * @return void
     */
    public function postRead(RequestEvent $event): void
    {
        $this->debugFunction(self::class, 'postRead');
        $response = $event->getResponse();
        $this->debugParameters(self::class, ['response' => $response]);
        $method = $event->getRequest()->getMethod();
        $this->debugParameters(self::class, ['method' => $method]);
    }

    /**
     * @param ExceptionEvent $event
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $this->debugFunction(self::class, 'onKernelException');
        $acceptHeader = $event->getRequest()->headers->get('Accept');
        if ($this->isJson($acceptHeader)) {
            $exception = $event->getThrowable();
            $response = new JsonResponse();
            $response->setContent($this->jsonException($exception) ?: null);
            if ($exception instanceof HttpExceptionInterface) {
                $response->setStatusCode($exception->getStatusCode());
                $response->headers->replace($exception->getHeaders());
            } else {
                $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $event->setResponse($response);
        }
    }

    /**
     * @param TerminateEvent $event
     *
     * @return void
     */
    public function onTerminate($event): void
    {
        $this->debugFunction(self::class, 'onTerminate');
        $this->debugParameters(self::class, ['event' => $event]);
    }

    /**
     * @param ResponseEvent $event
     *
     * @return void
     */
    public function onResponse(ResponseEvent $event): void
    {
        $this->debugFunction(self::class, 'onResponse');
        if ($this->appGlobals->getType() == ApplicationGlobals::TYPE_APP_WORK) {
            $request = $event->getRequest();
            $response = $event->getResponse();
            $acceptHeader = $request->headers->get('Accept');
            $contentType = $response->headers->get('content-type');
            $this->debugParameters(self::class, ['contentType' => $contentType]);
            $statusCode = $response->getStatusCode();
            $this->debugParameters(self::class, ['statusCode' => $statusCode]);
            $content = $response->getContent();
            $content = $content ?: '';
            if ($this->isTextHtml($contentType, $acceptHeader)) {
                $content = $this->buildContentText($content);
            } else {
                $result = $this->buildContentJson($statusCode, $response, $request, $content);
                $content = $result['content'];
                $response = $result['response'];
                $event->setResponse($response);
            }
            $this->debugParameters(self::class, ['content' => $content]);
        }
    }
}
