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
 * Class UserApiResourceIntegrationTest - Integration tests for API routes in User Entity by ApiResource with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Integration\Api
 */
class UserApiResourceIntegrationTest extends BaseApiControllerIntegrationTest
{
    /**
     * @testCase 1063 - Integration test GetCollection operation in User Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1063
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2063 - For GetCollection operation in User Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2063
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/users
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
    public function testApiUsers(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_USERS);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_USER]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_USERS]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertNotEmpty($json[self::API_HYDRA_MEMBER]);
        $this->assertNotEmpty($json[self::API_HYDRA_VIEW]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1063 - Integration test GetCollection operation in User Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1063
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2063 - For GetCollection operation in User Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2063
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/users/lottery
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
    public function testApiUsersLottery(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_USERS_LOTTERY);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_USER]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_USERS_LOTTERY]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertNotEmpty($json[self::API_HYDRA_MEMBER]);
        $this->assertNotEmpty($json[self::API_HYDRA_VIEW]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1064 - Integration test GetCollection operation in User Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1064
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2064 - For GetCollection operation in User Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2064
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/users/lottery1
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
    public function testApiUsersLottery1(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_USERS_LOTTERY1);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_USER]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_USERS_LOTTERY1]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertNotEmpty($json[self::API_HYDRA_MEMBER]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1065 - Integration test GetCollection operation in User Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1065
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2065 - For GetCollection operation in User Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2065
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/users/lottery2
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
    public function testApiUsersLottery2(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_USERS_LOTTERY2);
        $content = $response->getContent();
        $this->assertJson($content);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['users_lottery']);
        $this->assertUsersLottery($json['users_lottery']);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1066 - Integration test Get operation in User Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1066
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2066 - For Get operation in User Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2066
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/users/1
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
    public function testApiUsersGet(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_USERS_1);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_USER]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_USERS_1]);
        $this->assertJsonContains(['@type' => 'User']);

        $this->assertUser($content, true);
        $json = json_decode($content, true);

        $this->assertNotEmpty($json['createdAt']);
        $this->assertIsString($json['createdAt']);

        $this->assertNotEmpty($json['updatedAt']);
        $this->assertIsString($json['updatedAt']);

        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1067 - Integration test Post operation in User Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1067
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2067 - For Post operation in User Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2067
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request POST /api/users
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
    public function testApiUsersPost(): void
    {
        $userLast = $this->userLastRecord();
        $number = 1;
        if (isset($userLast)) {
            $number = $userLast->getId() + 1;
        }
        $username = 'user' . (string)$number;
        $userNew = $this->createUser();
        $options = [
            'json' => [
                'username' => $username,
                'email' => $username . '@' . $this->appDomain,
                'fullName' => $userNew->getFullName(),
                'description' => $userNew->getDescription(),
                'status' => $userNew->getStatus(),
                'access' => $userNew->getAccess(),
                'gender' => $userNew->getGender(),
                'dateBirthday' => $userNew->getDateBirthday()->format('Y-m-d'),
            ]
        ];
        $isRolePermissions = false;
        if ($isRolePermissions) {
            $rolePermissions = [];
            $rolePermissionsLast = $this->rolePermissionsLastRecord();
            $rolePermissions[] = $rolePermissionsLast;
            $userNew->setRolePermissions($rolePermissions);
            $options['json']['rolePermissions'] = $userNew->getRolePermissionsArrayIri();
        }
        $response = $this->getClientWithCredentials()->request(Request::METHOD_POST, self::ROUTE_API_USERS, $options);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_USER]);
        $this->assertJsonContains(['@type' => 'User']);
        $this->assertJsonContains(['username' => $username]);
        $this->assertUser($content, $isRolePermissions);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1068 - Integration test Patch operation in User Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1068
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2068 - For Patch operation in User Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2068
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request PATCH /api/users/1
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
    public function testApiUsersPatch400(): void
    {
        $userLast = $this->userLastRecord();
        $userNew = $this->createUser();
        $options = [
            'json' => [
                'username' => $userLast->getUsername(),
                'email' => $userLast->getEmail(),
                'fullName' => $userNew->getFullName(),
                'description' => $userNew->getDescription(),
                'status' => $userNew->getStatus(),
                'access' => $userNew->getAccess(),
                'gender' => $userNew->getGender(),
                'dateBirthday' => $userNew->getDateBirthday()->format('Y-m-d'),
            ]
        ];
        $isRolePermissions = false;
        if ($isRolePermissions) {
            $rolePermissions = [];
            $rolePermissionsLast = $this->rolePermissionsLastRecord();
            $rolePermissions[] = $rolePermissionsLast;
            $userNew->setRolePermissions($rolePermissions);
            $options['json']['rolePermissions'] = $userNew->getRolePermissionsArrayIri();
        }
        $this->getClientWithCredentials()->request(
            Request::METHOD_PATCH,
            self::ROUTE_API_USERS . '/' . $userLast->getId(),
            $options
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @testCase 1069 - Integration test Delete operation in User Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1069
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2069 - For Delete operation in User Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2069
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request DELETE /api/users/1
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
    public function testApiUsersDelete(): void
    {
        $userLast = $this->userLastRecord();
        $this->getClientWithCredentials()->request(
            Request::METHOD_DELETE,
            self::ROUTE_API_USERS . '/' . $userLast->getId()
        );
        $this->assertResponseHeaderSame(self::HEADER_ACCEPT_PATCH, self::HEADER_APPLICATION_JSON_MERGE);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    /**
     * @testCase 1069 - Integration test Delete operation in User Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1069
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2069 - For Delete operation in User Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2069
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request DELETE /api/users/1 - with wrong id
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
    public function testApiUsersDelete404(): void
    {
        $userLast = $this->userLastRecord();
        $userId = $userLast->getId() ?? '';
        $userId++;
        $this->getClientWithCredentials()->request(
            Request::METHOD_DELETE,
            self::ROUTE_API_USERS . '/' . $userId
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
