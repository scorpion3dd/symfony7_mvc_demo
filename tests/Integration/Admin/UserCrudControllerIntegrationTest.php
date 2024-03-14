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

use App\Controller\Admin\UserCrudController;
use App\Entity\User;
use Doctrine\ORM\Exception\NotSupported;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserCrudControllerIntegrationTest - for all integration tests
 * in Admin UserCrudController by EasyAdminBundle with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 101 - Business process - User
 * @link https://www.atlassian.com/software/confluence/bp/101
 *
 * @package App\Tests\Integration\Admin
 */
class UserCrudControllerIntegrationTest extends BaseCrudControllerIntegration
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
     * @testCase 1001 - Integration test index action for UserCrudController with AUTH - must be a success
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
    public function testIntegrationIndex(): void
    {
        $this->client->request(Request::METHOD_GET, $this->getCrudUrlIndex());
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1001 - Integration test index action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1001
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2001 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2001
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController
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
        $this->assertStringContainsString('<title>Users</title>', $html);
    }

    /**
     * @testCase 1002 - Integration test detail action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1002
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2002 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2002
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH - Admin
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
     * @throws NotSupported
     * @throws Exception
     */
    public function testIntegrationLoggedInDetail(): void
    {
        $this->auth();
        $this->adminContext('User');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlDetail());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>' . $this->user->getUsername() . '</title>', $html);
    }

    /**
     * @testCase 1003 - Integration test edit action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1003
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2003 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2003
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH - Admin
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
     * @throws NotSupported
     */
    public function testIntegrationLoggedInEdit(): void
    {
        $this->auth();
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlEdit());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Edit User</title>', $html);
    }

    /**
     * @testCase 1003 - Integration test edit action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1003
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2003 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2003
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH - Admin
     * adminContext - User
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
     * @throws NotSupported
     * @throws Exception
     */
    public function testIntegrationLoggedInEditPost422(): void
    {
        $this->auth();
        $this->adminContext('User');
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
     * @testCase 1003 - Integration test edit action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1003
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2003 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2003
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH - Admin
     * adminContext - User
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
     * get CSRF _token from form User
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
     * CrudPostParameters - User
     *     Assert:
     * StatusCode = 302 - HTTP_FOUND
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws NotSupported
     * @throws Exception
     */
    public function testIntegrationLoggedInEditPost(): void
    {
        $this->auth();
        $this->client->loginUser($this->admin);
        $crawlerGet = $this->client->request(Request::METHOD_GET, $this->getCrudUrlEdit());
        $htmlGet = $crawlerGet->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Edit User</title>', $htmlGet);

        $form = $crawlerGet->selectButton('Save changes')->form();
        $token = $form['User[_token]']->getValue();
        $this->auth();
        $this->adminContext('User');
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlEdit(),
            $this->getCrudPostParametersUserEdit($token)
        );
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1004 - Integration test new action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1004
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2004 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2004
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH - Admin
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
     * @throws NotSupported
     */
    public function testIntegrationLoggedInNew(): void
    {
        $this->auth();
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlNew());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('User details', $html);
    }

    /**
     * @testCase 1004 - Integration new action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1004
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2004 - For UserCrudController new action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2004
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH - Admin
     * adminContext - User
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
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
     * @throws NotSupported
     * @throws Exception
     */
    public function testIntegrationLoggedInNewPost422(): void
    {
        $this->auth();
        $this->adminContext('User', 'new');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlNew(),
            $this->getCrudPostParametersUserEdit($this->getTokenConst())
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('<title>Create User</title>', $html);
    }

    /**
     * @testCase 1004 - Integration test new action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1004
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2004 - For UserCrudController new action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2004
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * Act:
     * GET /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
     * get CSRF _token from form User
     * POST /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController
     * CrudPostParameters - User
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
        $this->assertStringContainsString('User details', $html);

        $form = $crawlerGet->selectButton('Create')->form();
        $token = $form['User[_token]']->getValue();
        $this->auth();
        $this->adminContext('User', 'new');
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlNew(),
            $this->getCrudPostParametersUserNew($token)
        );
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1005 - Integration test delete action for UserCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1005
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2005 - For UserCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2005
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * DELETE /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
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
        $entityFqcn = UserCrudController::getEntityFqcn();
        static::assertStringContainsString('App\Entity\User', $entityFqcn);
    }
}
