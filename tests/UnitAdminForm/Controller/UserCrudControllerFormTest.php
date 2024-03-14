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

namespace App\Tests\UnitAdminForm\Controller;

use App\Controller\Admin\UserCrudController;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserCrudControllerFormTest - for all unit tests in AdminForm UserCrudController by EasyAdminBundle
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 101 - Business process - User
 * @link https://www.atlassian.com/software/confluence/bp/101
 *
 * @package App\Tests\UnitAdminForm\Controller
 */
class UserCrudControllerFormTest extends BaseCrudControllerForm
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
     * Csrf Mock
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
     * CrudPostParameters - User
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
    public function testLoggedInEditPostCsrf(): void
    {
        self::markTestSkipped(self::class . ' skipped testLoggedInEditPostCsrf');
        $this->authMock(2);
        $this->adminContextMock('User', 2);
        $this->client->loginUser($this->admin);

        $session = $this->provideSession($this->client);
        $this->prepareCookie($this->client);
        $this->prepareSession('secret-santa-azerty', '123456789');

        $crawlerGet = $this->client->request(Request::METHOD_GET, $this->getCrudUrlEdit());
        $form = $crawlerGet->selectButton('Save changes')->form();
        $form->disableValidation();
        $crawler = $this->client->submit($form);
        $token = $form['User[_token]']->getValue();
        $token = '';
        $token = $this->generateCsrfToken('User');
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlEdit(),
            $this->getCrudPostParametersUserEdit($this->getTokenConst())
        );
        $html = $crawler->html();
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
     * formBuilder (isSubmitted, isValid) Mock
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
     * CrudPostParameters - User
     *     Assert:
     * StatusCode = 302 - HTTP_FOUND
     *
     * Class "EasyCorp\Bundle\EasyAdminBundle\Factory\FormFactory" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoggedInEditPost(): void
    {
        self::markTestSkipped(self::class . ' skipped testLoggedInEditPost');
        $this->authMock();
        $this->adminContextMock('User', 1);
        $this->formBuilderMock(true, true, 'createEditFormBuilder', 'User');
        $this->client->loginUser($this->admin);
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlEdit(),
            $this->getCrudPostParametersUserEdit()
        );
        static::assertResponseRedirects();
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
     * adminContext Mock - User
     * formBuilder (isSubmitted, isValid, getData) Mock
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
     * CrudPostParameters - User
     *     Assert:
     * StatusCode = 302 - HTTP_FOUND
     *
     * Class "EasyCorp\Bundle\EasyAdminBundle\Factory\FormFactory" is declared "final" and cannot be doubled
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoggedInNewPost(): void
    {
        self::markTestSkipped(self::class . ' skipped testLoggedInNewPost');
        $this->authMock();
        $this->adminContextMock('User', 0);
        $this->formBuilderMock(true, true, 'createNewFormBuilder', 'User');
        $this->client->loginUser($this->admin);
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlNew(),
            $this->getCrudPostParametersUserEdit()
        );
        static::assertResponseRedirects();
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
     * adminContext Mock - User
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController&entityId=1
     * CrudPostParameters - User
     *     Assert:
     * StatusCode = 302 - HTTP_FOUND
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoggedInDeletePost(): void
    {
        $this->authMock();
        $this->adminContextMock('User');
        $this->client->loginUser($this->admin);
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlDelete(),
            $this->getCrudPostParametersDelete($this->getTokenConst())
        );
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
