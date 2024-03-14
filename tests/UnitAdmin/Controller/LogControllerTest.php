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

use App\Controller\Admin\LogController;
use App\Document\Log;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LogControllerTest - for all unit tests in Admin LogController by EasyAdminBundle
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 106 - Business process - Log
 * @link https://www.atlassian.com/software/confluence/bp/106
 *
 * @package App\Tests\UnitAdmin\Controller
 */
class LogControllerTest extends BaseCrudControllerAdmin
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
     * @testCase 1021 - Unit test index action for LogController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1021
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2021 - For LogController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2021
     * @bp 106 - Business process - Log
     * @link https://www.atlassian.com/software/confluence/bp/106
     *     Arrange:
     *     Act:
     * GET /en/admin?routeName=logs
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    public function testIndex(): void
    {
        $this->client->request(Request::METHOD_GET, $this->getCrudUrlLogRouteName('routeName=logs'));
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1021 - Unit test index action for LogController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1021
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2021 - For LogController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2021
     * @bp 106 - Business process - Log
     * @link https://www.atlassian.com/software/confluence/bp/106
     *     Arrange:
     * AUTH Mock - Admin
     * paginator Mock - Logs
     * loginUser - Admin
     *     Act:
     * GET /en/admin?routeName=logs
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Crawler html contains string
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoggedInIndex(): void
    {
        $this->authMock();
        $this->paginatorLogMock();
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlLogRouteName('routeName=logs'));
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>', $html);
        $this->assertStringContainsString('Logs', $html);
    }

    /**
     * @testCase 1022 - Unit test detail action for LogController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1022
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2022 - For LogController detail action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2022
     * @bp 106 - Business process - Log
     * @link https://www.atlassian.com/software/confluence/bp/106
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?routeName=log_show&id=65a31f12da81997f160c61f2
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Crawler html contains string
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     */
    public function testLoggedInDetail(): void
    {
        $this->authMock();
        $this->adminContextMock('Log');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->getCrudUrlLogRouteName('routeName=log_show&id=' . $this->log->getId())
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>', $html);
        $this->assertStringContainsString($this->log->getId(), $html);
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
     * loginUser - Admin
     *     Act:
     * GET /en/admin?routeName=log_edit&id=65a31f12da81997f160c61f2
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
        $this->adminContextMock('Log');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->getCrudUrlLogRouteName('routeName=log_edit&id=' . $this->log->getId())
        );
        $html = $crawler->html();
        $this->assertIsString($html);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>', $html);
        $this->assertStringContainsString('Edit Log', $html);
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
        $this->adminContextMock('Log');
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlLogRouteName('routeName=log_edit&id=' . $this->log->getId()),
            $this->getCrudPostParametersLogEdit($this->getTokenConst())
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('<title>', $html);
        $this->assertStringContainsString('Edit Log', $html);
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
     * POST /en/admin?routeName=log_edit&id=123456789
     * CrudPostParameters - Log
     *   Document errors: log empty
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws Exception
     */
    public function testLoggedInEditPostEmpty(): void
    {
        $id = '123456789';
        $this->authMock();
        $this->logServiceGetLogMock($id);
        $this->client->loginUser($this->admin);
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlLogRouteName('routeName=log_edit&id=' . $id),
            $this->getCrudPostParametersLogEdit($this->getTokenConst())
        );
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1024 - Unit test new action for LogController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1024
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2024 - For LogController new action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2024
     * @bp 106 - Business process - Log
     * @link https://www.atlassian.com/software/confluence/bp/106
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?routeName=log_add
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
        $this->adminContextMock('Log', 0);
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->getCrudUrlLogRouteName('routeName=log_add')
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>', $html);
        $this->assertStringContainsString('Create Log', $html);
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
    public function testIntegrationLoggedInNewPost422(): void
    {
        $this->authMock();
        $this->adminContextMock('Log', 0);
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlLogRouteName('routeName=log_add'),
            $this->getCrudPostParametersLogEdit($this->getTokenConst())
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('<title>', $html);
        $this->assertStringContainsString('Create Log', $html);
    }

    /**
     * @testCase 1025 - Unit test delete action for LogController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1025
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2025 - For LogController delete action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2025
     * @bp 106 - Business process - Log
     * @link https://www.atlassian.com/software/confluence/bp/106
     *     Arrange:
     * AUTH Mock - Admin
     * loginUser - Admin
     *     Act:
     * DELETE /en/admin?routeName=log_delete&id=65a31f12da81997f160c61f2
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
        $this->adminContextMock('Log');
        $this->client->loginUser($this->admin);
        $this->client->request(
            Request::METHOD_DELETE,
            $this->getCrudUrlLogRouteName('routeName=log_delete&id=' . $this->log->getId())
        );
        static::assertResponseRedirects();
    }
}
