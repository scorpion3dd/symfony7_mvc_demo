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

namespace App\Tests\Integration\Admin;

use App\Controller\Admin\PermissionCrudController;
use App\Entity\Permission;
use Doctrine\ORM\Exception\NotSupported;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PermissionCrudControllerIntegrationTest - for all integration tests
 * in Admin PermissionCrudController by EasyAdminBundle with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 103 - Business process - Permission
 * @link https://www.atlassian.com/software/confluence/bp/103
 *
 * @package App\Tests\Integration\Admin
 */
class PermissionCrudControllerIntegrationTest extends BaseCrudControllerIntegration
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
     * @testCase 1011 - Integration test index action for PermissionCrudController with AUTH - must be a success
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
    public function testIntegrationIndex(): void
    {
        $this->client->request(Request::METHOD_GET, $this->getCrudUrlIndex());
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1011 - Integration test index action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1011
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2011 - For PermissionCrudController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2011
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Crawler html contains string
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws NotSupported
     */
    public function testIntegrationLoggedInIndex(): void
    {
        $this->auth();
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlIndex());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Permissions</title>', $html);
    }

    /**
     * @testCase 1012 - Integration test detail action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1012
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2012 - For PermissionCrudController detail action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2012
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH - Admin
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
     * @throws NotSupported
     * @throws Exception
     */
    public function testIntegrationLoggedInDetail(): void
    {
        $this->auth();
        $this->adminContext('Permission');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlDetail());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>' . $this->permission->getName() . '</title>', $html);
    }

    /**
     * @testCase 1013 - Integration test edit action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1013
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2013 - For PermissionCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2013
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH - Admin
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
     * @throws NotSupported
     */
    public function testIntegrationLoggedInEdit(): void
    {
        $this->auth();
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlEdit());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Edit Permission</title>', $html);
    }

    /**
     * @testCase 1013 - Integration test edit action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1013
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2013 - For PermissionCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2013
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH - Admin
     * adminContext - Permission
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
    public function testIntegrationLoggedInEditPost422(): void
    {
        $this->auth();
        $this->adminContext('Permission');
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
     * @testCase 1013 - Integration test edit action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1013
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2013 - For PermissionCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2013
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH - Admin
     * adminContext - Permission
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController&entityId=1
     * get CSRF _token from form Permission
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController&entityId=1
     * CrudPostParameters - Permission
     *     Assert:
     * StatusCode = 302 - HTTP_FOUND
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     */
    public function testIntegrationLoggedInEditPost(): void
    {
        $this->auth();
        $this->client->loginUser($this->admin);
        $crawlerGet = $this->client->request(Request::METHOD_GET, $this->getCrudUrlEdit());
        $htmlGet = $crawlerGet->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Edit Permission</title>', $htmlGet);

        $form = $crawlerGet->selectButton('Save changes')->form();
        $values = $form->getPhpValues();
        $token = $form['Permission[_token]']->getValue();

        $this->auth();
        $this->adminContext('Permission');
        /** some new data */
        $newData = $this->getCrudPostParametersPermissionEdit($token);
        $newData['Permission']['description'] .= ' - some new description';
        $values = array_merge($values, $newData);

        $this->client->request(Request::METHOD_POST, $this->getCrudUrlEdit(), $values);
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1014 - Integration test new action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1014
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2014 - For PermissionCrudController new action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2014
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH - Admin
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
     * @throws NotSupported
     */
    public function testIntegrationLoggedInNew(): void
    {
        $this->auth();
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlNew());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Create Permission</title>', $html);
    }

    /**
     * @testCase 1014 - Integration new action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1014
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2014 - For PermissionCrudController new action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2014
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH - Admin
     * adminContext - Permission
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController&entityId=1
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
     * @throws NotSupported
     * @throws Exception
     */
    public function testIntegrationLoggedInNewPost422(): void
    {
        $this->auth();
        $this->adminContext('Permission', 'new');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlNew(),
            $this->getCrudPostParametersPermissionEdit($this->getTokenConst())
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('<title>Create Permission</title>', $html);
    }

    /**
     * @testCase 1014 - Integration test new action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1014
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2014 - For PermissionCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2014
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController&entityId=1
     * get CSRF _token from form Permission
     * POST /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController
     * CrudPostParameters - Permission
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Crawler html contains string
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws NotSupported
     * @throws Exception
     */
    public function testIntegrationLoggedInNewPost(): void
    {
        $this->auth();
        $this->client->loginUser($this->admin);
        $crawlerGet = $this->client->request(Request::METHOD_GET, $this->getCrudUrlNew());
        $html = $crawlerGet->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Create Permission</title>', $html);

        $form = $crawlerGet->selectButton('Create')->form();
        $token = $form['Permission[_token]']->getValue();
        $this->auth();
        $this->adminContext('Permission', 'new');
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlNew(),
            $this->getCrudPostParametersPermissionNew($token)
        );
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1015 - Integration test delete action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1015
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2015 - For PermissionCrudController delete action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2015
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * DELETE /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController&entityId=1
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws NotSupported
     */
    public function testIntegrationLoggedInDelete(): void
    {
        $this->auth();
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
