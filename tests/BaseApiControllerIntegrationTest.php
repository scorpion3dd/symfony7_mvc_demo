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

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Document\Log;
use App\Entity\Admin;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Comment;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\RolePermission;
use App\Entity\User;
use App\Factory\CommentFactory;
use App\Factory\LogFactory;
use App\Factory\PermissionFactory;
use App\Factory\RoleFactory;
use App\Factory\RolePermissionFactory;
use App\Factory\UserFactory;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Abstract base class BaseControllerIntegrationTest - for all integration and functional
 * tests in API Controllers by API Platform with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests
 */
abstract class BaseApiControllerIntegrationTest extends ApiTestCase
{
    use TestTrait;

    /** @var string|null $token */
    protected ?string $token = null;

    /** @var string|null $username */
    protected ?string $username = null;

    /** @var string|null $refreshToken */
    protected ?string $refreshToken = null;

    /** @var ContainerInterface $container */
    protected ContainerInterface $container;

    /** @var EntityManagerInterface $entityManager */
    protected EntityManagerInterface $entityManager;

    /** @var DocumentManager $documentManager */
    protected DocumentManager $documentManager;

    /** @var string $appDomain */
    protected string $appDomain;

    /** @var Generator $faker */
    protected readonly Generator $faker;

    /** @var UserFactory $userFactory */
    protected UserFactory $userFactory;

    /** @var RoleFactory $roleFactory */
    protected RoleFactory $roleFactory;

    /** @var PermissionFactory $permissionFactory */
    protected PermissionFactory $permissionFactory;

    /** @var RolePermissionFactory $rolePermissionFactory */
    protected RolePermissionFactory $rolePermissionFactory;

    /** @var CommentFactory $commentFactory */
    protected CommentFactory $commentFactory;

    /** @var LogFactory $logFactory */
    protected LogFactory $logFactory;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->entityManager = $this->container->get(EntityManagerInterface::class);
        $this->documentManager = $this->container->get(DocumentManager::class);
        $this->appDomain = $this->container->getParameter('app.domain');
        $this->faker = \Faker\Factory::create();
        $this->userFactory = $this->container->get(UserFactory::class);
        $this->rolePermissionFactory = $this->container->get(RolePermissionFactory::class);
        $this->permissionFactory = $this->container->get(PermissionFactory::class);
        $this->roleFactory = $this->container->get(RoleFactory::class);
        $this->commentFactory = $this->container->get(CommentFactory::class);
        $this->logFactory = $this->container->get(LogFactory::class);
    }

    /**
     * @param string|null $token
     *
     * @return Client
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getClientWithCredentials(?string $token = null): Client
    {
        $token = $token ?? $this->getToken();

        return static::createClient([], ['headers' => $this->getHeaders($token)]);
    }

    /**
     * @return Client
     */
    protected function getApiClient(): Client
    {
        return static::createClient([], ['headers' => $this->getHeaders()]);
    }

    /**
     * @param array $body
     *
     * @return string
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getToken(array $body = []): string
    {
        if ($this->token) {
            return $this->token;
        }
        $response = static::createClient()->request(
            Request::METHOD_POST,
            self::ROUTE_API_LOGIN,
            [
                'json' => $body ?: [
                    'username' => self::AUTH_USERNAME,
                    'password' => self::AUTH_PASSWORD,
                ]
            ]
        );
        $statusCode = $response->getStatusCode();
        $token = '';
        if ($this->statusCodeIsSuccessful($statusCode)) {
            $this->assertResponseIsSuccessful();
            $data = $response->toArray();
            $token = $data['token'];
            $this->token = $data['token'];
            $this->refreshToken = $data['refreshToken'];
            $this->username = $data['username'];
        }

        return $token;
    }

    /**
     * @param int $statusCode
     *
     * @return bool
     */
    protected function statusCodeIsSuccessful(int $statusCode): bool
    {
        return in_array($statusCode, [
            Response::HTTP_OK,
            Response::HTTP_CREATED,
            Response::HTTP_ACCEPTED
        ]);
    }

    /**
     * @return Log|null
     */
    protected function logLastRecord(): ?Log
    {
        return $this->documentManager->getRepository(Log::class)->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * @return Comment|null
     */
    protected function commentLastRecord(): ?Comment
    {
        return $this->entityManager->getRepository(Comment::class)->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * @return Comment|null
     */
    protected function commentLastRecordPublished(): ?Comment
    {
        return $this->entityManager->getRepository(Comment::class)->findOneBy(['state' => 'published'], ['id' => 'DESC']);
    }

    /**
     * @return User|null
     */
    protected function userLastRecord(): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * @return Role|null
     */
    protected function roleLastRecord(): ?Role
    {
        return $this->entityManager->getRepository(Role::class)->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * @return Permission|null
     */
    protected function permissionLastRecord(): ?Permission
    {
        return $this->entityManager->getRepository(Permission::class)->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * @return RolePermission|null
     */
    protected function rolePermissionsLastRecord(): ?RolePermission
    {
        return $this->entityManager->getRepository(RolePermission::class)->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * @return Admin|null
     */
    protected function adminLastRecord(): ?Admin
    {
        return $this->entityManager->getRepository(Admin::class)->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * @return RefreshToken|null
     */
    protected function refreshTokenLastRecord(): ?RefreshToken
    {
        return $this->entityManager->getRepository(RefreshToken::class)->findOneBy([], ['id' => 'DESC']);
    }

    /**
     * @param array $usersLottery
     * @return void
     */
    protected function assertUsersLottery(array $usersLottery = []): void
    {
        $this->assertIsArray($usersLottery);
        $this->assertGreaterThan(0, count($usersLottery));
        foreach ($usersLottery as $userLottery) {
            $this->assertIsArray($userLottery);
            $this->assertGreaterThan(0, count($userLottery));
            $this->assertNotEmpty($userLottery['id']);
            $this->assertIsInt($userLottery['id']);
            $this->assertGreaterThan(0, $userLottery['id']);

            $this->assertNotEmpty($userLottery['uid']);
            $this->assertIsString($userLottery['uid']);

            $this->assertNotEmpty($userLottery['email']);
            $this->assertIsString($userLottery['email']);

            $this->assertNotEmpty($userLottery['fullName']);
            $this->assertIsString($userLottery['fullName']);

            $this->assertNotEmpty($userLottery['dateBirthday']);

            $this->assertNotEmpty($userLottery['gender']);
            $this->assertIsInt($userLottery['gender']);

            $this->assertNotEmpty($userLottery['slug']);
            $this->assertIsString($userLottery['slug']);

            $this->assertIsInt($userLottery['commentsCount']);
        }
    }

    /**
     * @param string $content
     * @param bool $isRolePermissions
     *
     * @return void
     */
    protected function assertUser(string $content = '', bool $isRolePermissions = false): void
    {
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['@id']);
        $this->assertIsString($json['@id']);
        $this->assertStringContainsString(self::ROUTE_API_USERS, $json['@id']);

        $this->assertNotEmpty($json['uid']);
        $this->assertIsString($json['uid']);

        $this->assertNotEmpty($json['username']);
        $this->assertIsString($json['username']);

        $this->assertNotEmpty($json['email']);
        $this->assertIsString($json['email']);

        $this->assertNotEmpty($json['fullName']);
        $this->assertIsString($json['fullName']);

        $this->assertNotEmpty($json['description']);
        $this->assertIsString($json['description']);

        $this->assertNotEmpty($json['status']);
        $this->assertIsInt($json['status']);

        $this->assertNotEmpty($json['access']);
        $this->assertIsInt($json['access']);

        $this->assertNotEmpty($json['gender']);
        $this->assertIsInt($json['gender']);

        $this->assertNotEmpty($json['dateBirthday']);
        $this->assertIsString($json['dateBirthday']);

        if ($isRolePermissions) {
            $this->assertNotEmpty($json['rolePermissions']);
            $this->assertIsArray($json['rolePermissions']);
        }
    }

    /**
     * @param string $content
     *
     * @return void
     */
    protected function assertRole(string $content = ''): void
    {
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['@id']);
        $this->assertIsString($json['@id']);
        $this->assertStringContainsString(self::ROUTE_API_ROLES, $json['@id']);

        $this->assertNotEmpty($json['name']);
        $this->assertIsString($json['name']);

        $this->assertNotEmpty($json['description']);
        $this->assertIsString($json['description']);

        $this->assertIsArray($json['permissions']);

        $this->assertNotEmpty($json['dateCreated']);
        $this->assertIsString($json['dateCreated']);
    }

    /**
     * @param string $content
     *
     * @return void
     */
    protected function assertPermission(string $content = ''): void
    {
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['@id']);
        $this->assertIsString($json['@id']);
        $this->assertStringContainsString(self::ROUTE_API_PERMISSIONS, $json['@id']);

        $this->assertNotEmpty($json['name']);
        $this->assertIsString($json['name']);

        $this->assertNotEmpty($json['description']);
        $this->assertIsString($json['description']);

        $this->assertIsArray($json['roles']);

        $this->assertNotEmpty($json['dateCreated']);
        $this->assertIsString($json['dateCreated']);
    }

    /**
     * @param string $content
     *
     * @return void
     */
    protected function assertComment(string $content = ''): void
    {
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['@id']);
        $this->assertIsString($json['@id']);
        $this->assertStringContainsString(self::ROUTE_API_COMMENTS, $json['@id']);

        $this->assertNotEmpty($json['author']);
        $this->assertIsString($json['author']);

        $this->assertNotEmpty($json['text']);
        $this->assertIsString($json['text']);

        $this->assertNotEmpty($json['email']);
        $this->assertIsString($json['email']);

        $this->assertNotEmpty($json['state']);
        $this->assertIsString($json['state']);

        $this->assertNotEmpty($json['user']);

        $this->assertNotEmpty($json['createdAt']);
        $this->assertIsString($json['createdAt']);
    }

    /**
     * @param string $content
     *
     * @return void
     */
    protected function assertLog(string $content = ''): void
    {
        $json = json_decode($content, true);
        $this->assertNotEmpty($json['@id']);
        $this->assertIsString($json['@id']);
        $this->assertStringContainsString(self::ROUTE_API_LOGS, $json['@id']);

        $this->assertNotEmpty($json['extra']);
        $this->assertIsArray($json['extra']);

        $this->assertNotEmpty($json['message']);
        $this->assertIsString($json['message']);

        $this->assertNotEmpty($json['priority']);
        $this->assertIsInt($json['priority']);

        $this->assertNotEmpty($json['priorityName']);
        $this->assertIsString($json['priorityName']);

        $this->assertNotEmpty($json['timestamp']);
        $this->assertIsString($json['timestamp']);
    }
}
