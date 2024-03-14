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

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Entity\User;
use App\Factory\CommentFactory;
use App\Repository\UserRepositoryInterface;
use App\Service\CommentServiceInterface;
use App\Util\LoggerTrait;
use App\Validator\ValidatorCommentUpload;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CommentUploadController
 * @package App\Controller\Api
 */
#[AsController]
class CommentUploadController extends AbstractController
{
    use LoggerTrait;

    /** @var string $photoDir */
    protected string $photoDir;

    /**
     * @param CommentFactory $commentFactory
     * @param CommentServiceInterface $commentService
     * @param ValidatorCommentUpload $validator
     * @param UserRepositoryInterface $userRepository
     * @param string $photoDir
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(
        private readonly CommentFactory $commentFactory,
        private readonly CommentServiceInterface $commentService,
        private readonly ValidatorCommentUpload $validator,
        private readonly UserRepositoryInterface $userRepository,
        #[Autowire('%app.photoDir%')] string $photoDir,
        LoggerInterface $logger,
        ContainerInterface $container
    ) {
        $this->photoDir = $photoDir;
        $this->logger = $logger;
        $this->container = $container;
        $this->debugConstruct(self::class);
    }

    /**
     * @param Request $request
     *
     * @return Comment|null
     * @throws Exception
     */
    public function __invoke(Request $request): ?Comment
    {
        $this->debugFunction(self::class, 'invoke');
        $commentDTO = $this->validator->validate($request);
        /** @var User|null $user */
        $user = $this->userRepository->find($commentDTO->userId);
        if (empty($user)) {
            $message = 'User with id ' . $commentDTO->userId . ' not found';
            $this->error($message);
            throw new NotFoundHttpException($message);
        }
        $comment = $this->commentFactory->create($user, $commentDTO->author, $commentDTO->email, $commentDTO->text);
        $comment = $this->commentService->savePhotoFileApi($commentDTO->uploadedFile, $comment, $this->photoDir);
        $this->commentService->save($comment, true);
        $reviewUrl = $this->generateUrl(
            'review_comment',
            ['id' => $comment->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $context = [
            'user_ip' => $request->getClientIp(),
            'user_agent' => $request->headers->get('user-agent'),
            'referrer' => $request->headers->get('referer'),
            'permalink' => $request->getUri(),
        ];
        $this->commentService->sendNotificationMessage($context, $comment, $reviewUrl);

        return $comment;
    }
}
