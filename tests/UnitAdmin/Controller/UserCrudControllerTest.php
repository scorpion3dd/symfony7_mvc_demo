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

namespace App\Tests\UnitAdmin\Controller;

use App\Controller\Admin\UserCrudController;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserCrudControllerTest - for all unit tests in Admin UserCrudController by EasyAdminBundle
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 101 - Business process - User
 * @link https://www.atlassian.com/software/confluence/bp/101
 *
 * @package App\Tests\UnitAdmin\Controller
 */
class UserCrudControllerTest extends BaseCrudControllerAdmin
{
    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return UserCrudController::class;
    }

    /**
     * @return string
     */
    protected static function getEntityName(): string
    {
        return User::class;
    }

    /**
     * @testCase 1001 - Unit test index action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1001
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2001 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2001
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    public function testIndex(): void
    {
        $this->client->request(Request::METHOD_GET, $this->getCrudUrlIndex());
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1001 - Unit test index action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1001
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2001 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2001
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH Mock - Admin
     * paginator Mock - Users
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Crawler html contains string
     *
     * Class "EasyCorp\Bundle\EasyAdminBundle\Factory\PaginatorFactory" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoggedInIndex(): void
    {
        self::markTestSkipped(self::class . ' skipped testLoggedInIndex');
        $this->authMock();
        $this->paginatorMock();
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlIndex());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Users</title>', $html);
    }

    /**
     * @testCase 1002 - Unit test detail action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1002
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2002 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2002
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=detail&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Crawler html contains string
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoggedInDetail(): void
    {
        $this->authMock();
        $this->adminContextMock('User');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlDetail());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>' . $this->user->getUsername() . '</title>', $html);
    }

    /**
     * @testCase 1003 - Unit test edit action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1003
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2003 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2003
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Crawler html contains string
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoggedInEdit(): void
    {
        $this->authMock();
        $this->adminContextMock('User');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlEdit());
        $html = $crawler->html();
        $this->assertIsString($html);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Edit User</title>', $html);
    }

    /**
     * @testCase 1003 - Unit test edit action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1003
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2003 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2003
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH Mock - Admin
     * adminContext Mock - User
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
     * CrudPostParameters - User
     *   Form errors:
     * The CSRF token is invalid. Please try to resubmit the form.
     *     Assert:
     * StatusCode = 422 - HTTP_UNPROCESSABLE_ENTITY
     * Crawler html contains string
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoggedInEditPost422(): void
    {
        $this->authMock();
        $this->adminContextMock('User');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlEdit(),
            $this->getCrudPostParametersUserEdit($this->getTokenConst())
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('<title>Edit User</title>', $html);
    }

    /**
     * @testCase 1004 - Unit test new action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1004
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2004 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2004
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Crawler html contains string
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoggedInNew(): void
    {
        $this->authMock();
        $this->adminContextMock('User', 0);
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlNew());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('User details', $html);
    }

    /**
     * @testCase 1005 - Unit test delete action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1005
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2005 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2005
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * DELETE /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoggedInDelete(): void
    {
        $this->authMock();
        $this->adminContextMock('User');
        $this->client->loginUser($this->admin);
        $this->client->request(Request::METHOD_DELETE, $this->getCrudUrlDelete());
        static::assertResponseRedirects();
    }

    /**
     * @return void
     */
    public function testIntegrationGetEntityFqcn(): void
    {
        $entityFqcn = UserCrudController::getEntityFqcn();
        static::assertStringContainsString('App\Entity\User', $entityFqcn);
    }
}
