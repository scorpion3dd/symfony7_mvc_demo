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

use App\Controller\Admin\PermissionCrudController;
use App\Entity\Permission;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PermissionCrudControllerFormTest - for all unit tests in AdminForm PermissionCrudController by EasyAdminBundle
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 103 - Business process - Permission
 * @link https://www.atlassian.com/software/confluence/bp/102
 *
 * @package App\Tests\UnitAdminForm\Controller
 */
class PermissionCrudControllerFormTest extends BaseCrudControllerForm
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
     * @testCase 1013 - Unit test edit action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1013
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2013 - For PermissionCrudController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2013
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH Mock - Admin
     * adminContext Mock - Permission
     * formBuilder (isSubmitted, isValid) Mock
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController&entityId=1
     * CrudPostParameters - Permission
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
        $this->adminContextMock('Permission', 1);
        $this->formBuilderMock(true, true, 'createEditFormBuilder', 'Permission');
        $this->client->loginUser($this->admin);
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlEdit(),
            $this->getCrudPostParametersPermissionEdit()
        );
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1014 - Unit test new action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1014
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2014 - For PermissionCrudController new action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2014
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH Mock - Admin
     * adminContext Mock - Permission
     * formBuilder (isSubmitted, isValid, getData) Mock
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController&entityId=1
     * CrudPostParameters - Permission
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
        $this->adminContextMock('Permission', 0);
        $this->formBuilderMock(true, true, 'createNewFormBuilder', 'Permission');
        $this->client->loginUser($this->admin);
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlNew(),
            $this->getCrudPostParametersPermissionEdit()
        );
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1015 - Unit test delete action for PermissionCrudController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1015
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2015 - For PermissionCrudController delete action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2015
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * AUTH Mock - Admin
     * adminContext Mock - Permission
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController&entityId=1
     * CrudPostParameters - Permission
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
        $this->adminContextMock('Permission');
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
        $entityFqcn = PermissionCrudController::getEntityFqcn();
        static::assertStringContainsString('App\Entity\Permission', $entityFqcn);
    }
}
