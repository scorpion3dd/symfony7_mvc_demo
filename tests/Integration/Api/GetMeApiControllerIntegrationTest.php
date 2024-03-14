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
 * Class GetMeControllerIntegrationTest - Integration tests for GetMeController with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Integration\Api
 */
class GetMeApiControllerIntegrationTest extends BaseApiControllerIntegrationTest
{
    /**
     * @testCase 1045 - Integration test invoke action for GetMeController with AUTH - must be a success
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
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testApiGetMe(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_ME);
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertJson($content);
        $this->assertJsonContains(['ulid' => 1]);
        $expected = '{"ulid":1,"identifier":"admin1","roles":["ROLE_ADMIN"]}';
        $this->assertJsonStringEqualsJsonString($expected, $content);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON);
        $this->assertResponseIsSuccessful();
    }
}
