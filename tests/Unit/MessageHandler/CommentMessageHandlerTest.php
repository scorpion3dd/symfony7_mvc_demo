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

namespace App\Tests\Unit\MessageHandler;

use App\Entity\Comment;
use App\Helper\ApplicationGlobals;
use App\Helper\ImageOptimizer;
use App\Helper\SendgridSender;
use App\Helper\SlackSender;
use App\Helper\SpamChecker;
use App\Message\CommentMessage;
use App\MessageHandler\CommentMessageHandler;
use App\Notification\CommentReviewNotification;
use App\Service\CommentServiceInterface;
use App\Tests\Unit\BaseKernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class CommentMessageHandlerTest - Unit tests for State CommentMessageHandler
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\MessageHandler
 */
class CommentMessageHandlerTest extends BaseKernelTestCase
{
    /** @var string $photoDir */
    private string $photoDir;

    /** @var CommentMessageHandler $handler */
    private CommentMessageHandler $handler;

    /** @var CommentServiceInterface $commentServiceMock */
    private CommentServiceInterface $commentServiceMock;

    /** @var WorkflowInterface $commentStateMachineMock */
    private WorkflowInterface $commentStateMachineMock;

    /** @var MessageBusInterface $busMock */
    private MessageBusInterface $busMock;

    /** @var Comment $comment */
    protected Comment $comment;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|\PHPUnit\Framework\MockObject\Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->appGlobals->setType(ApplicationGlobals::TYPE_APP_TESTS_HIDE);
        $this->photoDir = $this->container->getParameter('app.photoDir');
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $spamChecker = $this->createMock(SpamChecker::class);
        $this->commentServiceMock = $this->createMock(CommentServiceInterface::class);
        $this->busMock = $this->createMock(MessageBusInterface::class);
        $slack = $this->createMock(SlackSender::class);
        $sendgrid = $this->createMock(SendgridSender::class);
        $imageOptimizer = $this->createMock(ImageOptimizer::class);
        $logger = $this->createMock(LoggerInterface::class);
        $this->commentStateMachineMock = $this->createMock(WorkflowInterface::class);
        $this->handler = new CommentMessageHandler(
            $entityManager,
            $spamChecker,
            $this->commentServiceMock,
            $this->busMock,
            $slack,
            $sendgrid,
            $imageOptimizer,
            $this->photoDir,
            $logger,
            $this->appGlobals,
            $this->commentStateMachineMock
        );
    }

    /**
     * @testCase - method invoke - must be a success, commentStateMachine accept
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testInvokeAccept(): void
    {
        $commentId = 1;
        $reviewUrl = '/admin/comment/review/' . $commentId;
        $user = $this->createUser();
        $this->comment = $this->createComment($user);
        $this->comment->setId($commentId);
        $message = new CommentMessage($commentId, $reviewUrl);

        $this->commentServiceMock->expects($this->once())
            ->method('find')
            ->with($commentId)
            ->willReturn($this->comment);

        $this->commentStateMachineMock->expects($this->once())
            ->method('can')
            ->with($this->comment, 'accept')
            ->willReturn(true);

        $this->handler->__invoke($message);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method invoke - must be a success, commentStateMachine publish publish
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testInvokePublishPublish(): void
    {
        $commentId = 1;
        $reviewUrl = '/admin/comment/review/' . $commentId;
        $user = $this->createUser();
        $this->comment = $this->createComment($user);
        $this->comment->setId($commentId);
        $message = new CommentMessage($commentId, $reviewUrl);

        $this->commentServiceMock->expects($this->once())
            ->method('find')
            ->with($commentId)
            ->willReturn($this->comment);

        $this->commentStateMachineMock->expects($this->exactly(4))
            ->method('can')
            ->willReturnCallback(fn($comment, $status) => match ([$comment, $status]) {
                [$this->comment, 'accept'] => false,
                [$this->comment, 'publish'] => true,
                [$this->comment, 'publish_ham'] => false,
            });

        $notification = new CommentReviewNotification($this->comment, $message->getReviewUrl());
        $this->commentServiceMock->expects($this->once())
            ->method('sendAdminRecipients')
            ->with($notification);

        $this->handler->__invoke($message);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method invoke - must be a success, commentStateMachine publish publish_ham
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testInvokePublishPublishHam(): void
    {
        $commentId = 1;
        $reviewUrl = '/admin/comment/review/' . $commentId;
        $user = $this->createUser();
        $this->comment = $this->createComment($user);
        $this->comment->setId($commentId);
        $message = new CommentMessage($commentId, $reviewUrl);

        $this->commentServiceMock->expects($this->once())
            ->method('find')
            ->with($commentId)
            ->willReturn($this->comment);

        $this->commentStateMachineMock->expects($this->exactly(4))
            ->method('can')
            ->willReturnCallback(fn($comment, $status) => match ([$comment, $status]) {
                [$this->comment, 'accept'] => false,
                [$this->comment, 'publish'] => true,
                [$this->comment, 'publish_ham'] => true,
            });

        $notification = new CommentReviewNotification($this->comment, $message->getReviewUrl());
        $this->commentServiceMock->expects($this->once())
            ->method('sendAdminRecipients')
            ->with($notification);

        $this->handler->__invoke($message);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method invoke - must be a success, commentStateMachine optimize
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testInvokeOptimize(): void
    {
        $commentId = 1;
        $reviewUrl = '/admin/comment/review/' . $commentId;
        $user = $this->createUser();
        $this->comment = $this->createComment($user);
        $this->comment->setId($commentId);
        $this->comment->setPhotoFilename('photo.jpg');
        $message = new CommentMessage($commentId, $reviewUrl);

        $this->commentServiceMock->expects($this->once())
            ->method('find')
            ->with($commentId)
            ->willReturn($this->comment);

        $this->commentStateMachineMock->expects($this->exactly(4))
            ->method('can')
            ->willReturnCallback(fn($comment, $status) => match ([$comment, $status]) {
                [$this->comment, 'accept'] => false,
                [$this->comment, 'publish'] => false,
                [$this->comment, 'publish_ham'] => false,
                [$this->comment, 'optimize'] => true,
            });

        $this->handler->__invoke($message);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method invoke - must be a success, commentStateMachine else
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testInvokeElse(): void
    {
        $commentId = 1;
        $reviewUrl = '/admin/comment/review/' . $commentId;
        $user = $this->createUser();
        $this->comment = $this->createComment($user);
        $this->comment->setId($commentId);
        $message = new CommentMessage($commentId, $reviewUrl);

        $this->commentServiceMock->expects($this->once())
            ->method('find')
            ->with($commentId)
            ->willReturn($this->comment);

        $this->handler->__invoke($message);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method invoke - must be a success, Comment Null
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testInvokeCommentNull(): void
    {
        $commentId = 1;
        $reviewUrl = '/admin/comment/review/' . $commentId;
        $message = new CommentMessage($commentId, $reviewUrl);
        $this->handler->__invoke($message);
        $this->assertTrue(true);
    }
}
