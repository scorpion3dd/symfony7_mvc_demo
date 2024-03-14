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

namespace App\Controller;

use App\Entity\Comment;
use App\Service\CommentServiceInterface;
use App\Util\LoggerTrait;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AdminController
 * @package App\Controller
 */
#[Route('/admin')]
class AdminController extends AbstractController
{
    use LoggerTrait;

    /**
     * @param Environment $twig
     * @param CommentServiceInterface $commentService
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(
        private readonly Environment $twig,
        private readonly CommentServiceInterface $commentService,
        LoggerInterface $logger,
        ContainerInterface $container
    ) {
        $this->logger = $logger;
        $this->container = $container;
        $this->debugConstruct(self::class);
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @param WorkflowInterface $commentStateMachine
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[Route('/comment/review/{id}', name: 'review_comment')]
    public function reviewComment(Request $request, Comment $comment, WorkflowInterface $commentStateMachine): Response
    {
        $this->debugFunction(self::class, 'reviewComment');
        $accepted = ! $request->query->get('reject');
        $reviewUrl = '';
        if ($accepted) {
            $reviewUrl = $this->generateUrl(
                'review_comment',
                ['id' => $comment->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }
        $transition = $this->commentService->reviewComment($comment, $accepted, $reviewUrl, $commentStateMachine);
        if ($transition == '') {
            return new Response('Comment already reviewed or not in the right state.');
        }

        return new Response($this->twig->render('admin/review.html.twig', [
            'transition' => $transition,
            'comment' => $comment,
        ]));
    }

    /**
     * @param KernelInterface $kernel
     * @param Request $request
     * @param string $uri
     * @param StoreInterface $store
     *
     * @return Response
     */
    #[Route('/http-cache/{uri<.*>}', methods: ['PURGE'])]
    public function purgeHttpCache(KernelInterface $kernel, Request $request, string $uri, StoreInterface $store): Response
    {
        $this->debugFunction(self::class, 'purgeHttpCache');
        if ($this->commentService->isProd($kernel)) {
            // @codeCoverageIgnoreStart
            return new Response('KO', 400);
            // @codeCoverageIgnoreEnd
        }
        $store->purge($request->getSchemeAndHttpHost() . '/' . $uri);

        return new Response('Done');
    }
}
