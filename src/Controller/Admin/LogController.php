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

namespace App\Controller\Admin;

use App\Document\Log;
use App\Factory\LogFactory;
use App\Form\LogFormType;
use App\Service\LogService;
use App\Service\LogServiceInterface;
use App\Util\LoggerTrait;
use DateTime;
use Exception;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class LogController
 * @package App\Controller\Admin
 */
#[IsGranted('ROLE_ADMIN')]
class LogController extends AbstractController
{
    use LoggerTrait;

    /**
     * @param LogServiceInterface $logService
     * @param LogFactory $logFactory
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(
        private readonly LogServiceInterface $logService,
        private readonly LogFactory $logFactory,
        LoggerInterface $logger,
        ContainerInterface $container
    ) {
        $this->logger = $logger;
        $this->container = $container;
        $this->debugConstruct(self::class);
    }

    /**
     * @psalm-suppress RedundantCondition
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/{_locale<%app.supportedLocales%>}/logs', name: 'logs')]
    public function index(Request $request): Response
    {
        $this->debugFunction(self::class, 'index');
        $page = max(1, $request->query->getInt('page', 1));
        $filterField = $request->query->getAlnum('filterField') ?: null;
        $filterValue = $request->query->getAlnum('filterValue') ?: null;
        /** @var SlidingPagination $pagination */
        $pagination = $this->logService->getLogsPaginator($page, $filterField, $filterValue);
        $pagination->setTemplate('pagination/sliding.html.twig');
        $pagination->setPageRange(LogService::PAGINATOR_PER_PAGE);

        return $this->render('admin/log/index.html.twig', [
            /** @phpstan-ignore-next-line */
            'pagination' => ($pagination instanceof SlidingPagination) ? $pagination : null,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/{_locale<%app.supportedLocales%>}/admin?routeName=log_show', name: 'log_show')]
    public function show(Request $request): Response
    {
        $this->debugFunction(self::class, 'show');
        $id = (string)$request->query->get('id', '');
        $log = $this->logService->getLog($id);

        return $this->render('admin/log/show.html.twig', [
            'log' => $log,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/{_locale<%app.supportedLocales%>}/admin?routeName=log_add', name: 'log_add')]
    public function add(Request $request): Response
    {
        $this->debugFunction(self::class, 'add');
        $log = $this->logFactory->create();
        $logDbTimestamp = $log->getTimestamp() ?: new DateTime();
        $form = $this->createForm(LogFormType::class, $log);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var Log $log */
                $log = $form->getData();
                $logDbId = $log->getId();
                $log = $this->logService->editLog($log, $logDbId, $logDbTimestamp);
                $this->logService->save($log, true);

                return $this->redirectToRoute('admin', ['routeName' => 'log_show', 'id' => $log->getId()]);
            } else {
                $this->formErrors($form);
            }
        }

        return $this->render('admin/log/add.html.twig', [
            'log_form' => $form,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    #[Route('/{_locale<%app.supportedLocales%>}/admin?routeName=log_edit', name: 'log_edit')]
    public function edit(Request $request): Response
    {
        $this->debugFunction(self::class, 'edit');
        $id = (string)$request->query->get('id', '');
        $log = $this->logService->getLog($id);
        if (empty($log)) {
            return $this->redirectToRoute('admin', ['routeName' => 'logs']);
        }
        $logDbId = $log->getId();
        $logDbTimestamp = $log->getTimestamp() ?: new DateTime();
        $form = $this->createForm(LogFormType::class, $log);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var Log $log */
                $log = $form->getData();
                $log = $this->logService->editLog($log, $logDbId, $logDbTimestamp);
                $this->logService->save($log, true);

                return $this->redirectToRoute('admin', ['routeName' => 'log_show', 'id' => $log->getId()]);
            } else {
                $this->formErrors($form);
            }
        }

        return $this->render('admin/log/edit.html.twig', [
            'log' => $log,
            'log_form' => $form,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/{_locale<%app.supportedLocales%>}/admin?routeName=log_delete', name: 'log_delete')]
    public function delete(Request $request): Response
    {
        $this->debugFunction(self::class, 'delete');
        $id = (string)$request->query->get('id', '');
        $log = $this->logService->getLog($id);
        if (! empty($log)) {
            $this->logService->remove($log, true);
        }

        return $this->redirectToRoute('admin', ['routeName' => 'logs']);
    }
}
