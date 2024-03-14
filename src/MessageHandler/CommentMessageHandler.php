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

namespace App\MessageHandler;

use App\Entity\Comment;
use App\Helper\ApplicationGlobals;
use App\Helper\ImageOptimizer;
use App\Message\CommentMessage;
use App\Notification\CommentReviewNotification;
use App\Helper\SendgridSender;
use App\Helper\SlackSender;
use App\Helper\SpamChecker;
use App\Service\CommentServiceInterface;
use App\Util\ConsoleOutputTrait;
use App\Util\LoggerTrait;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class CommentMessageHandler
 * @package App\MessageHandler
 */
#[AsMessageHandler]
class CommentMessageHandler
{
    use LoggerTrait;
    use ConsoleOutputTrait;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SpamChecker $spamChecker
     * @param CommentServiceInterface $commentService
     * @param MessageBusInterface $bus
     * @param SlackSender $slack
     * @param SendgridSender $sendgrid
     * @param ImageOptimizer $imageOptimizer
     * @param string $photoDir
     * @param LoggerInterface $logger
     * @param ApplicationGlobals $appGlobals
     * @param WorkflowInterface|null $commentStateMachine
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SpamChecker $spamChecker,
        private CommentServiceInterface $commentService,
        private MessageBusInterface $bus,
        private SlackSender $slack,
        private SendgridSender $sendgrid,
        private ImageOptimizer $imageOptimizer,
        #[Autowire('%app.photoDir%')] private string $photoDir,
        LoggerInterface $logger,
        ApplicationGlobals $appGlobals,
        private ?WorkflowInterface $commentStateMachine = null,
    ) {
        $this->logger = $logger;
        $this->appGlobals = $appGlobals;
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
        $this->buildIo($this->input, $this->output);
        $this->debugConstruct(self::class);
    }

    /**
     * @psalm-suppress DeprecatedConstant
     * @param CommentMessage $message
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function __invoke(CommentMessage $message): void
    {
        $this->debugFunction(self::class, 'invoke');
        $name = self::class . ' invoke';
        $this->debugMessage(self::class, 'start');
        $this->echo($name . ' - started', Logger::NOTICE);

        $this->debugMessage(self::class, 'comment message status = accept');
        $this->debugParameters(self::class, ['photoDir' => $this->photoDir]);

        /** @var Comment|null $comment */
        $comment = $this->commentService->find($message->getId());
        if (! $comment) {
            $this->echo('NULL', Logger::NOTICE);

            return;
        }
        $this->echo('comment-id = ' . $comment->getId(), Logger::DEBUG);

        $this->slack->send($comment);
        $this->sendgrid->send($comment);

        $context = ['comment' => $comment->getId(), 'state' => $comment->getState()];
        if (! empty($this->commentStateMachine)) {
            if ($this->commentStateMachine->can($comment, 'accept')) {
                $this->debugMessage(self::class, 'comment message status = accept');
                $this->debugParameters(self::class, ['context' => $context]);

                $score = $this->spamChecker->getSpamScore($comment, $message->getContext());
                $transition = match ($score) {
                    2 => 'reject_spam',
                    1 => 'might_be_spam',
                    default => 'accept',
                };
                $this->commentStateMachine->apply($comment, $transition);
                $this->entityManager->flush();

                $this->debugMessage(self::class, 'comment message status = ' . $transition);
                $this->debugParameters(self::class, ['context' => $context]);
                try {
                    $this->bus->dispatch($message);
                // @codeCoverageIgnoreStart
                } catch (Exception $ex) {
                // @codeCoverageIgnoreEnd
                    $this->exception(self::class, $ex);
                }
            } elseif ($this->commentStateMachine->can($comment, 'publish')
                || $this->commentStateMachine->can($comment, 'publish_ham')) {
                if ($this->commentStateMachine->can($comment, 'publish')) {
                    $this->debugMessage(self::class, 'comment message status = publish');
                    $this->debugParameters(self::class, ['context' => $context]);
                }
                if ($this->commentStateMachine->can($comment, 'publish_ham')) {
                    $this->debugMessage(self::class, 'comment message status = publish_ham');
                    $this->debugParameters(self::class, ['context' => $context]);
                }
                $notification = new CommentReviewNotification($comment, $message->getReviewUrl());
                $this->echo('commentService->send', Logger::NOTICE);
                $this->commentService->sendAdminRecipients($notification);
            } elseif ($this->commentStateMachine->can($comment, 'optimize')) {
                $this->debugMessage(self::class, 'comment message status = optimize');
                $this->debugParameters(self::class, ['context' => $context]);
                if ($comment->getPhotoFilename()) {
                    $this->imageOptimizer->resize($this->photoDir.'/'.$comment->getPhotoFilename());
                }
                $this->commentStateMachine->apply($comment, 'optimize');
                $this->entityManager->flush();
            } else {
                $this->debugMessage(self::class, 'Dropping comment message');
                $this->debugParameters(self::class, ['context' => $context]);
            }
        }
        $this->debugMessage(self::class, 'finish');
        $this->echo($name . ' - finished', Logger::NOTICE);
    }
}
