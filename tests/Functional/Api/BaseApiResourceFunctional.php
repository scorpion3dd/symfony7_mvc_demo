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

namespace App\Tests\Functional\Api;

use App\Factory\LogFactory;
use App\Tests\BaseApiControllerIntegrationTest;
use App\Util\LoggerTrait;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Base class BaseApiResourceFunctional - for all functional tests
 * for API routes in Entities by ApiResource with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @link https://symfony.com/doc/current/testing.html#types-of-tests
 *
 * @package App\Tests\Integration\Api
 * @property EntityManager $entityManager
 * @property DocumentManager $documentManager
 */
class BaseApiResourceFunctional extends BaseApiControllerIntegrationTest
{
    use LoggerTrait;

    /** @var DocumentManager $documentManager */
    protected DocumentManager $documentManager;

    /** @var LogFactory $logFactory */
    protected LogFactory $logFactory;

    /** @var bool $assertByDb */
    protected bool $assertByDb = false;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->container->get(LoggerInterface::class);
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act1LoginPost(): void
    {
        $this->debugFunction(self::class, 'Act 1: POST ' . self::ROUTE_API_LOGIN);

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
     * @param string $route
     * @param string $context
     * @param string $id
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act2GetCollection(string $route, string $context, string $id): void
    {
        $this->debugFunction(self::class, 'Act 2: GET ' . $route);

        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, $route);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => $context]);
        $this->assertJsonContains(['@id' => $id]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertNotEmpty($json[self::API_HYDRA_MEMBER]);
        $this->assertNotEmpty($json[self::API_HYDRA_VIEW]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @param string $act
     * @param string $route
     * @param string $context
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function actGetCollectionList(string $act, string $route, string $context): void
    {
        $this->debugFunction(self::class, 'Act ' . $act . ': GET ' . $route);

        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, $route);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => $context]);
        $this->assertJsonContains(['@id' => $route]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertNotEmpty($json[self::API_HYDRA_MEMBER]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @param string $act
     * @param string $route
     * @param string $context
     * @param string $type
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function actGet(string $act, string $route, string $context, string $type): void
    {
        $this->debugFunction(self::class, 'Act ' . $act . ': GET ' . $route);

        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, $route);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => $context]);
        $this->assertJsonContains(['@id' => $route]);
        $this->assertJsonContains(['@type' => $type]);
        $this->assertEntity($content);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
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
    protected function actAdminGetMe(string $act, string $route): void
    {
        $this->debugFunction(self::class, 'Act ' . $act . ': GET ' . $route);

        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, $route);
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
    protected function actDelete(string $act, string $route, string $id): void
    {
        $this->debugFunction(self::class, 'Act ' . $act . ': DELETE ' . $route . '/1');

        $this->getClientWithCredentials()->request(
            Request::METHOD_DELETE,
            $route . '/' . $id
        );
        $this->assertResponseHeaderSame(self::HEADER_ACCEPT_PATCH, self::HEADER_APPLICATION_JSON_MERGE);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $act
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function actLogoutGet(string $act): void
    {
        $this->debugFunction(self::class, 'Act ' . $act . ': GET ' . self::ROUTE_API_LOGOUT);

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
}
