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
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Interface CommentServiceInterface
 * @package App\Service
 */
interface CommentServiceInterface extends BaseServiceInterface
{
    /**
     * @param array $context
     * @param Comment $comment
     * @param string $reviewUrl
     *
     * @return void
     */
    public function sendNotificationMessage(
        array $context,
        Comment $comment,
        string $reviewUrl,
    ): void;

    /**
     * @param Notification $notification
     *
     * @return void
     */
    public function sendAdminRecipients(Notification $notification): void;

    /**
     * @param Notification $notification
     *
     * @return void
     */
    public function send(Notification $notification): void;

    /**
     * @param FormInterface $form
     * @param Comment $comment
     * @param string $photoDir
     *
     * @return Comment
     */
    public function savePhotoFile(
        FormInterface $form,
        Comment $comment,
        string $photoDir,
    ): Comment;

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
    ): Comment;

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
    ): string;

    /**
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
    ): ?Paginator;

    /**
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countOldRejected(): int;

    /**
     * @return int
     * @throws Exception
     */
    public function deleteOldRejected(): int;

    /**
     * @param int $messageId
     *
     * @return Comment|null
     */
    public function find(int $messageId): ?Comment;

    /**
     * @param KernelInterface $kernel
     *
     * @return bool
     */
    public function isProd(KernelInterface $kernel): bool;
}
