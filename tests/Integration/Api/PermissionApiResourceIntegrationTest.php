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
 * Class PermissionApiResourceIntegrationTest - Integration tests for API routes in
 * Permission Entity by ApiResource with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Integration\Api
 */
class PermissionApiResourceIntegrationTest extends BaseApiControllerIntegrationTest
{
    /**
     * @testCase 1077 - Integration test GetCollection operation in Permission Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1077
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2077 - For GetCollection operation in Permission Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2077
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/permissions
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
    public function testApiPermissions(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_PERMISSIONS);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_PERMISSION]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_PERMISSIONS]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertNotEmpty($json[self::API_HYDRA_MEMBER]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1078 - Integration test Get operation in Permission Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1078
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2078 - For Get operation in Permission Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2078
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/permissions/1
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
    public function testApiPermissionsGet(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_PERMISSIONS_1);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_PERMISSION]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_PERMISSIONS_1]);
        $this->assertJsonContains(['@type' => 'Permission']);
        $this->assertPermission($content);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1079 - Integration test Post operation in Permission Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1079
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2079 - For Post operation in Permission Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2079
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request POST /api/permissions
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
    public function testApiPermissionsPost(): void
    {
        $permissionLast = $this->permissionLastRecord();
        $number = 1;
        if (isset($permissionLast)) {
            $number = $permissionLast->getId() + 1;
        }
        $name = 'permission' . (string)$number;
        $permissionNew = $this->createPermission();
        $options = [
            'json' => [
                'name' => $name,
                'description' => $permissionNew->getDescription(),
                'roles' => $permissionNew->getRolesArrayIri(),
                'dateCreated' => $permissionNew->getDateCreated()->format('Y-m-d'),
            ]
        ];
        $response = $this->getClientWithCredentials()->request(Request::METHOD_POST, self::ROUTE_API_PERMISSIONS, $options);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_PERMISSION]);
        $this->assertJsonContains(['@type' => 'Permission']);
        $this->assertJsonContains(['name' => $name]);
        $this->assertJsonContains(['description' => $permissionNew->getDescription()]);
        $this->assertPermission($content);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1080 - Integration test Patch operation in Permission Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1080
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2080 - For Patch operation in Permission Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2080
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request PATCH /api/permissions/1
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
    public function testApiPermissionsPatch400(): void
    {
        $permissionLast = $this->permissionLastRecord();
        $number = 1;
        if (isset($permissionLast)) {
            $number = $permissionLast->getId() + 1;
        }
        $name = 'permission' . (string)$number;
        $permissionNew = $this->createPermission();
        $options = [
            'json' => [
                'name' => $name,
                'description' => $permissionNew->getDescription(),
                'roles' => $permissionNew->getRolesArrayIri(),
                'dateCreated' => $permissionNew->getDateCreated()->format('Y-m-d'),
            ]
        ];
        $this->getClientWithCredentials()->request(
            Request::METHOD_PATCH,
            self::ROUTE_API_PERMISSIONS . '/' . $permissionLast->getId(),
            $options
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @testCase 1081 - Integration test Delete operation in Permission Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1081
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2081 - For Delete operation in Permission Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2081
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request DELETE /api/permissions/1
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
    public function testApiPermissionsDelete(): void
    {
        $permissionLast = $this->permissionLastRecord();
        $this->getClientWithCredentials()->request(
            Request::METHOD_DELETE,
            self::ROUTE_API_PERMISSIONS . '/' . $permissionLast->getId()
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @testCase 1081 - Integration test Delete operation in Permission Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1081
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2081 - For Delete operation in Permission Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2081
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request DELETE /api/permissions/1 - with wrong id
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
    public function testApiPermissionsDelete404(): void
    {
        $permissionLast = $this->permissionLastRecord();
        $permissionId = $permissionLast->getId() ?? '';
        $permissionId++;
        $this->getClientWithCredentials()->request(
            Request::METHOD_DELETE,
            self::ROUTE_API_PERMISSIONS . '/' . $permissionId
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
