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

use App\Controller\Admin\RoleCrudController;
use App\Entity\Role;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RoleCrudControllerFormTest - for all unit tests in AdminForm RoleCrudController by EasyAdminBundle
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 102 - Business process - Role
 * @link https://www.atlassian.com/software/confluence/bp/102
 *
 * @package App\Tests\UnitAdminForm\Controller
 */
class RoleCrudControllerFormTest extends BaseCrudControllerForm
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
     * formBuilder (isSubmitted, isValid) Mock
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=edit&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController&entityId=1
     * CrudPostParameters - Role
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
        $this->adminContextMock('Role', 1);
        $this->formBuilderMock(true, true, 'createEditFormBuilder', 'Role');
        $this->client->loginUser($this->admin);
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlEdit(),
            $this->getCrudPostParametersRoleEdit()
        );
        static::assertResponseRedirects();
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
     * adminContext Mock - Role
     * formBuilder (isSubmitted, isValid, getData) Mock
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=new&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController&entityId=1
     * CrudPostParameters - Role
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
        $this->adminContextMock('Role', 0);
        $this->formBuilderMock(true, true, 'createNewFormBuilder', 'Role');
        $this->client->loginUser($this->admin);
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlNew(),
            $this->getCrudPostParametersRoleEdit()
        );
        static::assertResponseRedirects();
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
     * adminContext Mock - Role
     * loginUser - Admin
     *     Act:
     * POST /en/admin?crudAction=delete&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController&entityId=1
     * CrudPostParameters - Role
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
        $this->adminContextMock('Role');
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
        $entityFqcn = RoleCrudController::getEntityFqcn();
        static::assertStringContainsString('App\Entity\Role', $entityFqcn);
    }
}
