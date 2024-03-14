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

namespace App\Tests\Unit\Controller;

use App\Tests\Unit\BaseKernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SecurityControllerTest - for all unit tests in SecurityController
 * with Auth and without Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 108 - Business process - Security
 * @link https://www.atlassian.com/software/confluence/bp/108
 *
 * @package App\Tests\Unit\Controller
 */
class SecurityControllerTest extends BaseKernelTestCase
{
    /**
     * @testCase 1032 - Unit test registration action for SecurityController without AUTH - must be a success
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
     */
    public function testRegistration(): void
    {
        $request = Request::create(self::ROUTE_URL_REGISTRATION, Request::METHOD_GET);
        $response = $this->dispatch($request);

        $this->assertStringContainsString('Please registration', $response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @testCase 1033 - Unit test login action for SecurityController without AUTH - must be a success
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
     */
    public function testLogin(): void
    {
        $request = Request::create(self::ROUTE_URL_LOGIN, Request::METHOD_GET);
        $response = $this->dispatch($request);

        $this->assertStringContainsString('Please sign in', $response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @testCase 1034 - Unit test logout action for SecurityController without AUTH - must be a success
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
     */
    public function testLogout(): void
    {
        $request = Request::create(self::ROUTE_URL_LOGOUT, Request::METHOD_GET);
        $response = $this->dispatch($request);

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
