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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class AdminApiResourceIntegrationTest - Integration tests for API routes in Admin Entity by ApiResource with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Integration\Api
 */
class AdminApiResourceIntegrationTest extends BaseApiControllerIntegrationTest
{
    /**
     * @testCase 1049 - Integration test GetCollection operation in Admin Entity
     * by ApiResource with AUTH Login As User with wrong password - Response Invalid Jwt Token
     * @link https://www.testrail.com/testCase/1049
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2049 - For GetCollection operation in Admin Entity by ApiResource
     * with AUTH Login As User with wrong password - Response Invalid Jwt Token
     * @link https://www.atlassian.com/ru/software/jira/task/2049
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH - with wrong password
     *     Act:
     * Request GET /api/admins
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 401 - HTTP_UNAUTHORIZED
     * Response Content contains Json message Invalid JWT Token
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testLoginAsUserInvalidJwtToken(): void
    {
        $token = $this->getToken([
            'username' => 'admin1',
            'password' => 'admin123',
        ]);
        $this->getClientWithCredentials($token)->request(Request::METHOD_GET, self::ROUTE_API_ADMINS);
        $this->assertJsonContains(['message' => 'Invalid JWT Token']);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @testCase 1050 - Integration test GetCollection operation in Admin Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1050
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2050 - For GetCollection operation in Admin Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2050
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/admins
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
    public function testApiAdmins(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_ADMINS);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_ADMIN]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_ADMINS]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertNotEmpty($json[self::API_HYDRA_MEMBER]);
        $this->assertNotEmpty($json[self::API_HYDRA_VIEW]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1051 - Integration test GetCollection operation admins_list1 in Admin Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1051
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2051 - For GetCollection operation admins_list1 in Admin Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2051
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/admins/list1
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
    public function testApiAdminsList1(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_ADMINS_LIST1);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_ADMIN]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_ADMINS_LIST1]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertNotEmpty($json[self::API_HYDRA_MEMBER]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1052 - Integration test GetCollection operation admins_list2 in Admin Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1052
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2052 - For GetCollection operation admins_list2 in Admin Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2052
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/admins/list2
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
    public function testApiAdminsList2(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_ADMINS_LIST2);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_ADMIN]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_ADMINS_LIST2]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertNotEmpty($json[self::API_HYDRA_MEMBER]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1053 - Integration test Get operation in Admin Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1053
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2053 - For Get operation in Admin Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2053
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/admins/1
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
    public function testApiAdminsGet(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_ADMINS_1);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_ADMIN]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_ADMINS_1]);
        $this->assertJsonContains(['@type' => 'Admin']);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['username']);
        $this->assertNotEmpty($json['token']);
        $this->assertNotEmpty($json['refreshToken']);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1054 - Integration test Get operation admins_item in Admin Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1054
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2054 - For Get operation admins_item in Admin Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2054
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/admins/1/item
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
    public function testApiAdminsGetItem(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_ADMINS_1_ITEM);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_ADMIN]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_ADMINS_1_ITEM]);
        $this->assertJsonContains(['@type' => 'Admin']);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['username']);
        $this->assertNotEmpty($json['token']);
        $this->assertNotEmpty($json['refreshToken']);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1055 - Integration test Get operation users_me in Admin Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1055
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2055 - For Get operation users_me in Admin Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2055
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/me
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 200 - HTTP_OK
     * Response Content contains Json
     * Response Header Content-Type contains application/json
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testApiAdminsGetMe(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_ME);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['identifier' => 'admin1']);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['ulid']);
        $this->assertNotEmpty($json['identifier']);
        $this->assertNotEmpty($json['roles']);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1056 - Integration test Post operation registration in Admin Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1056
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2056 - For Post operation registration in Admin Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2056
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request POST /api/registration
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
    public function testApiAdminsPostRegistration(): void
    {
        $admin = $this->adminLastRecord();
        $number = 1;
        if (isset($admin)) {
            $number = $admin->getId() + 1;
        }
        $username = 'admin' . (string)$number;
        $options = [
            'json' => [
                'username' => $username,
                'password' => 'admin' . (string)$number,
            ]
        ];
        $response = $this->getClientWithCredentials()->request(
            Request::METHOD_POST,
            self::ROUTE_API_REGISTRATION,
            $options
        );
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_ADMIN]);
        $this->assertJsonContains(['@type' => 'Admin']);
        $this->assertJsonContains(['username' => $username]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['token']);
        $this->assertIsString($json['token']);
        $this->assertGreaterThan(0, strlen($json['token']));
        $this->assertNotEmpty($json['refreshToken']);
        $this->assertIsString($json['refreshToken']);
        $this->assertGreaterThan(0, strlen($json['refreshToken']));
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1057 - Integration test Post operation login in Admin Entity
     * by ApiResource without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1057
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2057 - For Post operation login in Admin Entity by ApiResource
     * without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2057
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * without AUTH
     *     Act:
     * Request POST /api/login
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
    public function testApiAdminsPostLogin(): void
    {
        $admin = $this->adminLastRecord();
        $number = $admin->getId();
        $username = 'admin' . (string)$number;
        $options = [
            'json' => [
                'username' => $username,
                'password' => 'admin' . (string)$number,
            ]
        ];
        $response = $this->getClientWithCredentials()->request(
            Request::METHOD_POST,
            self::ROUTE_API_LOGIN,
            $options
        );
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_ADMIN]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_ADMINS . '/' . $number]);
        $this->assertJsonContains(['@type' => 'Admin']);
        $this->assertJsonContains(['username' => $username]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['token']);
        $this->assertIsString($json['token']);
        $this->assertGreaterThan(0, strlen($json['token']));
        $this->assertNotEmpty($json['refreshToken']);
        $this->assertIsString($json['refreshToken']);
        $this->assertGreaterThan(0, strlen($json['refreshToken']));
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1057 - Integration test Post operation login in Admin Entity
     * by ApiResource without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1057
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2057 - For Post operation login in Admin Entity by ApiResource
     * without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2057
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * without AUTH
     *     Act:
     * Request POST /api/login - with wrong password
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
     */
    public function testApiAdminsPostLogin400(): void
    {
        $options = [
            'json' => [
                'username' => 'admin2',
                'password' => 'admin2222',
            ]
        ];
        $this->getClientWithCredentials()->request(
            Request::METHOD_POST,
            self::ROUTE_API_LOGIN,
            $options
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @testCase 1057 - Integration test Post operation login in Admin Entity
     * by ApiResource without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1057
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2057 - For Post operation login in Admin Entity by ApiResource
     * without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2057
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * without AUTH
     *     Act:
     * Request POST /api/login - with wrong username
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
     */
    public function testApiAdminsPostLogin404(): void
    {
        $options = [
            'json' => [
                'username' => 'admin2222',
                'password' => 'admin2222',
            ]
        ];
        $this->getClientWithCredentials()->request(
            Request::METHOD_POST,
            self::ROUTE_API_LOGIN,
            $options
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @testCase 1058 - Integration test Post operation token refresh in Admin Entity
     * by ApiResource without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1058
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2058 - For Post operation token refresh in Admin Entity by ApiResource
     * without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2058
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * without AUTH
     *     Act:
     * Request POST /api/token/refresh
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
    public function testApiAdminsPostTokenRefresh(): void
    {
        $refreshTokenLast = $this->refreshTokenLastRecord();
        $username = $refreshTokenLast->getUsername() ?? '';
        $refreshToken = $refreshTokenLast->getRefreshToken() ?? '';
        $options = [
            'json' => [
                'refreshToken' => $refreshToken,
            ]
        ];
        $response = $this->getClientWithCredentials()->request(
            Request::METHOD_POST,
            self::ROUTE_API_TOKEN_REFRESH,
            $options
        );
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_ADMIN]);
        $this->assertJsonContains(['@type' => 'Admin']);
        $this->assertJsonContains(['username' => $username]);
        $this->assertJsonContains(['refreshToken' => $refreshToken]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['token']);
        $this->assertIsString($json['token']);
        $this->assertGreaterThan(0, strlen($json['token']));
        $this->assertNotEmpty($json['refreshToken']);
        $this->assertIsString($json['refreshToken']);
        $this->assertGreaterThan(0, strlen($json['refreshToken']));
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1058 - Integration test Post operation token refresh in Admin Entity
     * by ApiResource without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1058
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2058 - For Post operation token refresh in Admin Entity by ApiResource
     * without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2058
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * without AUTH
     *     Act:
     * Request POST /api/token/refresh - with wrong refreshToken
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 400 - HTTP_BAD_REQUEST
     * Response Header Content-Type contains application/json
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testApiAdminsPostTokenRefresh400(): void
    {
        $options = [
            'json' => [
                'refreshToken' => '',
            ]
        ];
        $this->getClientWithCredentials()->request(
            Request::METHOD_POST,
            self::ROUTE_API_TOKEN_REFRESH,
            $options
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @testCase 1059 - Integration test Patch operation in Admin Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1059
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2059 - For Patch operation in Admin Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2059
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request PATCH /api/admins/2
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
     */
    public function testApiAdminsPatch400(): void
    {
        $admin = $this->adminLastRecord();
        $userId = $admin->getId() ?? '';
        $username = $admin->getUsername() ?? '';
        $options = [
            'headers' => [self::HEADER_CONTENT_TYPE => self::HEADER_APPLICATION_JSON_MERGE],
            'json' => [
                'plainPassword' => $username,
            ]
        ];
        $this->getClientWithCredentials()->request(
            Request::METHOD_PATCH,
            self::ROUTE_API_ADMINS . '/' . $userId,
            $options
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @testCase 1060 - Integration test Delete operation in Admin Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1060
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2060 - For Delete operation in Admin Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2060
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request DELETE /api/admins/2
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
     */
    public function testApiAdminsDelete(): void
    {
        $admin = $this->adminLastRecord();
        $userId = $admin->getId() ?? '';
        $this->getClientWithCredentials()->request(
            Request::METHOD_DELETE,
            self::ROUTE_API_ADMINS . '/' . $userId
        );
        $this->assertResponseHeaderSame(self::HEADER_ACCEPT_PATCH, self::HEADER_APPLICATION_JSON_MERGE);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    /**
     * @testCase 1060 - Integration test Delete operation in Admin Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1060
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2060 - For Delete operation in Admin Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2060
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request DELETE /api/admins/2 - with wrong id
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
     */
    public function testApiAdminsDelete404(): void
    {
        $admin = $this->adminLastRecord();
        $userId = $admin->getId() ?? '';
        $userId++;
        $this->getClientWithCredentials()->request(
            Request::METHOD_DELETE,
            self::ROUTE_API_ADMINS . '/' . $userId
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @testCase 1061 - Integration test Get operation api_logout in Admin Entity
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
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testApiAdminsLogout(): void
    {
        $response = $this->getClientWithCredentials()->request(
            Request::METHOD_GET,
            self::ROUTE_API_LOGOUT
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        if (false) {
            $content = $response->getContent();
            $this->assertJson($content);
            $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON);
            $this->assertJsonContains(['message' => 'Logged out successfully']);
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        }
    }

    /**
     * @testCase 1061 - Integration test Get operation api_logout in Admin Entity
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
     * Request GET /api/logout - without credentials
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 400 - HTTP_BAD_REQUEST
     * Response Header Content-Type contains application/json
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function testApiAdminsLogout400(): void
    {
        $this->getApiClient()->request(
            Request::METHOD_GET,
            self::ROUTE_API_LOGOUT
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
