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

use App\Security\UserFetcherInterface;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LogoutControllerTest - Unit tests for LogoutController with Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Unit\Controller
 */
class LogoutControllerTest extends BaseKernelTestCase
{
    /**
     * @testCase 1061 - Unit test Get operation api_logout in Admin Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1061
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2061 - For Get operation api_logout in Admin Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2061
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/logout
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 200 - HTTP_OK
     * Response Header Content-Type contains application/json
     *
     * @return void
     * @throws Exception
     */
    public function testApiLogout(): void
    {
        $response = null;
        $userFetcherMock = $this->userFetcherMock();
        $userFetcherMock->expects($this->exactly(1))
            ->method('logout')
            ->willReturn($response);
        $this->container->set(UserFetcherInterface::class, $userFetcherMock);

        $request = Request::create(self::ROUTE_API_LOGOUT, Request::METHOD_GET, [], [], [], $this->getHeaders(self::AUTH_TOKEN));
        $response = $this->dispatch($request);
        $expected = '{"message":"Logged out successfully"}';
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsString($expected, $content);
        $this->assertJsonStringEqualsJsonString($expected, $content);
        $this->assertJson($content);
    }
}
