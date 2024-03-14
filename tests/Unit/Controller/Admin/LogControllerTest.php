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

namespace App\Tests\Unit\Controller\Admin;

use App\Controller\Admin\LogController;
use App\Document\Log;
use App\Tests\UnitAdmin\Controller\BaseCrudControllerAdminTest;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LogControllerTest - for all unit tests in Admin LogController
 * by EasyAdminBundle with Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 106 - Business process - Log
 * @link https://www.atlassian.com/software/confluence/bp/106
 *
 * @package App\Tests\Unit\Controller\Admin
 */
class LogControllerTest extends BaseCrudControllerAdminTest
{
    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return LogController::class;
    }

    /**
     * @return string
     */
    protected static function getEntityName(): string
    {
        return Log::class;
    }

    /**
     * @testCase 1023 - Unit test edit action for LogController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1023
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2023 - For LogController edit action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2023
     * @bp 106 - Business process - Log
     * @link https://www.atlassian.com/software/confluence/bp/106
     *     Arrange:
     * AUTH Mock - Admin
     * adminContext Mock - Log
     * loginUser - Admin
     *     Act:
     * POST /en/admin?routeName=log_edit&id=65a31f12da81997f160c61f2
     * CrudPostParameters - Log
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     */
    public function testLoggedInEditPost(): void
    {
        $this->authMock();
        $this->adminContextMock('Log', 0);
        $this->formMock(true, true, $this->log);
        $this->logServiceEditSaveLogMock();
        $this->client->loginUser($this->admin);
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlLogRouteName('routeName=log_edit&id=' . $this->log->getId()),
            $this->getCrudPostParametersLogEdit($this->getTokenConst())
        );
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1024 - Unit new action for LogController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1024
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2024 - For LogController new action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2024
     * @bp 106 - Business process - Log
     * @link https://www.atlassian.com/software/confluence/bp/106
     *     Arrange:
     * AUTH - Admin
     * adminContext - Log
     * loginUser - Admin
     *     Act:
     * POST /en/admin?routeName=log_add
     * CrudPostParameters - Log
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     */
    public function testIntegrationLoggedInNewPost(): void
    {
        $this->authMock();
        $this->adminContextMock('Log', 0);
        $this->formMock(true, true, $this->log);
        $this->logServiceEditSaveLogMock(false);
        $this->client->loginUser($this->admin);
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlLogRouteName('routeName=log_add'),
            $this->getCrudPostParametersLogEdit($this->getTokenConst())
        );
        static::assertResponseRedirects();
    }
}
