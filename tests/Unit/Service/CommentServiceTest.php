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

namespace App\Tests\Unit\Service;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Helper\SpamChecker;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\Repository\CommentRepositoryInterface;
use App\Service\CommentService;
use App\Tests\Unit\BaseKernelTestCase;
use App\Tests\Unit\Interface\NotifierInterfaceMock;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentServiceTest - Unit tests for service CommentService
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Service
 */
class CommentServiceTest extends BaseKernelTestCase
{
    /** @var CommentService $commentService */
    private CommentService $commentService;

    /** @var CommentRepositoryInterface|null $repository */
    private ?CommentRepositoryInterface $repository;

    /** @var LoggerInterface|null $logger */
    private ?LoggerInterface $logger;

    /** @var MessageBusInterface|null $bus */
    private ?MessageBusInterface $bus;

    /** @var NotifierInterface|null $notifier */
    private ?NotifierInterface $notifier;

    /** @var TranslatorInterface|null $translator */
    private ?TranslatorInterface $translator;

    /** @var SpamChecker|null $spamChecker */
    private ?SpamChecker $spamChecker;

    /** @var WorkflowInterface|null $commentStateMachine */
    private ?WorkflowInterface $commentStateMachine;

    /** @var string $photoDir */
    private string $photoDir;

    /** @var FormInterface|null $form */
    private ?FormInterface $form;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->bus = $this->container->get(MessageBusInterface::class);
        $this->notifier = $this->container->get(NotifierInterface::class);
        $this->translator = $this->container->get(TranslatorInterface::class);
        $this->spamChecker = $this->container->get(SpamChecker::class);
        $this->repository = $this->container->get(CommentRepositoryInterface::class);
        $this->logger = $this->container->get(LoggerInterface::class);
        $this->prepareDbMySqlMock();
        $this->commentService = new CommentService(
            $this->entityManager,
            $this->bus,
            $this->notifier,
            $this->translator,
            $this->spamChecker,
            $this->repository,
            $this->logger
        );
    }

    /**
     * @testCase - method find - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testFind(): void
    {
        $messageId = 1;
        $user = $this->createUser();
        $comment = $this->createComment($user);
        $repositoryMock = $this->getMockBuilder(CommentRepository::class)
            ->onlyMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo($messageId))
            ->willReturn($comment);
        $this->commentService->setRepository($repositoryMock);

        $commentNew = $this->commentService->find($messageId);
        $this->assertInstanceOf(Comment::class, $commentNew);
        $this->assertIsString($commentNew->getAuthor());
        $this->assertGreaterThan(5, strlen($commentNew->getAuthor()));
    }

    /**
     * @testCase - method deleteOldRejected - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testDeleteOldRejected(): void
    {
        $messageId = 1;
        $repositoryMock = $this->getMockBuilder(CommentRepository::class)
            ->onlyMethods(['deleteOldRejected'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('deleteOldRejected')
            ->willReturn($messageId);
        $this->commentService->setRepository($repositoryMock);

        $messageIdNew = $this->commentService->deleteOldRejected();
        $this->assertIsInt($messageIdNew);
        $this->assertEquals($messageId, $messageIdNew);
    }

    /**
     * @testCase - method countOldRejected - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testCountOldRejected(): void
    {
        $count = 10;
        $repositoryMock = $this->getMockBuilder(CommentRepository::class)
            ->onlyMethods(['countOldRejected'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('countOldRejected')
            ->willReturn($count);
        $this->commentService->setRepository($repositoryMock);

        $countNew = $this->commentService->countOldRejected();
        $this->assertIsInt($countNew);
        $this->assertEquals($count, $countNew);
    }

    /**
     * @testCase - method getCommentPaginator - must be a success
     * return Paginator
     *
     * @return void
     * @throws Exception
     */
    public function testGetCommentPaginator(): void
    {
        $parametersArray = [];
        $parameters = new ArrayCollection($parametersArray);
        $queryMock = $this->createMock(QueryBuilder::class);
        $queryMock->setParameters($parameters);
        $offset = 10;
        $state = 'ham';
        $user = $this->createUser();
        $repositoryMock = $this->getMockBuilder(CommentRepository::class)
            ->onlyMethods(['getComment'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('getComment')
            ->with(
                $this->equalTo($user),
                $this->equalTo($offset),
                $this->equalTo(CommentService::PAGINATOR_PER_PAGE),
                $this->equalTo($state),
            )
            ->willReturn($queryMock);
        $this->commentService->setRepository($repositoryMock);

        $paginator = $this->commentService->getCommentPaginator($user, $offset, $state);
        $this->assertInstanceOf(Paginator::class, $paginator);
    }

    /**
     * @testCase - method getCommentPaginator - must be a success
     * return null
     *
     * @return void
     * @throws Exception
     */
    public function testGetCommentPaginatorNull(): void
    {
        $queryMock = null;
        $offset = 10;
        $state = 'ham';
        $user = $this->createUser();
        $repositoryMock = $this->getMockBuilder(CommentRepository::class)
            ->onlyMethods(['getComment'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('getComment')
            ->with(
                $this->equalTo($user),
                $this->equalTo($offset),
                $this->equalTo(CommentService::PAGINATOR_PER_PAGE),
                $this->equalTo($state),
            )
            ->willReturn($queryMock);
        $this->commentService->setRepository($repositoryMock);

        $paginator = $this->commentService->getCommentPaginator($user, $offset, $state);
        $this->assertNull($paginator);
    }

    /**
     * @testCase - method reviewComment - must be a success
     *
     * @dataProvider provideReviewComment
     *
     * @param string $expected
     * @param string $version
     * @param bool $accepted
     * @param string $state
     *
     * @return void
     * @throws Exception
     */
    public function testReviewComment(string $expected, string $version, bool $accepted, string $state): void
    {
        $reviewUrl = '/admin/comment/review/1';
        $user = $this->createUser();
        $comment = $this->createComment($user);
        $comment->setState($state);
        if ($version == '1' || $version == '3') {
            $entityManagerMock = $this->getMockBuilder(EntityManager::class)
                ->onlyMethods(['flush'])
                ->disableOriginalConstructor()
                ->getMock();
            $entityManagerMock->expects($this->exactly(1))
                ->method('flush');
            $this->commentService->setEntityManager($entityManagerMock);

            $message = new CommentMessage($comment->getId() ?? 0, $reviewUrl);
            $envelope = Envelope::wrap($message);
            $busMock = $this->getMockBuilder(MessageBus::class)
                ->onlyMethods(['dispatch'])
                ->disableOriginalConstructor()
                ->getMock();
            $busMock->expects($this->exactly(1))
                ->method('dispatch')
                ->willReturn($envelope);
            $this->commentService->setBus($busMock);
        }
        $transition = null;
        try {
            $this->commentStateMachine = $this->container->get('state_machine.comment');
            $transition = $this->commentService->reviewComment($comment, $accepted, $reviewUrl, $this->commentStateMachine);
        } catch (Exception $ex) {
            if (empty($this->commentStateMachine)) {
                $transition = $this->commentService->reviewComment($comment, $accepted, $reviewUrl);
                $this->assertEquals('', $transition);
            }
        }
        $this->assertIsString($transition);
        $this->assertEquals($expected, $transition);
    }

    /**
     * @return iterable
     */
    public static function provideReviewComment(): iterable
    {
        $version = '1';
        $accepted = true;
        $state = 'ham';
        $expected = 'publish_ham';
        yield $version => [$expected, $version, $accepted, $state];

        $version = '2';
        $accepted = true;
        $state = 'spam';
        $expected = '';
        yield $version => [$expected, $version, $accepted, $state];

        $version = '3';
        $accepted = true;
        $state = 'potential_spam';
        $expected = 'publish';
        yield $version => [$expected, $version, $accepted, $state];
    }

    /**
     * @testCase - method savePhotoFile - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testSavePhotoFile(): void
    {
        $this->photoDir = $this->container->getParameter('app.photoDir');
        $user = $this->createUser();
        $comment = $this->createComment($user);
        $this->form = $this->createForm(CommentFormType::class, $comment);
        $path = __DIR__ . '/../data/Service/CommentService/';
        $fullFileNameFrom = $path . 'london1.jpg';
        $localFile = $path . 'london_album.jpg';
        copy($fullFileNameFrom, $localFile);
        if (file_exists($localFile)) {
            $photo = new UploadedFile(
                $localFile,
                'london1.jpg',
                'image/jpeg',
                null,
                true
            );
            $this->form['photo']->setData($photo);
        }
        $commentNew = $this->commentService->savePhotoFile($this->form, $comment, $this->photoDir);
        $this->assertInstanceOf(Comment::class, $commentNew);
    }

    /**
     * @testCase - method savePhotoFile - must be a FileException
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testSavePhotoFileFileException(): void
    {
        $this->photoDir = '';
        $user = $this->createUser();
        $comment = $this->createComment($user);
        $this->form = $this->createForm(CommentFormType::class, $comment);
        $localFile = __DIR__ . '/../data/Service/CommentService/london1.jpg';
        if (file_exists($localFile)) {
            $photo = new UploadedFile(
                $localFile,
                'london1.jpg',
                'image/jpeg',
                null,
                true
            );
            $this->form['photo']->setData($photo);
        }
        $commentNew = $this->commentService->savePhotoFile($this->form, $comment, $this->photoDir);
        $this->assertInstanceOf(Comment::class, $commentNew);
    }

    /**
     * @testCase - method savePhotoFileApi - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testSavePhotoFileApi(): void
    {
        $this->photoDir = $this->container->getParameter('app.photoDir');
        $user = $this->createUser();
        $comment = $this->createComment($user);
        $path = __DIR__ . '/../data/Service/CommentService/';
        $fullFileNameFrom = $path . 'london1.jpg';
        $localFile = $path . 'london_album.jpg';
        copy($fullFileNameFrom, $localFile);
        if (file_exists($localFile)) {
            $photo = new UploadedFile(
                $localFile,
                'london1.jpg',
                'image/jpeg',
                null,
                true
            );
            $commentNew = $this->commentService->savePhotoFileApi($photo, $comment, $this->photoDir);
            $this->assertInstanceOf(Comment::class, $commentNew);
        }
        $this->assertTrue(true);
    }

    /**
     * @testCase - method savePhotoFileApi - must be a FileException
     *
     * @return void
     * @throws Exception
     */
    public function testSavePhotoFileApiFileException(): void
    {
        $this->photoDir = '';
        $user = $this->createUser();
        $comment = $this->createComment($user);
        $localFile = __DIR__ . '/../data/Service/CommentService/london1.jpg';
        if (file_exists($localFile)) {
            $photo = new UploadedFile(
                $localFile,
                'london1.jpg',
                'image/jpeg',
                null,
                true
            );
            $commentNew = $this->commentService->savePhotoFileApi($photo, $comment, $this->photoDir);
            $this->assertInstanceOf(Comment::class, $commentNew);
        }
        $this->assertTrue(true);
    }

    /**
     * @testCase - method sendNotificationMessage - must be a success
     *
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws Exception
     */
    public function testSendNotificationMessage(): void
    {
        $reviewUrl = '/admin/comment/review/1';
        $user = $this->createUser();
        $comment = $this->createComment($user);
        $context = [
            'user_ip' => '1.1.1.1',
            'user_agent' => 'Chrome',
            'referrer' => '/admin',
            'permalink' => '/admin/users',
        ];

        $spamCheckerMock = $this->getMockBuilder(SpamChecker::class)
            ->onlyMethods(['getSpamScore'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamCheckerMock->expects($this->exactly(1))
            ->method('getSpamScore')
            ->with(
                $this->equalTo($comment),
                $this->equalTo($context)
            )
            ->willReturn(1);
        $this->commentService->setSpamChecker($spamCheckerMock);

        $message = new CommentMessage($comment->getId() ?? 0, $reviewUrl);
        $envelope = Envelope::wrap($message);
        $busMock = $this->getMockBuilder(MessageBus::class)
            ->onlyMethods(['dispatch'])
            ->disableOriginalConstructor()
            ->getMock();
        $busMock->expects($this->exactly(1))
            ->method('dispatch')
            ->willReturn($envelope);
        $this->commentService->setBus($busMock);

        $notifierMock = $this->getMockBuilder(NotifierInterface::class)
            ->onlyMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();
        $notifierMock->expects($this->exactly(1))
            ->method('send');
        $this->commentService->setNotifier($notifierMock);

        $this->commentService->sendNotificationMessage($context, $comment, $reviewUrl);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method sendAdminRecipients - must be a success
     *
     * @return void
     */
    public function testSendAdminRecipients(): void
    {
        $notification = new Notification($this->faker->text(100));
        $adminRecipients = [];
        $notifierMock = $this->getMockBuilder(NotifierInterfaceMock::class)
            ->onlyMethods(['send', 'getAdminRecipients'])
            ->disableOriginalConstructor()
            ->getMock();
        $notifierMock->expects($this->exactly(1))
            ->method('send');
        $notifierMock->expects($this->exactly(1))
            ->method('getAdminRecipients')
            ->willReturn($adminRecipients);
        $this->commentService->setNotifier($notifierMock);

        $this->commentService->sendAdminRecipients($notification);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method send - must be a success
     *
     * @return void
     */
    public function testSend(): void
    {
        $notification = new Notification($this->faker->text(100));
        $notifierMock = $this->getMockBuilder(NotifierInterface::class)
            ->onlyMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();
        $notifierMock->expects($this->exactly(1))
            ->method('send');
        $this->commentService->setNotifier($notifierMock);

        $this->commentService->send($notification);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method sendNotificationMessage - must be a RuntimeException
     *
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws Exception
     */
    public function testSendNotificationMessageRuntimeException(): void
    {
        $reviewUrl = '/admin/comment/review/1';
        $user = $this->createUser();
        $comment = $this->createComment($user);
        $context = [
            'user_ip' => '1.1.1.1',
            'user_agent' => 'Chrome',
            'referrer' => '/admin',
            'permalink' => '/admin/users',
        ];

        $spamCheckerMock = $this->getMockBuilder(SpamChecker::class)
            ->onlyMethods(['getSpamScore'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamCheckerMock->expects($this->exactly(1))
            ->method('getSpamScore')
            ->with(
                $this->equalTo($comment),
                $this->equalTo($context)
            )
            ->willReturn(2);
        $this->commentService->setSpamChecker($spamCheckerMock);

        $this->commentService->sendNotificationMessage($context, $comment, $reviewUrl);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method isProd - must be a success
     *
     * @return void
     */
    public function testIsProd(): void
    {
        $isProd = $this->commentService->isProd($this->getKernelTest());
        $this->assertFalse($isProd);
    }
}
