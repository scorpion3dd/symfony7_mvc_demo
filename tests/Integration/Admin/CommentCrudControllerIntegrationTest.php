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

use App\Controller\Admin\CommentCrudController;
use App\Entity\Comment;
use Doctrine\ORM\Exception\NotSupported;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CommentCrudControllerIntegrationTest - for all integration tests
 * in Admin CommentCrudController by EasyAdminBundle with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 104 - Business process - Comment
 * @link https://www.atlassian.com/software/confluence/bp/104
 *
 * @package App\Tests\Integration\Admin
 */
class CommentCrudControllerIntegrationTest extends BaseCrudControllerIntegration
{
    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return CommentCrudController::class;
    }

    /**
     * @return string
     */
    protected static function getEntityName(): string
    {
        return Comment::class;
    }

    /**
     * @testCase 1016 - Integration test index action for CommentCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1016
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2016 - For CommentCrudController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2016
     * @bp 104 - Business process - Comment
     * @link https://www.atlassian.com/software/confluence/bp/104
     *     Arrange:
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CCommentCrudController
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
     * @testCase 1016 - Integration test index action for CommentCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1016
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2016 - For CommentCrudController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2016
     * @bp 104 - Business process - Comment
     * @link https://www.atlassian.com/software/confluence/bp/104
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CCommentCrudController
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
        $this->assertStringContainsString('<title>User Comments</title>', $html);
    }

    /**
     * @testCase 1017 - Integration test detail action for CommentCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1017
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2017 - For CommentCrudController detail action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2017
     * @bp 104 - Business process - Comment
     * @link https://www.atlassian.com/software/confluence/bp/104
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=detail&crudControllerFqcn=App%5CController%5CAdmin%5CCommentCrudController&entityId=1
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
        $this->adminContext('Comment');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlDetail());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>' . $this->comment->getAuthor(), $html);
    }

    /**
     * @testCase 1018 - Integration test edit action for CommentCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1018
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2018 - For CommentCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2018
     * @bp 104 - Business process - Comment
     * @link https://www.atlassian.com/software/confluence/bp/104
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CCommentCrudController&entityId=1
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
        $this->assertStringContainsString('<title>Edit User Comment</title>', $html);
    }

    /**
     * @testCase 1018 - Integration test edit action for CommentCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1018
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2018 - For CommentCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2018
     * @bp 104 - Business process - Comment
     * @link https://www.atlassian.com/software/confluence/bp/104
     *     Arrange:
     * AUTH - Admin
     * adminContext - Comment
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CCommentCrudController&entityId=1
     * CrudPostParameters - Comment
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
        $this->adminContext('Comment');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlEdit(),
            $this->getCrudPostParametersCommentEdit($this->getTokenConst())
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('<title>Edit User Comment</title>', $html);
    }

    /**
     * @testCase 1018 - Integration test edit action for CommentCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1018
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2018 - For CommentCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2018
     * @bp 104 - Business process - Comment
     * @link https://www.atlassian.com/software/confluence/bp/104
     *     Arrange:
     * AUTH - Admin
     * adminContext - Comment
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CCommentCrudController&entityId=1
     * get CSRF _token from form Comment
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CCommentCrudController&entityId=1
     * CrudPostParameters - Comment
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
        $this->assertStringContainsString('<title>Edit User Comment</title>', $htmlGet);

        $form = $crawlerGet->selectButton('Save changes')->form();
        $values = $form->getPhpValues();
        $token = $form['Comment[_token]']->getValue();

        $this->auth();
        $this->adminContext('Comment');
        /** some new data */
        $newData = $this->getCrudPostParametersCommentEdit($token);
        $newData['Comment']['state'] = Comment::STATE_READY;
        $values = array_merge($values, $newData);

        $this->client->request(Request::METHOD_POST, $this->getCrudUrlEdit(), $values);
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1019 - Integration test delete action for CommentCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1019
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2019 - For CommentCrudController delete action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2019
     * @bp 104 - Business process - Comment
     * @link https://www.atlassian.com/software/confluence/bp/104
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * DELETE /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5CCommentCrudController&entityId=1
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
        $entityFqcn = CommentCrudController::getEntityFqcn();
        static::assertStringContainsString('App\Entity\Comment', $entityFqcn);
    }
}
