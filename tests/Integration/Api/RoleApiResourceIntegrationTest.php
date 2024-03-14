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
 * Class RoleApiResourceIntegrationTest - Integration tests for API routes in
 * Role Entity by ApiResource with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Integration\Api
 */
class RoleApiResourceIntegrationTest extends BaseApiControllerIntegrationTest
{
    /**
     * @testCase 1071 - Integration test GetCollection operation in Role Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1071
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2071 - For GetCollection operation in Role Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2071
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/roles
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
    public function testApiRoles(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_ROLES);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_ROLE]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_ROLES]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertNotEmpty($json[self::API_HYDRA_MEMBER]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1072 - Integration test Get operation in Role Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1072
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2072 - For Get operation in Role Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2072
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/roles/1
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
    public function testApiRolesGet(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_ROLES_1);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_ROLE]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_ROLES_1]);
        $this->assertJsonContains(['@type' => 'Role']);
        $this->assertRole($content);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1073 - Integration test Post operation in Role Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1073
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2073 - For Post operation in Role Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2073
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request POST /api/roles
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
    public function testApiRolesPost(): void
    {
        $roleLast = $this->roleLastRecord();
        $number = 1;
        if (isset($roleLast)) {
            $number = $roleLast->getId() + 1;
        }
        $name = 'role' . (string)$number;
        $roleNew = $this->createRole();
        $options = [
            'json' => [
                'name' => $name,
                'description' => $roleNew->getDescription(),
                'permissions' => $roleNew->getPermissionsArrayIri(),
                'dateCreated' => $roleNew->getDateCreated()->format('Y-m-d'),
            ]
        ];
        $response = $this->getClientWithCredentials()->request(Request::METHOD_POST, self::ROUTE_API_ROLES, $options);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_ROLE]);
        $this->assertJsonContains(['@type' => 'Role']);
        $this->assertJsonContains(['name' => $name]);
        $this->assertJsonContains(['description' => $roleNew->getDescription()]);
        $this->assertRole($content);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1074 - Integration test Patch operation in Role Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1074
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2074 - For Patch operation in Role Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2074
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request PATCH /api/roles/1
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
    public function testApiRolesPatch400(): void
    {
        $roleLast = $this->roleLastRecord();
        $number = 1;
        if (isset($roleLast)) {
            $number = $roleLast->getId() + 1;
        }
        $name = 'role' . (string)$number;
        $roleNew = $this->createRole();
        $options = [
            'json' => [
                'name' => $name,
                'description' => $roleNew->getDescription(),
                'permissions' => $roleNew->getPermissionsArrayIri(),
                'dateCreated' => $roleNew->getDateCreated()->format('Y-m-d'),
            ]
        ];
        $this->getClientWithCredentials()->request(
            Request::METHOD_PATCH,
            self::ROUTE_API_ROLES . '/' . $roleLast->getId(),
            $options
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @testCase 1075 - Integration test Delete operation in Role Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1075
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2075 - For Delete operation in Role Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2075
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request DELETE /api/roles/1
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
    public function testApiRolesDelete(): void
    {
        $roleLast = $this->roleLastRecord();
        $this->getClientWithCredentials()->request(
            Request::METHOD_DELETE,
            self::ROUTE_API_ROLES . '/' . $roleLast->getId()
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @testCase 1075 - Integration test Delete operation in Role Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1075
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2075 - For Delete operation in Role Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2075
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request DELETE /api/roles/1 - with wrong id
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
    public function testApiRolesDelete404(): void
    {
        $roleLast = $this->roleLastRecord();
        $roleId = $roleLast->getId() ?? '';
        $roleId++;
        $this->getClientWithCredentials()->request(
            Request::METHOD_DELETE,
            self::ROUTE_API_ROLES . '/' . $roleId
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
