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
 * Class AdminApiResourceFunctionalTest - for all functional tests for API routes
 * in Admin Entity by ApiResource with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Functional\Api
 */
class AdminApiResourceFunctionalTest extends BaseApiResourceFunctional
{
    /**
     * @testCase 1062 - Functional test for all operations in Admin Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1062
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2062 - For all operations in Admin Entity by ApiResource with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2062
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
    public function testAdminApiResources(): void
    {
        $this->debugFunction(self::class, 'testAdminApiResources');

        $this->act1LoginPost();
        $this->act2GetCollection(self::ROUTE_API_ADMINS, self::API_CONTEXTS_ADMIN, self::ROUTE_API_ADMINS);
        $this->actGetCollectionList('3', self::ROUTE_API_ADMINS_LIST1, self::API_CONTEXTS_ADMIN);
        $this->actGetCollectionList('4', self::ROUTE_API_ADMINS_LIST2, self::API_CONTEXTS_ADMIN);
        $this->actGet('5', self::ROUTE_API_ADMINS_1, self::API_CONTEXTS_ADMIN, 'Admin');
        $this->actGet('6', self::ROUTE_API_ADMINS_1_ITEM, self::API_CONTEXTS_ADMIN, 'Admin');
        $this->actAdminGetMe('7', self::ROUTE_API_ME);
        $this->act8AdminsPostRegistration();
        $this->act9AdminsPostTokenRefresh();
        $this->act10AdminsPatch400();
        $adminLast = $this->adminLastRecord();
        $adminId = (string)$adminLast->getId() ?? '';
        $this->actDelete('11', self::ROUTE_API_ADMINS, $adminId);
        $this->actLogoutGet('12');
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act8AdminsPostRegistration(): void
    {
        $this->debugFunction(self::class, 'Act 8: POST ' . self::ROUTE_API_REGISTRATION);

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
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act9AdminsPostTokenRefresh(): void
    {
        $this->debugFunction(self::class, 'Act 9: POST /api/token/refresh');

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
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act10AdminsPatch400(): void
    {
        $this->debugFunction(self::class, 'Act 10: PATCH ' . self::ROUTE_API_ADMINS . '/1');

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
     * @param string $content
     *
     * @return void
     */
    protected function assertEntity(string $content): void
    {
        $json = json_decode($content, true);

        $this->assertNotEmpty($json['username']);
        $this->assertNotEmpty($json['token']);
        $this->assertNotEmpty($json['refreshToken']);
    }
}
