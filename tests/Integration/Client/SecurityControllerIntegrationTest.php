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

namespace App\Tests\Integration\Client;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SecurityControllerIntegrationTest - for all integration tests
 * in SecurityController with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 108 - Business process - Security
 * @link https://www.atlassian.com/software/confluence/bp/108
 *
 * @package App\Tests\Integration\Client
 */
class SecurityControllerIntegrationTest extends BaseControllerIntegration
{
    /**
     * @testCase 1032 - Integration test registration action for SecurityController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1032
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2032 - For SecurityController registration action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2032
     * @bp 108 - Business process - Security
     * @link https://www.atlassian.com/software/confluence/bp/108
     *     Arrange:
     * without AUTH
     *     Act:
     * GET /en/registration
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains string
     *
     * @return void
     * @throws Exception
     */
    public function testRegistration(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, self::ROUTE_URL_REGISTRATION);
        $html = $crawler->html();
        $this->assertStringContainsString('Please registration', $html);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @testCase 1033 - Integration test login action for SecurityController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1033
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2033 - For SecurityController login action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2033
     * @bp 108 - Business process - Security
     * @link https://www.atlassian.com/software/confluence/bp/108
     *     Arrange:
     * without AUTH
     *     Act:
     * GET /en/login
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains string
     *
     * @return void
     * @throws Exception
     */
    public function testLogin(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, self::ROUTE_URL_LOGIN);
        $html = $crawler->html();
        $this->assertStringContainsString('Please sign in', $html);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @testCase 1034 - Integration test logout action for SecurityController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1034
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2034 - For SecurityController logout action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2034
     * @bp 108 - Business process - Security
     * @link https://www.atlassian.com/software/confluence/bp/108
     *     Arrange:
     * without AUTH
     *     Act:
     * GET /en/logout
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws Exception
     */
    public function testLogout(): void
    {
        $this->client->request(Request::METHOD_GET, self::ROUTE_URL_LOGOUT);
        static::assertResponseRedirects();
    }
}
