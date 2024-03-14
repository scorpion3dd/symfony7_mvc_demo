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

namespace App\Tests\Unit\Notification;

use App\Entity\Comment;
use App\Notification\CommentReviewNotification;
use App\Tests\Unit\BaseKernelTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Recipient\Recipient;

/**
 * Class CommentReviewNotificationTest - Unit tests for State CommentReviewNotification
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Notification
 */
class CommentReviewNotificationTest extends BaseKernelTestCase
{
    /** @var Comment $comment */
    private Comment $comment;

    /** @var CommentReviewNotification $notification */
    private CommentReviewNotification $notification;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $user = $this->createUser();
        $this->comment = $this->createComment($user);
        $this->notification = new CommentReviewNotification($this->comment, self::ROUTE_URL_COMMENT_REVIEW);
    }
    /**
     * @testCase - method asEmailMessage - must be a success
     *
     * @return void
     */
    public function testAsEmailMessage(): void
    {
        $recipient = new Recipient($this->comment->getEmail());
        $message = $this->notification->asEmailMessage($recipient);
        $this->assertInstanceOf(EmailMessage::class, $message);
        $this->assertInstanceOf(NotificationEmail::class, $message->getMessage());
    }

    /**
     * @testCase - method asChatMessage - must be a success
     *
     * @return void
     */
    public function testAsChatMessage(): void
    {
        $recipient = new Recipient($this->comment->getEmail());
        $message = $this->notification->asChatMessage($recipient, 'slack');
        $this->assertInstanceOf(ChatMessage::class, $message);
    }

    /**
     * @testCase - method asChatMessage - must be a success, Transport Null
     *
     * @return void
     */
    public function testAsChatMessageTransportNull(): void
    {
        $recipient = new Recipient($this->comment->getEmail());
        $message = $this->notification->asChatMessage($recipient);
        $this->assertNull($message);
    }

    /**
     * @testCase - method getChannels - must be a success
     *
     * @return void
     */
    public function testGetChannels(): void
    {
        $recipient = new Recipient($this->comment->getEmail());
        $channels = $this->notification->getChannels($recipient);
        $this->assertIsArray($channels);
        $this->assertContains('email', $channels);
    }

    /**
     * @testCase - method getChannels - must be a success, comment Text great
     *
     * @return void
     */
    public function testGetChannelsGreat(): void
    {
        $this->comment->setText('great simple text');
        $recipient = new Recipient($this->comment->getEmail());
        $notification = new CommentReviewNotification($this->comment, self::ROUTE_URL_COMMENT_REVIEW);
        $channels = $notification->getChannels($recipient);
        $this->assertIsArray($channels);
        $this->assertEquals(['email', 'chat/slack'], $channels);
    }
}
