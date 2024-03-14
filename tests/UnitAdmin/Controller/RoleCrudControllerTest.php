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

use App\Controller\Admin\RoleCrudController;
use App\Entity\Role;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RoleCrudControllerTest - for all unit tests in Admin RoleCrudController by EasyAdminBundle
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 102 - Business process - Role
 * @link https://www.atlassian.com/software/confluence/bp/102
 *
 * @package App\Tests\UnitAdmin\Controller
 */
class RoleCrudControllerTest extends BaseCrudControllerAdmin
{
    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return RoleCrudController::class;
    }

    /**
     * @return string
     */
    protected static function getEntityName(): string
    {
        return Role::class;
    }

    /**
     * @testCase 1006 - Unit test index action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1006
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2006 - For RoleCrudController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2006
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController
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
     * @testCase 1006 - Unit test index action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1006
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2006 - For RoleCrudController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2006
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH Mock - Admin
     * paginator Mock - Roles
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController
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
        $this->assertStringContainsString('<title>Roles</title>', $html);
    }

    /**
     * @testCase 1007 - Unit test detail action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1007
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2007 - For RoleCrudController detail action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2007
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=detail&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController&entityId=1
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
        $this->adminContextMock('Role');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlDetail());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>' . $this->role->getName() . '</title>', $html);
    }

    /**
     * @testCase 1008 - Unit test edit action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1008
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2008 - For RoleCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2008
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController&entityId=1
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Crawler html contains string
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     */
    public function testLoggedInEdit(): void
    {
        $this->authMock();
        $this->adminContextMock('Role');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlEdit());
        $html = $crawler->html();
        $this->assertIsString($html);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Edit Role</title>', $html);
    }

    /**
     * @testCase 1008 - Unit test edit action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1008
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2008 - For RoleCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2008
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH Mock - Admin
     * adminContext Mock - Role
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController&entityId=1
     * CrudPostParameters - Role
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
     */
    public function testLoggedInEditPost422(): void
    {
        $this->authMock();
        $this->adminContextMock('Role');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlEdit(),
            $this->getCrudPostParametersRoleEdit($this->getTokenConst())
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('<title>Edit Role</title>', $html);
    }

    /**
     * @testCase 1009 - Unit test new action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1009
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2009 - For RoleCrudController new action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2009
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Crawler html contains string
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     */
    public function testLoggedInNew(): void
    {
        $this->authMock();
        $this->adminContextMock('Role', 0);
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlNew());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Create Role</title>', $html);
    }

    /**
     * @testCase 1010 - Unit test delete action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1010
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2010 - For RoleCrudController delete action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2010
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * DELETE /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController&entityId=1
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     */
    public function testLoggedInDelete(): void
    {
        $this->authMock();
        $this->adminContextMock('Role');
        $this->client->loginUser($this->admin);
        $this->client->request(Request::METHOD_DELETE, $this->getCrudUrlDelete());
        static::assertResponseRedirects();
    }

    /**
     * @return void
     */
    public function testIntegrationGetEntityFqcn(): void
    {
        $entityFqcn = RoleCrudController::getEntityFqcn();
        static::assertStringContainsString('App\Entity\Role', $entityFqcn);
    }
}
