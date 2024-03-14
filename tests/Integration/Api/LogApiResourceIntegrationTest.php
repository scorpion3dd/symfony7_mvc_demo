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
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class LogApiResourceIntegrationTest - Integration tests for API routes in
 * Log Entity by ApiResource with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Integration\Api
 */
class LogApiResourceIntegrationTest extends BaseApiControllerIntegrationTest
{
    /**
     * @testCase 1089 - Integration test GetCollection operation in Log Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1089
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2089 - For GetCollection operation in Log Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2089
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/logs
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 200 - HTTP_OK
     * Response Content contains Json
     * Response Header Content-Type contains application/ld+json; charset=utf-8
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testApiLogs(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_LOGS);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_LOG]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_LOGS]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertNotEmpty($json[self::API_HYDRA_MEMBER]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1090 - Integration test Get operation in Log Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1090
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2090 - For Get operation in Log Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2090
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/logs/1
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 200 - HTTP_OK
     * Response Content contains Json
     * Response Header Content-Type contains application/ld+json; charset=utf-8
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testApiLogsGet(): void
    {
        $logLast = $this->logLastRecord();
        $response = $this->getClientWithCredentials()->request(
            Request::METHOD_GET,
            self::ROUTE_API_LOGS  . '/' . $logLast->getId()
        );
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_LOG]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_LOGS  . '/' . $logLast->getId()]);
        $this->assertJsonContains(['@type' => 'Log']);
        $this->assertLog($content);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1091 - Integration test Post operation in Log Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1091
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2091 - For Post operation in Log Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2091
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request POST /api/logs
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 200 - HTTP_OK
     * Response Content contains Json
     * Response Header Content-Type contains application/ld+json; charset=utf-8
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testApiLogsPost(): void
    {
        $logNew = $this->createLog();
        $options = [
            'json' => [
                'extra' => $logNew->getExtra(),
                'message' => $logNew->getMessage() . ' example',
                'priority' => $logNew->getPriority(),
                'priorityName' => $logNew->getPriorityName(),
                'timestamp' => $logNew->getTimestamp(),
            ]
        ];
        $response = $this->getClientWithCredentials()->request(Request::METHOD_POST, self::ROUTE_API_LOGS, $options);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_LOG]);
        $this->assertJsonContains(['@type' => 'Log']);
        $this->assertJsonContains(['message' => $logNew->getMessage() . ' example']);
        $this->assertJsonContains(['priority' => $logNew->getPriority()]);
        $this->assertLog($content);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1092 - Integration test Patch operation in Log Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1092
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2092 - For Patch operation in Log Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2092
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request PATCH /api/logs/1
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 400 - HTTP_BAD_REQUEST
     * Response Header Content-Type contains application/ld+json
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testApiLogsPatch400(): void
    {
        $logLast = $this->logLastRecord();
        $logNew = $this->createLog();
        $options = [
            'json' => [
                'extra' => $logNew->getExtra(),
                'message' => $logNew->getMessage() . ' example',
                'priority' => $logNew->getPriority(),
                'priorityName' => $logNew->getPriorityName(),
                'timestamp' => $logNew->getTimestamp(),
            ]
        ];
        $this->getClientWithCredentials()->request(
            Request::METHOD_PATCH,
            self::ROUTE_API_LOGS . '/' . $logLast->getId(),
            $options
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @testCase 1093 - Integration test Delete operation in Log Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1093
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2093 - For Delete operation in Log Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2093
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request DELETE /api/logs/1
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 204 - HTTP_NO_CONTENT
     * Response Header accept-patch contains application/merge-patch+json
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testApiLogsDelete(): void
    {
        $logLast = $this->logLastRecord();
        $this->getClientWithCredentials()->request(
            Request::METHOD_DELETE,
            self::ROUTE_API_LOGS . '/' . $logLast->getId()
        );
        $this->assertResponseHeaderSame(self::HEADER_ACCEPT_PATCH, self::HEADER_APPLICATION_JSON_MERGE);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    /**
     * @testCase 1093 - Integration test Delete operation in Log Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1093
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2093 - For Delete operation in Log Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2093
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request DELETE /api/logs/1 - with wrong id
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 404 - HTTP_NOT_FOUND
     * Response Header Content-Type contains application/ld+json
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testApiLogsDelete404(): void
    {
        $logLast = $this->logLastRecord();
        $logId = $logLast->getId() ?? '';
        $logId++;
        $this->getClientWithCredentials()->request(
            Request::METHOD_DELETE,
            self::ROUTE_API_LOGS . '/' . $logId
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
