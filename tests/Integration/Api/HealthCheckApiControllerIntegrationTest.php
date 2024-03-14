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

namespace App\Tests\Integration\Api;

use App\Tests\BaseApiControllerIntegrationTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class HealthCheckControllerIntegrationTest - Integration tests for HealthCheckController without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Integration\Api
 */
class HealthCheckApiControllerIntegrationTest extends BaseApiControllerIntegrationTest
{
    /**
     * @testCase 1046 - Integration test invoke action for HealthCheckController without AUTH - must be a success
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
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testApiHealthCheck(): void
    {
        $response = $this->getApiClient()->request(Request::METHOD_GET, self::ROUTE_API_HEALTH_CHECK);
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertJson($content);
        $this->assertJsonContains(['status' => 'ok']);
        $expected = '{"status":"ok"}';
        $this->assertJsonStringEqualsJsonString($expected, $content);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON);
        $this->assertResponseIsSuccessful();
    }
}
