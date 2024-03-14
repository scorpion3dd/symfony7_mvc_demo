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
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HealthCheckControllerTest - Unit tests for HealthCheckController without Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Unit\Controller
 */
class HealthCheckControllerTest extends BaseKernelTestCase
{
    /**
     * @testCase 1046 - Unit test invoke action for HealthCheckController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1046
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2046 - For HealthCheckController invoke action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2046
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * without AUTH
     * Accept: application/ld+json
     *     Act:
     * GET /api/health-check
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains Json
     *
     * https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html#doctrine-converter
     *
     * @return void
     * @throws Exception
     */
    public function testApiHealthCheck(): void
    {
        $request = Request::create(self::ROUTE_API_HEALTH_CHECK, Request::METHOD_GET, [], [], [], $this->getHeaders());
        $response = $this->dispatch($request);
        $expected = '{"status":"ok"}';
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsString($expected, $content);
        $this->assertJsonStringEqualsJsonString($expected, $content);
        $this->assertJson($content);
    }
}
