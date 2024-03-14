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

namespace App\Service;

use App\Entity\Comment;
use App\Entity\User;
use App\Enum\Environments;
use App\Helper\SpamChecker;
use App\Message\CommentMessage;
use App\Repository\CommentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentService
 * @package App\Service
 */
class CommentService extends BaseService implements CommentServiceInterface
{
    public const PAGINATOR_PER_PAGE = 2;

    /**
     * @param EntityManagerInterface $entityManager
     * @param MessageBusInterface $bus
     * @param NotifierInterface $notifier
     * @param TranslatorInterface $translator
     * @param SpamChecker $spamChecker
     * @param CommentRepositoryInterface $repository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $bus,
        private NotifierInterface $notifier,
        private readonly TranslatorInterface $translator,
        private SpamChecker $spamChecker,
        protected CommentRepositoryInterface $repository,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @param array $context
     * @param Comment $comment
     * @param string $reviewUrl
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function sendNotificationMessage(
        array $context,
        Comment $comment,
        string $reviewUrl,
    ): void {
        $this->debugFunction(self::class, 'sendNotificationMessage');
        try {
            if (2 === $this->spamChecker->getSpamScore($comment, $context)) {
                throw new RuntimeException('Blatant spam, go away!');
            }
            $this->bus->dispatch(new CommentMessage($comment->getId() ?? 0, $reviewUrl, $context));
            $this->send(new Notification($this->translator
                ->trans('Thank you for the comment, your comment will be posted after moderation.'), ['browser']));
        } catch (Exception $ex) {
            $this->stringException($ex);
        }
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     * @param Notification $notification
     *
     * @return void
     */
    public function sendAdminRecipients(Notification $notification): void
    {
        $this->debugFunction(self::class, 'sendAdminRecipients');
        /** @phpstan-ignore-next-line */
        $this->notifier->send($notification, ...$this->notifier->getAdminRecipients());
    }

    /**
     * @param Notification $notification
     *
     * @return void
     */
    public function send(Notification $notification): void
    {
        $this->debugFunction(self::class, 'send');
        $this->notifier->send($notification);
    }

    /**
     * @psalm-suppress PossiblyNullReference
     * @param FormInterface $form
     * @param Comment $comment
     * @param string $photoDir
     *
     * @return Comment
     * @throws Exception
     */
    public function savePhotoFile(
        FormInterface $form,
        Comment $comment,
        string $photoDir,
    ): Comment {
        $this->debugFunction(self::class, 'savePhotoFile');
        $photo = $form['photo']->getData();
        if ($photo) {
            $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
            try {
                $photo->move($photoDir, $filename);
                $comment->setPhotoFilename($filename);
            } catch (FileException $ex) {
                $this->exception(self::class . ' Unable to upload the photo, give up', $ex);
            }
        }

        return $comment;
    }

    /**
     * @param UploadedFile $photo
     * @param Comment $comment
     * @param string $photoDir
     *
     * @return Comment
     * @throws Exception
     */
    public function savePhotoFileApi(
        UploadedFile $photo,
        Comment $comment,
        string $photoDir,
    ): Comment {
        $this->debugFunction(self::class, 'savePhotoFile');
        $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
        try {
            $photo->move($photoDir, $filename);
            $comment->setPhotoFilename($filename);
        } catch (FileException $ex) {
            $this->exception(self::class . ' Unable to upload the photo, give up', $ex);
        }

        return $comment;
    }

    /**
     * @param Comment $comment
     * @param bool $accepted
     * @param string $reviewUrl
     * @param WorkflowInterface|null $commentStateMachine
     *
     * @return string
     */
    public function reviewComment(
        Comment $comment,
        bool $accepted,
        string $reviewUrl,
        ?WorkflowInterface $commentStateMachine = null
    ): string {
        $this->debugFunction(self::class, 'reviewComment');
        $transition = '';
        if (! empty($commentStateMachine)) {
            if ($commentStateMachine->can($comment, 'publish')) {
                $transition = $accepted ? 'publish' : 'reject';
            } elseif ($commentStateMachine->can($comment, 'publish_ham')) {
                $transition = $accepted ? 'publish_ham' : 'reject_ham';
            }
            if ($transition != '') {
                $commentStateMachine->apply($comment, $transition);
                $this->entityManager->flush();
                if ($accepted) {
                    $this->bus->dispatch(new CommentMessage($comment->getId() ?? 0, $reviewUrl));
                }
            }
        }

        return $transition;
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     * @param User $user
     * @param int $offset
     * @param string $state
     *
     * @return Paginator|null
     */
    public function getCommentPaginator(
        User $user,
        int $offset,
        string $state = 'published'
    ): ?Paginator {
        $this->debugFunction(self::class, 'getCommentPaginator');
        $query = $this->repository->getComment($user, $offset, self::PAGINATOR_PER_PAGE, $state);
        if (isset($query)) {
            return new Paginator($query);
        }

        return null;
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countOldRejected(): int
    {
        return $this->repository->countOldRejected();
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     * @return int
     * @throws Exception
     */
    public function deleteOldRejected(): int
    {
        return $this->repository->deleteOldRejected();
    }

    /**
     * @param int $messageId
     *
     * @return Comment|null
     */
    public function find(int $messageId): ?Comment
    {
        /** @var Comment|null $comment */
        $comment = $this->repository->find($messageId);

        return $comment;
    }

    /**
     * @param CommentRepositoryInterface $repository
     */
    public function setRepository(CommentRepositoryInterface $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param MessageBusInterface $bus
     */
    public function setBus(MessageBusInterface $bus): void
    {
        $this->bus = $bus;
    }

    /**
     * @param SpamChecker $spamChecker
     */
    public function setSpamChecker(SpamChecker $spamChecker): void
    {
        $this->spamChecker = $spamChecker;
    }

    /**
     * @param NotifierInterface $notifier
     */
    public function setNotifier(NotifierInterface $notifier): void
    {
        $this->notifier = $notifier;
    }

    /**
     * @param KernelInterface $kernel
     *
     * @return bool
     */
    public function isProd(KernelInterface $kernel): bool
    {
        return Environments::PROD === $kernel->getEnvironment();
    }
}
