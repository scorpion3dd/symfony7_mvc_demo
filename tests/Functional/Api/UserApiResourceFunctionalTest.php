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
 * Class UserApiResourceFunctionalTest - for all functional tests for API routes
 * in User Entity by ApiResource with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Functional\Api
 */
class UserApiResourceFunctionalTest extends BaseApiResourceFunctional
{
    /**
     * @testCase 1070 - Functional test for all operations in User Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1070
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2070 - For all operations in User Entity by ApiResource with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2070
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
    public function testUserApiResources(): void
    {
        $this->debugFunction(self::class, 'testUserApiResources');

        $this->act1LoginPost();
        $this->act2GetCollection(self::ROUTE_API_USERS, self::API_CONTEXTS_USER, self::ROUTE_API_USERS);
        $this->actGetCollectionList('3', self::ROUTE_API_USERS_LOTTERY, self::API_CONTEXTS_USER);
        $this->actGetCollectionList('4', self::ROUTE_API_USERS_LOTTERY1, self::API_CONTEXTS_USER);
        $this->actGetCollectionList2('5', self::ROUTE_API_USERS_LOTTERY2);
        $this->actGet('6', self::ROUTE_API_USERS_1, self::API_CONTEXTS_USER, 'User');
        $this->act7Post();
        $this->act8Patch400();
        $userLast = $this->userLastRecord();
        $userId = (string)$userLast->getId() ?? '';
        $this->actDelete('9', self::ROUTE_API_USERS, $userId);
        $this->actLogoutGet('10');
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act7Post(): void
    {
        $this->debugFunction(self::class, 'Act 7: POST ' . self::ROUTE_API_USERS);

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
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act8Patch400(): void
    {
        $this->debugFunction(self::class, 'Act 8: PATCH ' . self::ROUTE_API_USERS . '/1');

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
     * @param string $act
     * @param string $route
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function actGetCollectionList2(string $act, string $route): void
    {
        $this->debugFunction(self::class, 'Act ' . $act . ': GET ' . $route);

        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, $route);
        $content = $response->getContent();
        $this->assertJson($content);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['users_lottery']);
        $this->assertUsersLottery($json['users_lottery']);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @param string $content
     *
     * @return void
     */
    protected function assertEntity(string $content): void
    {
        $this->assertUser($content, true);
        $json = json_decode($content, true);

        $this->assertNotEmpty($json['createdAt']);
        $this->assertIsString($json['createdAt']);

        $this->assertNotEmpty($json['updatedAt']);
        $this->assertIsString($json['updatedAt']);
    }
}
