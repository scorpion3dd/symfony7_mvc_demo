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

use App\Controller\Admin\RoleCrudController;
use App\Entity\Role;
use Doctrine\ORM\Exception\NotSupported;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RoleCrudControllerIntegrationTest - for all integration tests
 * in Admin RoleCrudController by EasyAdminBundle with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 102 - Business process - Role
 * @link https://www.atlassian.com/software/confluence/bp/102
 *
 * @package App\Tests\Integration\Admin
 */
class RoleCrudControllerIntegrationTest extends BaseCrudControllerIntegration
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
     * @testCase 1006 - Integration test index action for RoleCrudController with AUTH - must be a success
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
    public function testIntegrationIndex(): void
    {
        $this->client->request(Request::METHOD_GET, $this->getCrudUrlIndex());
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1006 - Integration test index action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1006
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2006 - For RoleCrudController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2006
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController
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
        $this->assertStringContainsString('<title>Roles</title>', $html);
    }

    /**
     * @testCase 1007 - Integration test detail action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1007
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2007 - For RoleCrudController detail action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2007
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH - Admin
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
     * @throws NotSupported
     * @throws Exception
     */
    public function testIntegrationLoggedInDetail(): void
    {
        $this->auth();
        $this->adminContext('Role');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlDetail());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>' . $this->role->getName() . '</title>', $html);
    }

    /**
     * @testCase 1008 - Integration test edit action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1008
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2008 - For RoleCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2008
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH - Admin
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
     * @throws NotSupported
     */
    public function testIntegrationLoggedInEdit(): void
    {
        $this->auth();
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlEdit());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Edit Role</title>', $html);
    }

    /**
     * @testCase 1008 - Integration test edit action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1008
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2008 - For RoleCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2008
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH - Admin
     * adminContext - Role
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
    public function testIntegrationLoggedInEditPost422(): void
    {
        $this->auth();
        $this->adminContext('Role');
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
     * @testCase 1008 - Integration test edit action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1008
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2008 - For RoleCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2008
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH - Admin
     * adminContext - Role
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController&entityId=1
     * get CSRF _token from form Role
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController&entityId=1
     * CrudPostParameters - Role
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
        $this->assertStringContainsString('<title>Edit Role</title>', $htmlGet);

        $form = $crawlerGet->selectButton('Save changes')->form();
        $values = $form->getPhpValues();
        $token = $form['Role[_token]']->getValue();

        $this->auth();
        $this->adminContext('Role');
        /** some new data */
        $newData = $this->getCrudPostParametersRoleEdit($token);
        $newData['Role']['description'] .= ' - some new description';
        $values = array_merge($values, $newData);

        $this->client->request(Request::METHOD_POST, $this->getCrudUrlEdit(), $values);
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1009 - Integration test new action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1009
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2009 - For RoleCrudController new action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2009
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH - Admin
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
     * @throws NotSupported
     */
    public function testIntegrationLoggedInNew(): void
    {
        $this->auth();
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlNew());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Create Role</title>', $html);
    }

    /**
     * @testCase 1009 - Integration new action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1009
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2009 - For RoleCrudController new action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2009
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH - Admin
     * adminContext - Role
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController&entityId=1
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
     * @throws NotSupported
     * @throws Exception
     */
    public function testIntegrationLoggedInNewPost422(): void
    {
        $this->auth();
        $this->adminContext('Role', 'new');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlNew(),
            $this->getCrudPostParametersRoleEdit($this->getTokenConst())
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('<title>Create Role</title>', $html);
    }

    /**
     * @testCase 1009 - Integration test new action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1009
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2009 - For RoleCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2009
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController&entityId=1
     * get CSRF _token from form Role
     * POST /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController
     * CrudPostParameters - Role
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
        $this->assertStringContainsString('<title>Create Role</title>', $html);

        $form = $crawlerGet->selectButton('Create')->form();
        $token = $form['Role[_token]']->getValue();
        $this->auth();
        $this->adminContext('Role', 'new');
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlNew(),
            $this->getCrudPostParametersRoleNew($token)
        );
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1010 - Integration test delete action for RoleCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1010
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2010 - For RoleCrudController delete action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2010
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * DELETE /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController&entityId=1
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
        $entityFqcn = RoleCrudController::getEntityFqcn();
        static::assertStringContainsString('App\Entity\Role', $entityFqcn);
    }
}
