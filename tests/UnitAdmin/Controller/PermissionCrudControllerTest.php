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

use App\Controller\Admin\PermissionCrudController;
use App\Entity\Permission;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PermissionCrudControllerTest - for all unit tests in Admin PermissionCrudController by EasyAdminBundle
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 103 - Business process - Permission
 * @link https://www.atlassian.com/software/confluence/bp/103
 *
 * @package App\Tests\UnitAdmin\Controller
 */
class PermissionCrudControllerTest extends BaseCrudControllerAdmin
{
    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return PermissionCrudController::class;
    }

    /**
     * @return string
     */
    protected static function getEntityName(): string
    {
        return Permission::class;
    }

    /**
     * @testCase 1011 - Unit test index action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1011
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2011 - For PermissionCrudController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2011
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController
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
     * @testCase 1011 - Unit test index action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1011
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2011 - For PermissionCrudController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2011
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH Mock - Admin
     * paginator Mock - Permissions
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController
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
        $this->assertStringContainsString('<title>Permissions</title>', $html);
    }

    /**
     * @testCase 1012 - Unit test detail action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1012
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2012 - For PermissionCrudController detail action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2012
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=detail&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController&entityId=1
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
        $this->adminContextMock('Permission');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlDetail());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>' . $this->permission->getName() . '</title>', $html);
    }

    /**
     * @testCase 1013 - Unit test edit action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1013
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2013 - For PermissionCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2013
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController&entityId=1
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
        $this->adminContextMock('Permission');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlEdit());
        $html = $crawler->html();
        $this->assertIsString($html);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Edit Permission</title>', $html);
    }

    /**
     * @testCase 1013 - Unit test edit action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1013
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2013 - For PermissionCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2013
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH Mock - Admin
     * adminContext Mock - Permission
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController&entityId=1
     * CrudPostParameters - Permission
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
        $this->adminContextMock('Permission');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlEdit(),
            $this->getCrudPostParametersPermissionEdit($this->getTokenConst())
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('<title>Edit Permission</title>', $html);
    }

    /**
     * @testCase 1014 - Unit test new action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1014
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2014 - For PermissionCrudController new action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2014
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController
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
        $this->adminContextMock('Permission', 0);
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlNew());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Create Permission</title>', $html);
    }

    /**
     * @testCase 1015 - Unit test delete action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1015
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2015 - For PermissionCrudController delete action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2015
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * DELETE /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController&entityId=1
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
        $this->adminContextMock('Permission');
        $this->client->loginUser($this->admin);
        $this->client->request(Request::METHOD_DELETE, $this->getCrudUrlDelete());
        static::assertResponseRedirects();
    }

    /**
     * @return void
     */
    public function testIntegrationGetEntityFqcn(): void
    {
        $entityFqcn = PermissionCrudController::getEntityFqcn();
        static::assertStringContainsString('App\Entity\Permission', $entityFqcn);
    }
}
