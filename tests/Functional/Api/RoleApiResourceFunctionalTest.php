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
 * Class RoleApiResourceFunctionalTest - for all functional tests for API routes
 * in Role Entity by ApiResource with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Functional\Api
 */
class RoleApiResourceFunctionalTest extends BaseApiResourceFunctional
{
    /**
     * @testCase 1076 - Functional test for all operations in Role Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1076
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2076 - For all operations in Role Entity by ApiResource with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2076
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
    public function testRoleApiResources(): void
    {
        $this->debugFunction(self::class, 'testRoleApiResources');

        $this->act1LoginPost();
        $this->actGetCollectionList('2', self::ROUTE_API_ROLES, self::API_CONTEXTS_ROLE);
        $this->actGet('3', self::ROUTE_API_ROLES_1, self::API_CONTEXTS_ROLE, 'Role');
        $this->act4Post();
        $this->act5Patch400();
        $roleLast = $this->roleLastRecord();
        $roleId = (string)$roleLast->getId() ?? '';
        $this->actDelete('6', self::ROUTE_API_ROLES, $roleId);
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
        $this->debugFunction(self::class, 'Act 4: POST ' . self::ROUTE_API_ROLES);

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
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act5Patch400(): void
    {
        $this->debugFunction(self::class, 'Act 5: PATCH ' . self::ROUTE_API_ROLES . '/1');

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
     * @param string $content
     *
     * @return void
     */
    protected function assertEntity(string $content): void
    {
        $this->assertRole($content);
    }
}
