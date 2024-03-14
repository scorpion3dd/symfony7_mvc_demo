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
 * Class PermissionApiResourceFunctionalTest - for all functional tests for API routes
 * in Permission Entity by ApiResource with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Functional\Api
 */
class PermissionApiResourceFunctionalTest extends BaseApiResourceFunctional
{
    /**
     * @testCase 1082 - Functional test for all operations in Permission Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1082
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2082 - For all operations in Permission Entity by ApiResource with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2082
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
    public function testPermissionApiResources(): void
    {
        $this->debugFunction(self::class, 'testPermissionApiResources');

        $this->act1LoginPost();
        $this->actGetCollectionList('2', self::ROUTE_API_PERMISSIONS, self::API_CONTEXTS_PERMISSION);
        $this->actGet('3', self::ROUTE_API_PERMISSIONS_1, self::API_CONTEXTS_PERMISSION, 'Permission');
        $this->act4Post();
        $this->act5Patch400();
        $permissionLast = $this->roleLastRecord();
        $permissionId = (string)$permissionLast->getId() ?? '';
        $this->act6Delete('6', self::ROUTE_API_PERMISSIONS, $permissionId);
        $this->actLogoutGet('7');
    }

    /**
     * @param string $act
     * @param string $route
     * @param string $id
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act6Delete(string $act, string $route, string $id): void
    {
        $this->debugFunction(self::class, 'Act ' . $act . ': DELETE ' . $route . '/1');

        $this->getClientWithCredentials()->request(
            Request::METHOD_DELETE,
            $route . '/' . $id
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
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
        $this->debugFunction(self::class, 'Act 4: POST ' . self::ROUTE_API_PERMISSIONS);

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
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act5Patch400(): void
    {
        $this->debugFunction(self::class, 'Act 5: PATCH ' . self::ROUTE_API_PERMISSIONS . '/1');

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
     * @param string $content
     *
     * @return void
     */
    protected function assertEntity(string $content): void
    {
        $this->assertPermission($content);
    }
}
