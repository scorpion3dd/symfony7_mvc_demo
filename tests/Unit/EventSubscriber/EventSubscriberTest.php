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

namespace App\Tests\Unit\EventSubscriber;

use ApiPlatform\Doctrine\Orm\AbstractPaginator;
use App\Entity\Admin;
use App\Entity\Comment;
use App\EventSubscriber\BaseSubscriber;
use App\EventSubscriber\EventSubscriber;
use App\Factory\AdminFactory;
use App\Helper\ApplicationGlobals;
use App\Helper\UriHelper;
use App\Service\RefreshTokenServiceInterface;
use App\Tests\Unit\BaseKernelTestCase;
use ArrayIterator;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class EventSubscriberTest - Unit tests for State EventSubscriber
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\EventSubscriber
 */
class EventSubscriberTest extends BaseKernelTestCase
{
    /** @var EventSubscriber $subscriber */
    private EventSubscriber $subscriber;

    /** @var RefreshTokenServiceInterface $refreshTokenService */
    private $refreshTokenService;

    /** @var UriHelper $uriHelper */
    private $uriHelper;

    /** @var LoggerInterface $logger */
    private $logger;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->refreshTokenService = $this->createMock(RefreshTokenServiceInterface::class);
        $this->uriHelper = $this->createMock(UriHelper::class);
        $this->adminFactory = $this->createMock(AdminFactory::class);
        $this->appGlobals = $this->createMock(ApplicationGlobals::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->subscriber = new EventSubscriber(
            $this->refreshTokenService,
            $this->uriHelper,
            $this->adminFactory,
            $this->appGlobals,
            $this->logger
        );
    }

    /**
     * @testCase - static method getSubscribedEvents - must be a success
     *
     * @return void
     */
    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = EventSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::TERMINATE, $subscribedEvents);
        $this->assertArrayHasKey(KernelEvents::RESPONSE, $subscribedEvents);
        $this->assertArrayHasKey(KernelEvents::EXCEPTION, $subscribedEvents);
        $this->assertArrayHasKey(KernelEvents::VIEW, $subscribedEvents);
        $this->assertArrayHasKey(KernelEvents::REQUEST, $subscribedEvents);
        if (class_exists(ConsoleEvents::class)) {
            $this->assertArrayHasKey(ConsoleEvents::TERMINATE, $subscribedEvents);
        }
    }

    /**
     * @testCase - method preWrite - must be a success
     *
     * @return void
     */
    public function testPreWrite(): void
    {
        $request = new Request();
        $response = new Response();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ViewEvent($kernel, $request, 0, $response);

        $this->subscriber->preWrite($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method postWrite - must be a success, result Admin
     *
     * @return void
     * @throws Exception
     */
    public function testPostWriteAdmin(): void
    {
        $admin = $this->createMock(Admin::class);
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $response = new Response();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ViewEvent($kernel, $request, 0, $response);
        $event->setControllerResult($admin);

        $this->uriHelper->expects($this->once())
            ->method('isApiAdminsItem')
            ->willReturn(true);

        $this->refreshTokenService->expects($this->once())
            ->method('getJwtRefreshToken')
            ->willReturn(new RefreshToken());

        $this->subscriber->postWrite($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method postWrite - must be a success, result KeyValueStore
     *
     * Class "EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPostWriteKeyValueStore(): void
    {
        self::markTestSkipped(self::class . ' skipped testPostWriteKeyValueStore');
        $keyValueStore = $this->createMock(KeyValueStore::class);
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $response = new Response();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ViewEvent($kernel, $request, 0, $response);
        $event->setControllerResult($keyValueStore);

        $this->subscriber->postWrite($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method postWrite - must be a success, result Paginator
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testPostWritePaginator(): void
    {
        $admin1 = $this->createAdmin('username1', 'password1');
        $data1 = [[$admin1, self::TOKEN]];
        $data2 = [$admin1];
        $iterator1 = new ArrayIterator($data1);
        $iterator2 = new ArrayIterator($data2);

        $paginator = $this->createMock(AbstractPaginator::class);
        $paginator->expects($this->exactly(2))
            ->method('getIterator')
            ->willReturnOnConsecutiveCalls($iterator1, $iterator2);

        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $response = new Response();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ViewEvent($kernel, $request, 0, $response);
        $event->setControllerResult($paginator);

        $this->uriHelper->expects($this->once())
            ->method('isApiAdmins')
            ->willReturn(true);

        $this->uriHelper->expects($this->once())
            ->method('isApiAdminsList')
            ->willReturn(true);

        $this->subscriber->postWrite($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method postWrite - must be a success, method Post
     *
     * @return void
     * @throws Exception
     */
    public function testPostWritePost(): void
    {
        $comment = $this->createMock(Comment::class);
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $response = new Response();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ViewEvent($kernel, $request, 0, $response);
        $event->setControllerResult($comment);

        $this->uriHelper->expects($this->once())
            ->method('isApiCommentsUpload')
            ->willReturn(true);

        $this->subscriber->postWrite($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method preRead - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testPreRead(): void
    {
        $request = new Request();
        $response = new Response();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ViewEvent($kernel, $request, 0, $response);

        $this->subscriber->preRead($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method postRead - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testPostRead(): void
    {
        $request = new Request();
        $response = new Response();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ViewEvent($kernel, $request, 0, $response);

        $this->subscriber->postRead($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method onKernelException - must be a success, HttpExceptionInterface
     *
     * @return void
     * @throws Exception
     */
    public function testOnKernelHttpExceptionInterface(): void
    {
        $e = $this->createMock(HttpExceptionInterface::class);
        $e->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(404);

        $request = new Request([], [], [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $requestStack = new RequestStack();
        $requestStack->push($request);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ExceptionEvent($kernel, $request, 0, $e);

        $this->subscriber->onKernelException($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method onKernelException - must be a success, Exception
     *
     * @return void
     * @throws Exception
     */
    public function testOnKernelException(): void
    {
        $e = new Exception();
        $request = new Request([], [], [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $requestStack = new RequestStack();
        $requestStack->push($request);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ExceptionEvent($kernel, $request, 0, $e);

        $this->subscriber->onKernelException($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method onTerminate - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testOnTerminate(): void
    {
        $request = new Request();
        $response = new Response();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new TerminateEvent($kernel, $request, $response);

        $this->subscriber->onTerminate($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method onResponse - must be a success, isTextHtml DOCTYPE
     *
     * @return void
     * @throws Exception
     */
    public function testOnResponseIsTextHtmlDocType(): void
    {
        $this->appGlobals->expects($this->once())
            ->method('getType')
            ->willReturn(ApplicationGlobals::TYPE_APP_WORK);

        $server = [
            'HTTP_CONTENT_TYPE' => BaseSubscriber::MIME_TEXT_HTML,
            'HTTP_ACCEPT' => BaseSubscriber::MIME_TEXT_HTML
        ];
        $request = new Request([], [], [], [], [], $server);
        $response = new Response();
        $response->setContent(BaseSubscriber::DOCTYPE . ' example');
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ResponseEvent($kernel, $request, 0, $response);

        $this->subscriber->onResponse($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method onResponse - must be a success, isTextHtml
     *
     * @return void
     * @throws Exception
     */
    public function testOnResponseIsTextHtml(): void
    {
        $this->appGlobals->expects($this->once())
            ->method('getType')
            ->willReturn(ApplicationGlobals::TYPE_APP_WORK);

        $server = [
            'HTTP_CONTENT_TYPE' => BaseSubscriber::MIME_TEXT_HTML,
            'HTTP_ACCEPT' => BaseSubscriber::MIME_TEXT_HTML
        ];
        $request = new Request([], [], [], [], [], $server);
        $response = new Response();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ResponseEvent($kernel, $request, 0, $response);

        $this->subscriber->onResponse($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @testCase - method onResponse - must be a success, else
     *
     * @dataProvider provideContent
     *
     * @param string $version
     * @param string $content
     * @param int $statusCode
     *
     * @return void
     */
    public function testOnResponse(string $version, string $content, int $statusCode): void
    {
        $this->appGlobals->expects($this->once())
            ->method('getType')
            ->willReturn(ApplicationGlobals::TYPE_APP_WORK);

        $request = new Request();
        $response = new Response();
        $response->setContent($content);
        $response->setStatusCode($statusCode);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ResponseEvent($kernel, $request, 0, $response);

        $this->subscriber->onResponse($event);
        $this->assertTrue(method_exists($this->subscriber, 'debugFunction'));
    }

    /**
     * @return iterable
     */
    public static function provideContent(): iterable
    {
        $version = '1';
        $content = '{"message":"Example","' . BaseSubscriber::HYDRA_DESCRIPTION . '":""}';
        $statusCode = 402;
        yield $version => [$version, $content, $statusCode];

        $version = '2';
        $content = '{"' . BaseSubscriber::HYDRA_DESCRIPTION . '":"Unable to generate an IRI for the item of type"}';
        yield $version => [$version, $content, $statusCode];

        $version = '3';
        $content = 'No route found';
        yield $version => [$version, $content, $statusCode];
    }
}
