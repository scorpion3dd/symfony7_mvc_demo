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

use App\Controller\Admin\DashboardController;
use Doctrine\ORM\Exception\NotSupported;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DashboardControllerIntegrationTest - for all integration tests
 * in Admin DashboardController by EasyAdminBundle with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 105 - Business process - Dashboard
 * @link https://www.atlassian.com/software/confluence/bp/105
 *
 * @package App\Tests\Integration\Admin
 */
class DashboardControllerIntegrationTest extends BaseCrudControllerIntegration
{
    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return DashboardController::class;
    }

    /**
     * @testCase 1020 - Integration test index action for DashboardController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1020
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2020 - For DashboardController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2020
     * @bp 105 - Business process - Dashboard
     * @link https://www.atlassian.com/software/confluence/bp/105
     *     Arrange:
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CDashboardController
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    public function testIntegrationIndex(): void
    {
        $this->client->request(Request::METHOD_GET, $this->getCrudUrlDashboardIndex());
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1020 - Integration test index action for DashboardController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1020
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2020 - For DashboardController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2020
     * @bp 105 - Business process - Dashboard
     * @link https://www.atlassian.com/software/confluence/bp/105
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CDashboardController
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
        $crawler = $this->client->request(Request::METHOD_GET, $this->getCrudUrlDashboardIndex());
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Dashboard</title>', $html);
    }

    /**
     * @testCase 1020 - Integration test index action for DashboardController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1020
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2020 - For DashboardController index action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2020
     * @bp 105 - Business process - Dashboard
     * @link https://www.atlassian.com/software/confluence/bp/105
     *     Arrange:
     * AUTH - Admin
     * loginUser - Admin
     *     Act:
     * GET /en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CDashboardController
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Crawler html contains string
     *
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     * @throws NotSupported
     */
    public function testIntegrationLoggedInIndexRouteName(): void
    {
        $this->auth();
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->getCrudUrlDashboardIndexRouteName()
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('<title>Dashboard</title>', $html);
    }
}
