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

namespace App\Notification;

use App\Entity\Comment;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackActionsBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

/**
 * Class CommentReviewNotification
 * @package App\Notification
 */
class CommentReviewNotification extends Notification implements EmailNotificationInterface, ChatNotificationInterface
{
    /**
     * @param Comment $comment
     * @param string $reviewUrl
     */
    public function __construct(
        private Comment $comment,
        private string $reviewUrl,
    ) {
        parent::__construct('New comment posted');
    }

    /**
     * @psalm-suppress UndefinedMethod
     * @param EmailRecipientInterface $recipient
     * @param string|null $transport
     *
     * @return EmailMessage|null
     */
    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $message = EmailMessage::fromNotification($this, $recipient);
        /** @phpstan-ignore-next-line */
        $message->getMessage()->htmlTemplate('emails/comment_notification.html.twig')
            ->context(['comment' => $this->comment]);

        return $message;
    }

    /**
     * @param RecipientInterface $recipient
     * @param string|null $transport
     *
     * @return ChatMessage|null
     */
    public function asChatMessage(RecipientInterface $recipient, string $transport = null): ?ChatMessage
    {
        if ('slack' !== $transport) {
            return null;
        }
        $message = ChatMessage::fromNotification($this);
        $message->subject($this->getSubject());
        $message->options((new SlackOptions())
            ->iconEmoji('tada')
            ->iconUrl('http://symfony6.myguestbook.os')
            ->username('Guestbook')
            ->block((new SlackSectionBlock())->text($this->getSubject()))
            ->block(new SlackDividerBlock())
            ->block((new SlackSectionBlock())
                ->text(sprintf(
                    '%s (%s) says: %s',
                    $this->comment->getAuthor() ?? '',
                    $this->comment->getEmail() ?? '',
                    $this->comment->getText() ?? ''
                )))
            ->block((new SlackActionsBlock())
                ->button('Accept', $this->reviewUrl, 'primary')
                ->button('Reject', $this->reviewUrl.'?reject=1', 'danger')));

        return $message;
    }

    /**
     * @param RecipientInterface $recipient
     *
     * @return list<string>
     */
    public function getChannels(RecipientInterface $recipient): array
    {
        if (preg_match('{\b(great|awesome)\b}i', $this->comment->getText() ?? '')) {
            return ['email', 'chat/slack'];
        }
        $this->importance(Notification::IMPORTANCE_LOW);

        return ['email'];
    }
}
