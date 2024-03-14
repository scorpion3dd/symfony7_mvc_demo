<?php
/**
 * This file is part of the Simple Web Demo Free Admin Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Symfony Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2023-2024 scorpion3dd
 */

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class LogApiResourceFunctionalTest - for all functional tests for API routes
 * in Log Entity by ApiResource with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Functional\Api
 */
class LogApiResourceFunctionalTest extends BaseApiResourceFunctional
{
    /**
     * @testCase 1094 - Functional test for all operations in Log Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1094
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2094 - For all operations in Log Entity by ApiResource with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2094
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * without AUTH
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testLogApiResources(): void
    {
        $this->debugFunction(self::class, 'testLogApiResources');

        $logLast = $this->logLastRecord();
        $logId = (string)$logLast->getId() ?? '';
        $this->act1LoginPost();
        $this->actGetCollectionList('2', self::ROUTE_API_LOGS, self::API_CONTEXTS_LOG);
        $this->actGet('3', self::ROUTE_API_LOGS . '/' . $logId, self::API_CONTEXTS_LOG, 'Log');
        $this->act4Post();
        $this->act5Patch400();
        $this->actDelete('6', self::ROUTE_API_LOGS, $logId);
        $this->actLogoutGet('7');
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act4Post(): void
    {
        $this->debugFunction(self::class, 'Act 4: POST ' . self::ROUTE_API_LOGS);

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
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act5Patch400(): void
    {
        $this->debugFunction(self::class, 'Act 5: PATCH ' . self::ROUTE_API_LOGS . '/1');

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
     * @param string $content
     *
     * @return void
     */
    protected function assertEntity(string $content): void
    {
        $this->assertLog($content);
    }
}
