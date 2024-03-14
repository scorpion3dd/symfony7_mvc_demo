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

use App\Controller\Admin\CommentCrudController;
use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentCrudControllerFormTest - for all unit tests in AdminForm CommentCrudController by EasyAdminBundle
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 104 - Business process - Comment
 * @link https://www.atlassian.com/software/confluence/bp/104
 *
 * @package App\Tests\UnitAdminForm\Controller
 */
class CommentCrudControllerFormTest extends BaseCrudControllerForm
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
     * @testCase 1018 - Unit test edit action for CommentCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1018
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2018 - For CommentCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2018
     * @bp 104 - Business process - Comment
     * @link https://www.atlassian.com/software/confluence/bp/104
     *     Arrange:
     * AUTH Mock - Admin
     * adminContext Mock - Comment
     * formBuilder (isSubmitted, isValid) Mock
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CCommentCrudController&entityId=1
     * CrudPostParameters - Comment
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
        $this->adminContextMock('Comment', 1);
        $this->formBuilderMock(true, true, 'createEditFormBuilder', 'Comment');
        $this->client->loginUser($this->admin);
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlEdit(),
            $this->getCrudPostParametersCommentEdit()
        );
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1019 - Unit test delete action for CommentCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1019
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2019 - For CommentCrudController delete action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2019
     * @bp 104 - Business process - Comment
     * @link https://www.atlassian.com/software/confluence/bp/104
     *     Arrange:
     * AUTH Mock - Admin
     * adminContext Mock - Comment
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5CCommentCrudController&entityId=1
     * CrudPostParameters - Comment
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
        $this->adminContextMock('Comment');
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
        $entityFqcn = CommentCrudController::getEntityFqcn();
        static::assertStringContainsString('App\Entity\Comment', $entityFqcn);
    }
}
