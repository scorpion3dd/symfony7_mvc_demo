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

use App\Entity\User;
use App\Factory\CommentFactory;
use App\Form\CommentFormType;
use App\Service\CommentService;
use App\Service\CommentServiceInterface;
use App\Helper\UriHelper;
use App\Service\UserServiceInterface;
use App\Util\LoggerTrait;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LotteryController
 * @package App\Controller
 */
class LotteryController extends AbstractController
{
    use LoggerTrait;

    /**
     * @param TranslatorInterface $translator
     * @param UserServiceInterface $userService
     * @param CommentFactory $commentFactory
     * @param CommentServiceInterface $commentService
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UserServiceInterface $userService,
        private readonly CommentFactory $commentFactory,
        private readonly CommentServiceInterface $commentService,
        LoggerInterface $logger,
        ContainerInterface $container
    ) {
        $this->logger = $logger;
        $this->container = $container;
        $this->debugConstruct(self::class);
    }

    /**
     * @return Response
     */
    #[Route('/')]
    public function indexNoLocale(): Response
    {
        $this->debugFunction(self::class, 'indexNoLocale');

        return $this->redirectToRoute('homepage', ['_locale' => 'en']);
    }

    /**
     * @return Response
     */
    #[Route('/{_locale<%app.supportedLocales%>}/about', name: 'about')]
    public function about(): Response
    {
        $this->debugFunction(self::class, 'about');

        return $this->render('lottery/about.html.twig')->setSharedMaxAge(3600);
    }

    /**
     * @param Request $request
     * @param UriHelper $uriHelper
     *
     * @return Response
     */
    #[Route('/{_locale<%app.supportedLocales%>}/language', name: 'language')]
    public function language(Request $request, UriHelper $uriHelper): Response
    {
        $this->debugFunction(self::class, 'language');
        $locale = $request->getLocale();
        $localePath = '/' . $locale . '/';
        $urlRedirect = $request->server->get('HTTP_REFERER');
        if (! empty($urlRedirect)) {
            $urlR = $uriHelper->getInit($urlRedirect);
            if ($urlR->getPath() != $localePath) {
                return $this->redirect($uriHelper->urlRedirectNew($urlR, $localePath));
            }
        }

        return $this->redirectToRoute('homepage', ['_locale' => $locale]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/{_locale<%app.supportedLocales%>}/', name: 'homepage')]
    public function index(Request $request): Response
    {
        $this->debugFunction(self::class, 'index');
        $page = max(1, $request->query->getInt('page', 1));
        $pagination = $this->userService->getUsersPaginator($page, 'pagination/sliding.html.twig');

        return $this->render('lottery/index.html.twig', [
            'pagination' => ($pagination instanceof SlidingPagination) ? $pagination : null,
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/{_locale<%app.supportedLocales%>}/lottery_header', name: 'lottery_header')]
    public function lotteryHeader(): Response
    {
        $this->debugFunction(self::class, 'lotteryHeader');

        return $this->render('lottery/header.html.twig', [])->setSharedMaxAge(3600);
    }

    /**
     * @psalm-suppress RedundantCondition
     * @param Request $request
     * @param User $user
     * @param string $photoDir
     *
     * @return Response
     * @throws Exception
     */
    #[Route('/{_locale<%app.supportedLocales%>}/lottery/{slug}', name: 'lottery')]
    public function show(
        Request $request,
        User $user,
        #[Autowire('%app.photoDir%')] string $photoDir,
    ): Response {
        $this->debugFunction(self::class, 'show');
        $comment = $this->commentFactory->create($user);
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $comment = $this->commentService->savePhotoFile($form, $comment, $photoDir);
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

                return $this->redirectToRoute('lottery', ['slug' => $user->getSlug()]);
            } else {
                $this->formErrors($form);
            }
            $this->commentService->send(new Notification($this->translator
                ->trans('Can you check your submission? There are some problems with it.'), ['browser']));
        }
        $offset = max(0, $request->query->getInt('offset', 0));
        $commentPaginator = $this->commentService->getCommentPaginator($user, $offset);
        /** @phpstan-ignore-next-line */
        $countCommentPaginator = ($commentPaginator instanceof Paginator) ? count($commentPaginator) : 0;

        $commentForm = ($form instanceof Form) ? $form : null;
        /** @phpstan-ignore-next-line */
        $comments = ($commentPaginator instanceof Paginator) ? $commentPaginator : null;

        return $this->render('lottery/show.html.twig', [
            'user' => $user,
            'comments' => $comments,
            'previous' => $offset - CommentService::PAGINATOR_PER_PAGE,
            'next' => min($countCommentPaginator, $offset + CommentService::PAGINATOR_PER_PAGE),
            'comment_form' => $commentForm,
        ]);
    }
}
