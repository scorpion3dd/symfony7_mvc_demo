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
 * Class GetMeControllerTest - Unit tests for GetMeController with Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Unit\Controller
 */
class GetMeControllerTest extends BaseKernelTestCase
{
    /**
     * @testCase 1045 - Unit test invoke action for GetMeController with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1045
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2045 - For GetMeController invoke action with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2045
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     * Authorization: Bearer token
     * Accept: application/ld+json
     *     Act:
     * GET /api/me
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains Json
     *
     * https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html#doctrine-converter
     *
     * @return void
     * @throws Exception
     */
    public function testApiGetMe(): void
    {
        $this->createAdminAuth();

        $userFetcherMock = $this->userFetcherMock();
        $userFetcherMock->expects($this->exactly(1))
            ->method('getAuthUser')
            ->willReturn($this->admin);
        $this->container->set(UserFetcherInterface::class, $userFetcherMock);

        $request = Request::create(self::ROUTE_API_ME, Request::METHOD_GET, [], [], [], $this->getHeaders(self::AUTH_TOKEN));
        $response = $this->dispatch($request);
        $expected = '{"ulid":1,"identifier":"admin1","roles":["ROLE_ADMIN"]}';
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsString($expected, $content);
        $this->assertJsonStringEqualsJsonString($expected, $content);
        $this->assertJson($content);
    }
}
