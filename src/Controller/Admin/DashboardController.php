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

use App\Entity\Comment;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Helper\UserChartHelper;
use App\Util\LoggerTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DashboardController
 * @package App\Controller\Admin
 */
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    use LoggerTrait;

    /** @var Request|null $request */
    protected ?Request $request;

    /**
     * @param TranslatorInterface $translator
     * @param UserChartHelper $userChartHelper
     * @param RequestStack $requestStack
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UserChartHelper $userChartHelper,
        private readonly RequestStack $requestStack,
        LoggerInterface $logger,
        ContainerInterface $container
    ) {
        $this->logger = $logger;
        $this->container = $container;
        $this->debugConstruct(self::class);
        $this->request = $this->requestStack->getCurrentRequest();
    }

    /**
     * https://symfony.com/bundles/EasyAdminBundle/current/dashboards.html#customizing-the-dashboard-contents
     *
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{_locale<%app.supportedLocales%>}/admin', name: 'admin')]
    public function index(): Response
    {
        try {
            $this->debugFunction(self::class, 'index');

            return $this->render('admin/dashboard.html.twig', [
                'chart_comments' => $this->userChartHelper->createChart(),
//                'chart_comments' => null,
            ]);
        // @codeCoverageIgnoreStart
        } catch (Exception $ex) {
            $this->exception(self::class . ' createChart', $ex);

            $routeBuilder = $this->container->get(AdminUrlGenerator::class);
            $url = $routeBuilder->setController(UserCrudController::class)->generateUrl();

            return $this->redirect($url);
        }
        // @codeCoverageIgnoreEnd

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    /**
     * @return Dashboard
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/images/readme/3ds.jpg" alt="logo" width="30px">
                    <span class="text-small">'
                    . $this->translator->trans('Simple Web Demo Free Lottery Management Application')
                    . '</span>')
            ->setFaviconPath('favicon.ico')
//            ->renderSidebarMinimized()
            ->setLocales(['en', 'fr'])
            ;
    }

    /**
     * @return iterable<mixed, MenuItemInterface>
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoRoute('Back to the website', 'fas fa-home', 'homepage');
        yield MenuItem::linktoRoute('Dashboard', 'fa fa-columns', 'admin');
        yield MenuItem::section('Main');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Comments', 'fa fa-comment', Comment::class);
        yield MenuItem::section('References');
        yield MenuItem::linkToCrud('Permissions', 'fa fa-lock', Permission::class);
        yield MenuItem::linkToCrud('Roles', 'fa fa-user-circle', Role::class);
        yield MenuItem::section('Monitoring');
        yield MenuItem::linkToRoute('Logs', 'fa fa-list-alt', 'logs');
        yield MenuItem::section('-------');
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }

    /**
     * https://symfony.com/bundles/EasyAdminBundle/current/design.html#adding-custom-web-assets
     * /vendor/easycorp/easyadmin-bundle/src/Resources/public
     *
     * @return Assets
     * @throws Exception
     */
    public function configureAssets(): Assets
    {
        $path = 'js/jquery-ui-1.13.2/';
        $assets = parent::configureAssets()
//            ->addCssFile('build/admin.css')
//            ->addCssFile('https://example.org/css/admin2.css')
//            ->addHtmlContentToHead('<link rel="dns-prefetch" href="https://assets.example.com">')
//            ->addHtmlContentToBody('<!-- generated at '.time().' -->')
//            ->addWebpackEncoreEntry('admin-app')
            ->addCssFile($path . 'jquery-ui.min.css')
            ->addJsFile($path . 'external/jquery/jquery.js')
            ->addJsFile($path . 'jquery-ui.min.js')
            ->addJsFile('build/admin-app.js')
            ->addJsFile('build/admin.js')
            ->addJsFile('js/admin.js')
            ->addJsFile('https://cdn.jsdelivr.net/npm/chart.js');
        if ($this->isController('DashboardController')) {
            $assets->addHtmlContentToBody($this->userChartHelper->getJavascript($this->userChartHelper->getDataChartUsers()));
        }

        return $assets;
    }

    /**
     * @param string $crudController
     *
     * @return bool
     */
    private function isController(string $crudController = ''): bool
    {
        $res = false;
        if (isset($this->request)) {
            $queryString = $this->request->getQueryString();
        }
        $queryString = $queryString ?? 'routeName=admin';
        if ($crudController === 'DashboardController' && $queryString === 'routeName=admin') {
            $res = true;
        } else {
            parse_str($queryString, $queryArray);
            if (isset($queryArray['crudControllerFqcn'])) {
                /** @phpstan-ignore-next-line */
                $crudControllerFqcn = (string)$queryArray['crudControllerFqcn'];
                if (false !== strpos($crudControllerFqcn, $crudController)) {
                    $res = true;
                }
            }
        }

        return $res;
    }
}
