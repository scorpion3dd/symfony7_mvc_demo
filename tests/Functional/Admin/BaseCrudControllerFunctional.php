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

namespace App\Tests\Functional\Admin;

use App\Document\DocumentInterface;
use App\Document\Log;
use App\Entity\Comment;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Factory\LogFactory;
use App\Tests\BaseCrudController;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base class BaseCrudControllerFunctional - for all functional tests
 * in Admin CrudControllers by EasyAdminBundle with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @link https://symfony.com/doc/current/testing.html#types-of-tests
 *
 * @package App\Tests\Integration\Admin
 * @property EntityManager $entityManager
 * @property DocumentManager $documentManager
 */
class BaseCrudControllerFunctional extends BaseCrudController
{
    public const USERNAME = 'admin1';
    public const PASSWORD = 'admin1';
    public const TEXT_REDIRECTING_TO = 'Redirecting to ';
    public const TEXT_EN_ADMIN = '/en/admin';
    public const TEXT_DASHBOARD = 'Dashboard';
    public const TEXT_SPAN_CONTENT = 'span.form-fieldset-title-content';
    public const TEXT_H1_TITLE = 'h1.title';
    public const TEXT_H1_H3 = 'h1.h3';
    public const TEXT_H1 = 'h1';
    public const TEXT_H2 = 'h2';
    public const TEXT_STRONG = 'strong';

    /** @var DocumentManager $documentManager */
    protected DocumentManager $documentManager;

    /** @var LogFactory $logFactory */
    protected LogFactory $logFactory;

    /** @var bool $assertByDb */
    protected bool $assertByDb = false;

    /** @var Crawler $crawlerFormLogin */
    protected Crawler $crawlerFormLogin;

    /** @var Crawler $crawlerAdmin */
    protected Crawler $crawlerAdmin;

    /** @var Crawler $crawlerIndex */
    protected Crawler $crawlerIndex;

    /** @var Crawler $crawlerShow */
    protected Crawler $crawlerShow;

    /** @var Crawler $crawlerFormEdit */
    protected Crawler $crawlerFormEdit;

    /** @var Crawler $crawlerDetail */
    protected Crawler $crawlerDetail;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->logFactory = $this->container->get(LogFactory::class);
        $this->documentManager = $this->container->get(DocumentManager::class);
    }

    /**
     * @param int $id
     *
     * @return void
     * @throws NotSupported
     */
    protected function comment(int $id): void
    {
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $this->comment = $commentRepository->find($id);
    }

    /**
     * @param int $id
     *
     * @return void
     * @throws NotSupported
     */
    protected function user(int $id): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $this->user = $userRepository->find($id);
    }

    /**
     * @param int|string $id
     * @param string $class
     *
     * @return void
     * @throws NotSupported
     * @throws LockException
     * @throws MappingException
     */
    protected function entity(int|string $id, string $class): void
    {
        if ($this->classImplements('App\Document\DocumentInterface', $class)) {
            $repository = $this->documentManager->getRepository($class);
        } elseif ($this->classImplements('App\Entity\EntityInterface', $class)) {
            $repository = $this->entityManager->getRepository($class);
        }
        $this->entity = $repository->find($id);
    }

    /**
     * @param string $interface
     * @param string $class
     *
     * @return bool
     */
    protected function classImplements(string $interface, string $class): bool
    {
        return in_array($interface, class_implements($class));
    }

    /**
     * @param string $username
     *
     * @return void
     * @throws NotSupported
     */
    protected function userByUsername(string $username): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $this->user = $userRepository->findOneBy(['username' => $username]);
    }

    /**
     * @param string $name
     *
     * @return void
     * @throws NotSupported
     */
    protected function roleByName(string $name): void
    {
        $roleRepository = $this->entityManager->getRepository(Role::class);
        $this->role = $roleRepository->findOneBy(['name' => $name]);
    }

    /**
     * @param string $name
     *
     * @return void
     * @throws NotSupported
     */
    protected function permissionByName(string $name): void
    {
        $permissionRepository = $this->entityManager->getRepository(Permission::class);
        $this->permission = $permissionRepository->findOneBy(['name' => $name]);
    }

    /**
     * @param string $message
     *
     * @return void
     * @throws NotSupported
     */
    protected function logByMessage(string $message): void
    {
        $logRepository = $this->entityManager->getRepository(Log::class);
        $this->log = $logRepository->findOneBy(['message' => $message]);
    }

    /**
     * @return void
     */
    protected function act1LoginGet(): void
    {
        $this->debugFunction(self::class, 'Act 1: GET /en/login');
        $this->crawlerFormLogin = $this->client->request(Request::METHOD_GET, self::ROUTE_URL_LOGIN);
        $this->assertPageTitleContains('Log in!');
        $this->assertSelectorTextContains(self::TEXT_H1_H3, 'Please sign in');
        static::assertResponseIsSuccessful();
    }

    /**
     * @return void
     */
    protected function act2LoginPost(): void
    {
        $this->debugFunction(self::class, 'Act 2: POST form submit /en/login');
        $formLogin = $this->getFormLogin($this->crawlerFormLogin);
        $this->client->submit($formLogin);
        static::assertResponseRedirects();
        $this->assertPageTitleContains(self::TEXT_REDIRECTING_TO);
        $this->assertPageTitleContains(self::TEXT_EN_ADMIN);
    }

    /**
     * @return void
     */
    protected function act3AdminGet(): void
    {
        $this->debugFunction(self::class, 'Act 3: Redirecting to GET /en/admin');
        $this->crawlerAdmin = $this->client->request(Request::METHOD_GET, self::ROUTE_URL_ADMIN);
        $this->assertPageTitleContains(self::TEXT_DASHBOARD);
        $this->assertSelectorTextContains(self::TEXT_H1, self::TEXT_DASHBOARD);
        $this->assertSelectorTextContains(self::TEXT_H2, 'Welcome to Admin application');
        $this->assertSelectorTextContains(self::TEXT_STRONG, 'You have successfully enter in your Admin application!');
        $this->assertSelectorExists('canvas.admin_chart[id=chart_users]');
    }

    /**
     * @param string $title
     * @param string $detail
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function actDashboardGet(string $title, string $detail): void
    {
        $this->debugFunction(self::class, 'Act 1: link click Index - '
            . 'GET http://localhost:81/en/admin?routeName=admin');
        $this->crawlerIndex = $this->client->request(Request::METHOD_GET, $this->getCrudUrlDashboardIndex());
        static::assertResponseIsSuccessful();
        $this->assertPageTitleContains($title);
        $this->assertSelectorTextContains(self::TEXT_H1_TITLE, $title);
        $this->assertSelectorExists('div canvas.admin_chart');
        $this->assertSelectorTextContains(self::TEXT_H2, $detail);
    }

    /**
     * @param string $act
     * @param string $title
     * @param string $crudController
     * @param bool $log
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     */
    protected function actIndexGet(string $act, string $title, string $crudController, bool $log = false): void
    {
        $this->debugFunction(self::class, 'Act ' . $act . ': link click Index - '
            . 'GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5C' . $crudController);
        $urlLog = $log ? $this->getCrudUrlLogRouteName('routeName=logs') : null;
        $selector = $this->getSelectorIndex($urlLog);
        $this->crawlerIndex = $this->client->click($this->getLink($this->crawlerAdmin, $selector));
        static::assertResponseIsSuccessful();
        $this->assertPageTitleContains($title);
        $this->assertSelectorTextContains(self::TEXT_H1_TITLE, $title);
        $selectorTable = $log ? 'table.table-striped' : 'table.datagrid';
        $this->assertSelectorExists($selectorTable);
        $this->assertSelectorExists('td.actions a.dropdown-toggle');
        $this->assertTrue($this->crawlerIndex->filter('td.actions a.dropdown-toggle')->count() > 0);
    }

    /**
     * @param string $class
     * @param string $crudController
     * @param string|null $detail
     * @param bool $log
     *
     * @return void
     * @throws Exception
     */
    protected function act5DetailGet(string $class, string $crudController, ?string $detail = null, bool $log = false): void
    {
        $this->debugFunction(self::class, 'Act 5: link click Detail - '
            . 'GET /en/admin?crudAction=detail&crudControllerFqcn=App%5CController%5CAdmin%5C'
            . $crudController . '&entityId=1');
        $selector = 'td.actions .dropdown-item.action-detail';
        $href = $this->getHref($this->crawlerIndex, $selector);
        $link = $this->getLink($this->crawlerIndex, $selector);
        $uri = $link->getUri();
        $this->assertEquals($href, $uri);
        if ($log) {
            $this->entityId = $this->getValue($uri, 'id');
            $this->assertIsString($this->entityId);
            $this->assertGreaterThan(0, strlen($this->entityId));
        } else {
            $this->entityId = (int)$this->getValue($uri, 'entityId');
            $this->assertIsInt($this->entityId);
            $this->assertGreaterThan(0, $this->entityId);
        }
        $this->entity($this->entityId, $class);

        $this->crawlerShow = $this->client->click($link);
        static::assertResponseIsSuccessful();
        $this->assertPageTitleContains($this->getTitle());
        $this->assertSelectorTextContains(self::TEXT_H1_TITLE, $this->getTitle());
        if (! empty($detail)) {
            $this->assertSelectorTextContains(self::TEXT_SPAN_CONTENT, $detail);
        }
    }

    /**
     * @param string $title
     * @param string $crudController
     * @param string|null $detail
     *
     * @return void
     * @throws Exception
     */
    protected function act6EditGet(string $title, string $crudController, ?string $detail = null): void
    {
        $this->debugFunction(self::class, 'Act 6: link click Edit - '
            . 'GET /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5C'
            . $crudController . '&entityId=1');
        $selector = 'a.action-edit';
        $link = $this->getLink($this->crawlerShow, $selector);
        $this->crawlerFormEdit = $this->client->click($link);
        static::assertResponseIsSuccessful();
        $this->assertPageTitleContains($title);
        $this->assertSelectorTextContains(self::TEXT_H1_TITLE, $title);
        if (! empty($detail)) {
            $this->assertSelectorTextContains(self::TEXT_SPAN_CONTENT, $detail);
        }
    }

    /**
     * @param string $class
     * @param string $crudController
     *
     * @return void
     * @throws NotSupported
     */
    protected function act7EditPost(string $class, string $crudController): void
    {
        $this->debugFunction(self::class, 'Act 7: link click Edit - '
            . 'POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5C'
            . $crudController . '&entityId=1');
        list($formEdit, $param) = $this->getFormEdit($this->crawlerFormEdit);
        $this->client->submit($formEdit);
        static::assertResponseRedirects();
        $this->assertPageTitleContains(self::TEXT_REDIRECTING_TO);
        $this->assertPageTitleContains(self::TEXT_EN_ADMIN);
        $this->entity($this->entityId, $class);
        $this->assertByDbEdit($param);
    }

    /**
     * @param string $title
     * @param string $crudController
     * @param string|null $detail
     * @param bool $log
     *
     * @return void
     * @throws Exception
     */
    protected function act8DetailGet(string $title, string $crudController, ?string $detail = null, bool $log = false): void
    {
        $this->debugFunction(self::class, 'Act 8: redirecting to Detail - '
            . 'GET /en/admin?crudAction=detail&crudControllerFqcn=App%5CController%5CAdmin%5C'
            . $crudController . '&entityId=1');
        $uri = $log ? $this->getCrudUrlLogRouteName('routeName=log_show&id=' . $this->entityId) : $this->getCrudUrlDetail();
        $this->crawlerDetail = $this->client->request(Request::METHOD_GET, $uri);
        $this->assertPageTitleContains($title);
        if (! empty($detail)) {
            $this->assertSelectorTextContains(self::TEXT_SPAN_CONTENT, $detail);
        }
    }

    /**
     * @param string $title
     * @param string $crudController
     * @param string|null $detail
     *
     * @return void
     * @throws NotSupported
     * @throws Exception
     */
    protected function act10NewGetSubmit(string $title, string $crudController, ?string $detail = null): void
    {
        $this->debugFunction(self::class, 'Act 10: link click New - '
            . 'GET /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5C' . $crudController);
        $selector = 'a.action-new';
        $link = $this->getLink($this->crawlerIndex, $selector);
        $crawlerFormNew = $this->client->click($link);
        static::assertResponseIsSuccessful();
        $this->assertPageTitleContains($title);
        $this->assertSelectorTextContains(self::TEXT_H1_TITLE, $title);
        if (! empty($detail)) {
            $this->assertSelectorTextContains(self::TEXT_SPAN_CONTENT, $detail);
        }
        $formNew = $this->getFormNew($crawlerFormNew);
        $this->client->submit($formNew);
        static::assertResponseRedirects();
        $this->assertPageTitleContains(self::TEXT_REDIRECTING_TO);
        $this->assertPageTitleContains(self::TEXT_EN_ADMIN);
        $this->assertByDbNew($this->entity);
    }

    /**
     * @param string $crudController
     *
     * @return void
     * @throws Exception
     */
    protected function act12DeleteGet(string $crudController): void
    {
        $this->debugFunction(self::class, 'Act 12: link click Delete - '
            . 'GET /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5C'
            . $crudController . '&entityId=1');
        $selector = 'td.actions .dropdown-item.action-delete';
        $link = $this->getLink($this->crawlerIndex, $selector);
        $crawlerDelete = $this->client->click($link);
        $this->debugParameters(self::class, ['crawlerDelete->html' => $crawlerDelete->html()]);
    }

    /**
     * @param string $crudController
     * @param bool $log
     *
     * @return void
     * @throws Exception
     */
    protected function act13DeleteDelete(string $crudController, bool $log = false): void
    {
        $this->debugFunction(self::class, 'Act 13: redirecting to Delete - '
            . 'DELETE /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5C'
            . $crudController . '&entityId=1');
        $urlLog = $log ? $this->getCrudUrlLogRouteName('routeName=log_delete&id=' . $this->entityId) : $this->getCrudUrlDelete();
        $this->client->request(Request::METHOD_DELETE, $urlLog);
        static::assertResponseRedirects();
        $this->assertPageTitleContains(self::TEXT_REDIRECTING_TO);
        $this->assertPageTitleContains(self::TEXT_EN_ADMIN);
        $this->assertByDbDelete();
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function act15LogoutGet(): void
    {
        $this->debugFunction(self::class, 'Act 15: link click Logout - GET /en/logout');
        $selector = 'a.menu-item-contents[href*="' . self::ROUTE_URL_LOGOUT . '"]';
        $this->client->click($this->getLink($this->crawlerIndex, $selector));
        static::assertResponseRedirects();
        $this->assertPageTitleContains(self::TEXT_REDIRECTING_TO);
        $this->assertPageTitleContains(self::ROUTE_URL_LOGIN);
    }

    /**
     * @param string $classCrudController
     * @param string $crudController
     * @param string $classEntity
     *
     * @return void
     */
    protected function act16GetEntityFqcn(
        string $classCrudController,
        string $crudController,
        string $classEntity
    ): void {
        $this->debugFunction(self::class, 'Act 16: ' . $crudController . ' - getEntityFqcn');
        /** @var AbstractCrudController $classCrudController */
        $entityFqcn = $classCrudController::getEntityFqcn();
        static::assertStringContainsString($classEntity, $entityFqcn);
    }

    /**
     * @param Crawler $crawler
     *
     * @return Form
     */
    private function getFormLogin(Crawler $crawler): Form
    {
        $formLogin = $crawler->selectButton('Sign in')->form();
        $formLogin['username'] = self::USERNAME;
        $formLogin['password'] = self::PASSWORD;

        return $formLogin;
    }
}
