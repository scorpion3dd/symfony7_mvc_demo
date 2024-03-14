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

use App\Document\Log;
use App\Entity\Admin;
use App\Entity\Comment;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\RolePermission;
use App\Entity\User;
use App\Entity\UserRole;
use App\Factory\AdminFactory;
use App\Factory\CommentFactory;
use App\Factory\LogFactory;
use App\Factory\PermissionFactory;
use App\Factory\RoleFactory;
use App\Factory\RolePermissionFactory;
use App\Factory\UserFactory;
use App\Factory\UserRoleFactory;
use App\Repository\CommentRepositoryInterface;
use App\Repository\LogRepositoryInterface;
use App\Repository\PermissionRepositoryInterface;
use App\Repository\RoleRepositoryInterface;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryInterface;
use App\Security\UserFetcherInterface;
use App\Service\CommentServiceInterface;
use App\Service\LogServiceInterface;
use App\Service\UserServiceInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;
use Faker\Generator;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Monolog\Logger;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait TestTrait
 * @package App\Tests
 * @property ContainerInterface $container
 * @property string $appDomain
 * @property Generator $faker
 * @property UserFactory $userFactory
 * @property UserRoleFactory $userRoleFactory
 * @property AdminFactory $adminFactory
 * @property CommentFactory $commentFactory
 * @property LogFactory $logFactory
 * @property RolePermissionFactory $rolePermissionFactory
 * @property RoleFactory $roleFactory
 * @property PermissionFactory $permissionFactory
 * @property EntityManager $entityManager
 */
trait TestTrait
{
    protected const AUTH_ADMIN_ID = 1;
    protected const AUTH_USERNAME = 'admin1';
    protected const AUTH_PASSWORD = 'admin1';
    protected const AUTH_PASSWORD_HACHED = '$2y$04$rOVUdw9OuBYHaBkWoNes..qAxyNZzBvaM6p5Ogy5w8BrALaOeJtXW';
    protected const TOKEN = '15342948cb343ad045570f55bd86.O08gVP6wkZfSpbyChmrhJ_54OQ0E8FLMi2WqmnB0Jeg.ZHdYA4j64MPq5t_z1juHd5AQd2NUghP17AHg-TQTdLEPKn8ButH44-fX2w';
    protected const AUTH_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.'
    .'eyJpYXQiOjE3MDM2MTc1NTYsImV4cCI6MTcwMzY1MzU1Niwicm9sZXMiOlsiUk9MRV9BRE1JTiJdLCJ1c2VybmFtZSI6ImFkbWluMSIsInVzZXIi'
    .'OnsicHJvZmlsZSI6eyJpZCI6bnVsbCwiY3VzdG9tIjoiY3VzdG9tIGRhdGEifX19.Ap5vr66pfgaeLJFJs-Wv-QBEgFHOHrLnw1aweduBKAu_Iur'
    .'IcED37Wapv-SBQ4rik9RRRUINr5svw_znIRj9TgSUD-UuP1McrdcVnvpORB4fMtrjbARiGRWmpeHi8Sq9N97zgKGpxlUr6wnftpwa6nZcOXF7EbS'
    .'qDLZRDjmqQ7xuaHBUw9aTpmsCKFxzo2SNxJudkDQI_-H1Gclf6kQ8PDSVhtvzJwE8oIkpeaHrwQcgIAL4xFXMwKvfTlxZVnzlnLZsdIheD5Y6pWK6'
    .'-TMZZaol7kRpKxijRGBWXp9hY5TExHeIwOXZ4XJwmCmONkzxTRtI2KenKU1hzplf0dEaNQ';
    protected const AUTH_REFRESH_TOKEN = 'ea59f38cf00583c21e443a20deaf0c4a927d87c6e31f254ed225648df6303249ab0ae6cac1b3677bf2c130a160d28cb767ee3ca8b3062d5ff0699f1b69975c92';

    protected const ROUTE_URL_INDEX = '/en/';
    protected const ROUTE_URL_INDEX_NO_LOCALE = '/';
    protected const ROUTE_URL_ABOUT = '/en/about';
    protected const ROUTE_URL_LANGUAGE = '/en/language';
    protected const ROUTE_URL_SHOW = '/en/lottery/';
    protected const ROUTE_URL_COMMENT_REVIEW = '/admin/comment/review/';
    protected const ROUTE_URL_PURGE_HTTP_CACHE = '/admin/http-cache/users';
    protected const ROUTE_URL_REGISTRATION = '/en/registration';
    protected const ROUTE_URL_LOGIN = '/en/login';
    protected const ROUTE_URL_LOGOUT = '/en/logout';
    protected const ROUTE_URL_INDEX_EN = '/en/';
    protected const ROUTE_URL_INDEX_FR = '/fr/';
    protected const ROUTE_URL_ABOUT_EN = '/en/about';
    protected const ROUTE_URL_ABOUT_FR = '/fr/about';
    protected const ROUTE_URL_LANGUAGE_EN = '/en/language';
    protected const ROUTE_URL_LANGUAGE_FR = '/fr/language';
    protected const ROUTE_URL_SHOW_EN = '/en/lottery/';
    protected const ROUTE_URL_SHOW_FR = '/fr/lottery/';
    protected const ROUTE_URL_ADMIN = '/en/admin';

    protected const ROUTE_API_ME = '/api/me';
    protected const ROUTE_API_REGISTRATION = '/api/registration';
    protected const ROUTE_API_LOGOUT = '/api/logout';
    protected const ROUTE_API_LOGIN = '/api/login';
    protected const ROUTE_API_TOKEN_REFRESH = '/api/token/refresh';
    protected const ROUTE_API_HEALTH_CHECK = '/api/health-check';
    protected const ROUTE_API_ADMINS = '/api/admins';
    protected const ROUTE_API_ADMINS_LIST1 = '/api/admins/list1';
    protected const ROUTE_API_ADMINS_LIST2 = '/api/admins/list2';
    protected const ROUTE_API_ADMINS_1 = '/api/admins/1';
    protected const ROUTE_API_ADMINS_1_ITEM = '/api/admins/1/item';
    protected const ROUTE_API_USERS = '/api/users';
    protected const ROUTE_API_USERS_1 = '/api/users/1';
    protected const ROUTE_API_USERS_LOTTERY = '/api/users/lottery';
    protected const ROUTE_API_USERS_LOTTERY1 = '/api/users/lottery1';
    protected const ROUTE_API_USERS_LOTTERY2 = '/api/users/lottery2';
    protected const ROUTE_API_COMMENTS_UPLOAD = '/api/comments/upload';
    protected const ROUTE_API_ROLES = '/api/roles';
    protected const ROUTE_API_ROLES_1 = '/api/roles/1';
    protected const ROUTE_API_PERMISSIONS = '/api/permissions';
    protected const ROUTE_API_PERMISSIONS_1 = '/api/permissions/1';
    protected const ROUTE_API_COMMENTS = '/api/comments';
    protected const ROUTE_API_COMMENTS_USER = '/api/comments?userId=';
    protected const ROUTE_API_LOGS = '/api/logs';

    protected const API_CONTEXTS_LOG = '/api/contexts/Log';
    protected const API_CONTEXTS_COMMENT = '/api/contexts/Comment';
    protected const API_CONTEXTS_PERMISSION = '/api/contexts/Permission';
    protected const API_CONTEXTS_ROLE = '/api/contexts/Role';
    protected const API_CONTEXTS_USER = '/api/contexts/User';
    protected const API_CONTEXTS_ADMIN = '/api/contexts/Admin';

    protected const API_HYDRA_COLLECTION = 'hydra:Collection';
    protected const API_HYDRA_TOTAL_ITEMS = 'hydra:totalItems';
    protected const API_HYDRA_MEMBER = 'hydra:member';
    protected const API_HYDRA_VIEW = 'hydra:view';
    protected const API_HYDRA_SEARCH = 'hydra:search';

    protected const BEARER = 'Bearer';
    protected const HEADER_AUTHORIZATION = 'Authorization';
    protected const HEADER_ACCEPT = 'Accept';
    protected const HEADER_CONTENT_TYPE = 'Content-Type';
    protected const HEADER_ACCEPT_PATCH = 'accept-patch';
    protected const HEADER_MULTIPART_FORM_DATA = 'multipart/form-data';
    protected const HEADER_APPLICATION_JSON = 'application/json';
    protected const HEADER_APPLICATION_JSON_LD = 'application/ld+json';
    protected const HEADER_APPLICATION_JSON_MERGE = 'application/merge-patch+json';
    protected const HEADER_APPLICATION_JSON_CHARSET = 'application/ld+json; charset=utf-8';
    protected const HEADER_APPLICATION_CHARSET = 'charset=utf-8';

    /**
     * @param string $username
     * @param string $password
     *
     * @return Admin
     */
    protected function createAdmin(string $username, string $password): Admin
    {
        return $this->adminFactory->create($username, $password);
    }

    /**
     * @return User
     * @throws Exception
     */
    protected function createUser(): User
    {
        $genderId = User::randomGenderId();
        $gender = $genderId == User::GENDER_MALE_ID ? User::GENDER_MALE : User::GENDER_FEMALE;
        $createdAt = $this->faker->dateTimeBetween('-15 years', 'now');
        $beginerEmail = 'resident';
        return $this->userFactory->create(
            "$beginerEmail-1@{$this->appDomain}",
            $genderId,
            $this->faker->userName(),
            $this->faker->name($gender),
            $this->faker->text(1024),
            User::randomStatusId(),
            User::randomAccessId(),
            $this->faker->dateTimeBetween('-50 years', '-20 years'),
            $createdAt,
            $this->faker->dateTimeBetween($createdAt, 'now'),
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function createUserFio(): string
    {
        $genderId = User::randomGenderId();
        $gender = $genderId == User::GENDER_MALE_ID ? User::GENDER_MALE : User::GENDER_FEMALE;

        return $this->faker->name($gender);
    }

    /**
     * @param string $token
     *
     * @return array[]
     * @throws Exception
     */
    protected function getPostParametersComment(string $token = 'c681802d8b680.jOpGgsdhNyKIVcg1tkaU99BSxgTnw-o6XYOgbABvsWc.1ZA885MkeVLbEoN76X7DgLwVt2KK7tl0EeXRQVM_9hDnsgrAvi9bSP4whQ'): array
    {
        return [
            'comment_form' => [
                'author' => $this->createUserFio(),
                'text' => $this->faker->text(100),
                'email' => $this->faker->email(),
                'submit' => '',
                '_token' => $token,
            ]
        ];
    }

    /**
     * @param string $fullFileName
     * @return array[]
     */
    protected function getPostFilesComment(string $fullFileName = ''): array
    {
        $tmpPath = '';
        $name = '';
        $type = '';
        $size = 0;
        if (file_exists($fullFileName)) {
            $tmpFile = tmpfile();
            $tmpPath = stream_get_meta_data($tmpFile)['uri'];
            $content = file_get_contents($fullFileName);
            $res = file_put_contents($tmpPath, $content);
            if ($res && file_exists($tmpPath)) {
                $name = basename($fullFileName);
                $type = mime_content_type($fullFileName);
                $size = filesize($fullFileName);
            }
        }

        return [
            'comment_form' => [
                'name' => ['photo' => ($name == '') ? 'london1.jpg' : $name],
                'full_path' => ['photo' => ($name == '') ? 'london1.jpg' : $name],
                'type' => ['photo' => ($type == '') ? 'image/jpeg' : $type],
                'tmp_name' => ['photo' => ($tmpPath == '') ? '/tmp/phpq3xMGk' : $tmpPath],
                'error' => ['photo' => 0],
                'size' => ['photo' => ($size == 0) ? 202246 : $size],
            ]
        ];
    }

    /**
     * @return array[]
     */
    protected function getServerUploadedFile(): array
    {
        return [
            'CONTENT_TYPE' => self::HEADER_MULTIPART_FORM_DATA
        ];
    }

    /**
     * @param string $fullFileName
     * @param string $suffix
     *
     * @return string
     */
    protected function getFullFileNameTo(string $fullFileName = '', string $suffix = '_test'): string
    {
        $pathInfo = pathinfo($fullFileName);
        $path = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        return $path . '/' . $filename . $suffix .'.' . $extension;
    }

    /**
     * @param UploadedFile|null $file
     * @param array $parameters
     *
     * @return array
     */
    protected function getPostCommentUploadedFileOptions(?UploadedFile $file, array $parameters = []): array
    {
        $options = [
            'headers' => [self::HEADER_CONTENT_TYPE => self::HEADER_MULTIPART_FORM_DATA],
            'extra' => ['parameters' => $parameters]
        ];
        if (isset($file)) {
            $options['extra']['files'] = ['photoFile' => $file];
        }

        return $options;
    }

    /**
     * @param string $fullFileName
     *
     * @return UploadedFile|null
     */
    protected function getPostCommentUploadedFile(string $fullFileName = ''): ?UploadedFile
    {
        if (file_exists($fullFileName)) {
            $fullFileNameTo = $this->getFullFileNameTo($fullFileName);
            copy($fullFileName, $fullFileNameTo);
            if (file_exists($fullFileNameTo)) {
                $name = basename($fullFileNameTo);
                $type = mime_content_type($fullFileNameTo);

                return new UploadedFile($fullFileNameTo, $name, $type, null, true);
            }
        }

        return null;
    }

    /**
     * @param string $fullFileName
     *
     * @return array[]
     */
    protected function getPostFilesCommentUploadedFile(string $fullFileName = ''): array
    {
        $comment_form = [];
        if (file_exists($fullFileName)) {
            $fullFileNameTo = $this->getFullFileNameTo($fullFileName);
            copy($fullFileName, $fullFileNameTo);
            if (file_exists($fullFileNameTo)) {
                $name = basename($fullFileNameTo);
                $type = mime_content_type($fullFileNameTo);
                $comment_form = new UploadedFile($fullFileNameTo, $name, $type, null, true);
            }
        }

        return [
            'comment_form' => [
                'photo' => $comment_form
            ]
        ];
    }

    /**
     * @param User $user
     *
     * @return iterable
     * @throws Exception
     */
    protected function createComments(User $user): iterable
    {
        $comments = [];
        $comment = $this->createComment($user);
        $comment->setId(1);
        $comments[] = $comment;

        return $comments;
    }

    /**
     * @param User $user
     *
     * @return Comment
     * @throws Exception
     */
    protected function createComment(User $user): Comment
    {
        $genderId = User::randomGenderId();
        $gender = $genderId == User::GENDER_MALE_ID ? User::GENDER_MALE : User::GENDER_FEMALE;

        return $this->commentFactory->create(
            $user,
            $this->faker->name($gender),
            $this->faker->email(),
            $this->faker->text(1024),
            Comment::randomStateComment()
        );
    }

    /**
     * @param string $type
     * @param mixed|null $data
     * @param array $options
     *
     * @return FormInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    /**
     * @return Log
     * @throws Exception
     */
    protected function createLog(): Log
    {
        $priority = Logger::DEBUG;

        return $this->logFactory->create($this->faker->text(100), $priority);
    }

    /**
     * @return RefreshToken
     */
    protected function createRefreshToken(): RefreshToken
    {
        return new RefreshToken();
    }

    /**
     * @return Role
     */
    protected function createRole(): Role
    {
        $parentRole = $this->roleFactory->create('parentRole', 'A person who is parentRole.');
        $parentRole->setId(1);
        $childRole = $this->roleFactory->create('childRole', 'A person who is childRole.');
        $childRole->setId(2);

        return $this->roleFactory->create('Human', 'A person who is human.', $parentRole, $childRole);
    }

    /**
     * @return Permission
     */
    protected function createPermission(): Permission
    {
        return $this->permissionFactory->create('Usa', 'Users from USA');
    }

    /**
     * @return iterable
     */
    protected function createRolePermissions(): iterable
    {
        $rolePermissions = [];
        $rolePermission = $this->createRolePermission();
        $rolePermission->setId(1);
        $rolePermissions[] = $rolePermission;

        return $rolePermissions;
    }

    /**
     * @return RolePermission
     */
    protected function createRolePermission(): RolePermission
    {
        $role = $this->createRole();
        $role->setId(1);
        $permission = $this->createPermission();
        $permission->setId(1);

        return $this->rolePermissionFactory->create($role, $permission);
    }

    /**
     * @param RolePermission $rolePermission
     * @param User $user
     *
     * @return UserRole
     */
    protected function createUserRole(RolePermission $rolePermission, User $user): UserRole
    {
        return $this->userRoleFactory->create($rolePermission, $user);
    }

    /**
     * @return MockObject
     */
    protected function userFetcherMock(): MockObject
    {
        return $this->getMockBuilder(UserFetcherInterface::class)
            ->onlyMethods(['getAuthUser', 'logout'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject
     */
    protected function commentServiceMock(): MockObject
    {
        return $this->getMockBuilder(CommentServiceInterface::class)
            ->onlyMethods(['send', 'sendAdminRecipients', 'sendNotificationMessage', 'savePhotoFile', 'savePhotoFileApi', 'reviewComment', 'getCommentPaginator', 'countOldRejected', 'deleteOldRejected', 'find', 'isProd', 'save', 'remove'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject
     */
    protected function storeMock(): MockObject
    {
        return $this->getMockBuilder(StoreInterface::class)
            ->onlyMethods(['purge', 'lookup', 'write', 'invalidate', 'lock', 'unlock', 'isLocked', 'cleanup'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject
     */
    protected function logServiceMock(): MockObject
    {
        return $this->getMockBuilder(LogServiceInterface::class)
            ->onlyMethods(['getLogsPaginator', 'getLog', 'editLog', 'save', 'remove'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject
     */
    protected function userServiceMock(): MockObject
    {
        return $this->getMockBuilder(UserServiceInterface::class)
            ->onlyMethods(['getUsersPaginator', 'createFakerUser', 'findUserByEmailQuery', 'getUsersLottery', 'getLotteryUsers', 'findOneByField', 'findByField', 'save', 'remove'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function userRepositoryMock(): MockObject
    {
        return $this->createMock(UserRepository::class);
    }

    /**
     * @return MockObject
     */
    protected function roleRepositoryMock(): MockObject
    {
        return $this->getMockBuilder(RoleRepositoryInterface::class)
            ->onlyMethods(['findOneBy', 'getAllDefaultRoles', 'save', 'remove', 'find', 'findAll', 'findBy', 'getClassName'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject
     */
    protected function logRepositoryMock(): MockObject
    {
        return $this->getMockBuilder(LogRepositoryInterface::class)
            ->onlyMethods(['findOneBy', 'save', 'remove', 'find', 'findAll', 'findBy', 'getClassName', 'findAllLogs', 'deleteAllLogs'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject
     */
    protected function permissionRepositoryMock(): MockObject
    {
        return $this->getMockBuilder(PermissionRepositoryInterface::class)
            ->onlyMethods(['findOneBy', 'save', 'remove', 'find', 'findAll', 'findBy', 'getClassName'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function createMockEntityRepository(): MockObject
    {
        return $this->createMock(EntityRepository::class);
    }

    /**
     * @return MockObject
     */
    protected function commentRepositoryMock(): MockObject
    {
        return $this->getMockBuilder(CommentRepositoryInterface::class)
            ->onlyMethods(['findOneBy', 'countOldRejected', 'deleteOldRejected', 'getComment', 'save', 'remove', 'find', 'findAll', 'findBy', 'getClassName'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function dispatch(Request $request): Response
    {
        /** @var HttpKernelInterface $kernel */
        return static::$kernel->handle($request, HttpKernelInterface::MAIN_REQUEST, false);
    }

    /**
     * @return void
     */
    protected function prepareDbMySqlMock(): void
    {
        $this->entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->onlyMethods([
                'getConnection', 'getRepository', 'persist', 'flush', 'remove', 'beginTransaction', 'commit', 'rollback'
            ])
            ->disableOriginalConstructor()
            ->getMock();
        $this->entityManager->expects($this->any())
            ->method('getConnection');
        $this->entityManager->expects($this->any())
            ->method('persist');
        $this->entityManager->expects($this->any())
            ->method('flush');
        $this->entityManager->expects($this->any())
            ->method('remove');
//        $this->container->set('doctrine.entitymanager.orm_default', $this->entityManager);
        $this->container->set(EntityManager::class, $this->entityManager);
    }

    /**
     * @param bool $isSubmitted
     * @param bool $isValid
     * @param object|null $data
     *
     * @return void
     */
    protected function formMock(bool $isSubmitted = true, bool $isValid = true, ?object $data = null): void
    {
        $formMock = $this->getMockBuilder(FormInterface::class)
            ->onlyMethods(['isValid', 'setParent', 'getParent', 'add', 'get', 'has', 'remove', 'all', 'getErrors', 'setData', 'getData', 'getNormData', 'getViewData', 'getExtraData', 'getConfig', 'isSubmitted', 'getName', 'getPropertyPath', 'addError', 'isRequired', 'isDisabled', 'isEmpty', 'isSynchronized', 'getTransformationFailure', 'initialize', 'handleRequest', 'submit', 'getRoot', 'isRoot', 'createView', 'offsetExists', 'offsetGet', 'offsetSet', 'offsetUnset', 'count'])
            ->disableOriginalConstructor()
            ->getMock();
        $formMock->expects($this->exactly(1))
            ->method('isSubmitted')
            ->willReturn($isSubmitted);
        if ($isSubmitted == true) {
            $formMock->expects($this->exactly(1))
                ->method('isValid')
                ->willReturn($isValid);
        }
        if (isset($data)) {
            $formMock->expects($this->exactly(1))
                ->method('getData')
                ->willReturn($data);
        }
        $formFactoryMock = $this->getMockBuilder(FormFactoryInterface::class)
            ->onlyMethods(['create', 'createNamed', 'createForProperty', 'createBuilder', 'createNamedBuilder', 'createBuilderForProperty'])
            ->disableOriginalConstructor()
            ->getMock();
        $formFactoryMock->expects($this->exactly(1))
            ->method('create')
            ->willReturn($formMock);
        $this->container->set(FormFactoryInterface::class, $formFactoryMock);
    }

    /**
     * @param string|null $token
     *
     * @return string[]
     */
    protected function getHeaders(?string $token = null): array
    {
        $headers = [
            self::HEADER_ACCEPT => self::HEADER_APPLICATION_JSON_LD,
            self::HEADER_CONTENT_TYPE => self::HEADER_APPLICATION_JSON,
        ];
        if ($token) {
            $headers[self::HEADER_AUTHORIZATION] = self::BEARER . ' ' . $token;
        }

        return $headers;
    }

    /**
     * @return void
     */
    protected function createAdminAuth(): void
    {
        $this->admin = $this->adminFactory->create(self::AUTH_USERNAME, self::AUTH_PASSWORD_HACHED);
        $this->admin->setId(self::AUTH_ADMIN_ID);
        $this->admin->setToken(self::AUTH_TOKEN);
        $this->admin->setRefreshToken(self::AUTH_REFRESH_TOKEN);
    }
}
