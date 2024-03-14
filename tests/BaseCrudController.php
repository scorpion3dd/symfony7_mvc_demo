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

use App\Controller\Admin\DashboardController;
use App\Document\DocumentInterface;
use App\Document\Log;
use App\Entity\Admin;
use App\Entity\Comment;
use App\Entity\EntityInterface;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Factory\AdminFactory;
use App\Factory\CommentFactory;
use App\Factory\PermissionFactory;
use App\Factory\RoleFactory;
use App\Factory\RolePermissionFactory;
use App\Factory\UserFactory;
use App\Helper\ApplicationGlobals;
use App\Repository\AdminRepositoryInterface;
use App\Repository\CommentRepositoryInterface;
use App\Repository\LogRepositoryInterface;
use App\Repository\PermissionRepositoryInterface;
use App\Repository\RoleRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Util\LoggerTrait;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Doctrine\ODM\MongoDB\Repository\GridFSRepository;
use Doctrine\ODM\MongoDB\Repository\ViewRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Factory\EntityFactory;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Faker\Generator;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base class BaseCrudController - for all tests
 * in CrudControllers by EasyAdminBundle
 *
 * @link https://symfony.com/doc/current/testing.html#types-of-tests
 *
 * @package App\Tests
 */
class BaseCrudController extends AbstractCrudTestCase
{
    use TestTrait;
    use LoggerTrait;

    protected const SESSION_ID = 'session-mock-id';
    protected const MOCK_SESSID = 'MOCKSESSID';

    /** @var array|string[] $options */
    protected array $options = ['_locale' => 'en'];

    /** @var string|int|null $entityId */
    protected string|int|null $entityId = 1;

    /** @var string|null $entityId */
    protected string|null $logId = '65a31f12da81997f160c61f2';

    /** @var AdminFactory $adminFactory */
    protected AdminFactory $adminFactory;

    /** @var UserFactory $userFactory */
    protected UserFactory $userFactory;

    /** @var CommentFactory $commentFactory */
    protected CommentFactory $commentFactory;

    /** @var RoleFactory $roleFactory */
    protected RoleFactory $roleFactory;

    /** @var PermissionFactory $permissionFactory */
    protected PermissionFactory $permissionFactory;

    /** @var RolePermissionFactory $rolePermissionFactory */
    protected RolePermissionFactory $rolePermissionFactory;

    /** @var ContainerInterface $container */
    protected ContainerInterface $container;

    /** @var ApplicationGlobals $appGlobals */
    protected ApplicationGlobals $appGlobals;

    /** @var Admin $admin */
    protected Admin $admin;

    /** @var User|null $user */
    protected ?User $user;

    /** @var EntityInterface|DocumentInterface|null $entity */
    protected EntityInterface|DocumentInterface|null $entity = null;

    /** @var Role $role */
    protected Role $role;

    /** @var Log $log */
    protected Log $log;

    /** @var Permission $permission */
    protected Permission $permission;

    /** @var Comment $comment */
    protected Comment $comment;

    /** @var Generator $faker */
    protected readonly Generator $faker;

    /** @var string $appDomain */
    protected string $appDomain;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->container = static::getContainer();
        $this->rolePermissionFactory = $this->container->get(RolePermissionFactory::class);
        $this->permissionFactory = $this->container->get(PermissionFactory::class);
        $this->roleFactory = $this->container->get(RoleFactory::class);
        $this->userFactory = $this->container->get(UserFactory::class);
        $this->commentFactory = $this->container->get(CommentFactory::class);
        $this->adminFactory = $this->container->get(AdminFactory::class);
        $this->appGlobals = $this->container->get(ApplicationGlobals::class);
        $this->appGlobals->setType(ApplicationGlobals::TYPE_APP_TESTS);
        $this->faker = \Faker\Factory::create();
        $this->appDomain = $this->container->getParameter('app.domain');
        $this->logger = $this->container->get(LoggerInterface::class);
    }

    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return AbstractCrudController::class;
    }

    /**
     * @return string
     */
    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    /**
     * @param int|string|null $entityId
     */
    protected function setEntityId(int|string|null $entityId): void
    {
        $this->entityId = $entityId;
    }

    /**
     * @param int $exactly
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function authMock(int $exactly = 1): void
    {
        $this->createAdminAuth();

        $adminRepositoryMock = $this->createMock(EntityRepository::class);
        $adminRepositoryMock->expects($this->exactly($exactly))
            ->method('find')
            ->willReturn($this->admin);
        $this->container->set(AdminRepositoryInterface::class, $adminRepositoryMock);
    }

    /**
     * @param int $exactly
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function userRepositoryMockFind(int $exactly = 1): void
    {
        $userRepositoryMock = $this->userRepositoryMock();
        $userRepositoryMock->expects($this->exactly($exactly))
            ->method('find')
            ->willReturn($this->user);
        $this->container->set(UserRepositoryInterface::class, $userRepositoryMock);
    }

    /**
     * @param int $exactly
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function roleRepositoryMockFind(int $exactly = 1): void
    {
        $roleRepositoryMock = $this->createMockEntityRepository();
        $roleRepositoryMock->expects($this->exactly($exactly))
            ->method('find')
            ->willReturn($this->role);
        $this->container->set(RoleRepositoryInterface::class, $roleRepositoryMock);
    }

    /**
     * @param int $exactly
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function permissionRepositoryMockFind(int $exactly = 1): void
    {
        $permissionRepositoryMock = $this->createMockEntityRepository();
        $permissionRepositoryMock->expects($this->exactly($exactly))
            ->method('find')
            ->willReturn($this->permission);
        $this->container->set(PermissionRepositoryInterface::class, $permissionRepositoryMock);
    }

    /**
     * @param int $exactly
     *
     * @return void
     */
    protected function logRepositoryMockFindOneBy(int $exactly = 1): void
    {
        $logRepositoryMock = $this->logRepositoryMock();
        $logRepositoryMock->expects($this->exactly($exactly))
            ->method('findOneBy')
            ->willReturn($this->log);
        $this->container->set(LogRepositoryInterface::class, $logRepositoryMock);
    }

    /**
     * @param int $exactly
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function commentRepositoryMockFind(int $exactly = 1): void
    {
        $commentRepositoryMock = $this->createMockEntityRepository();
        $commentRepositoryMock->expects($this->exactly($exactly))
            ->method('find')
            ->willReturn($this->comment);
        $this->container->set(CommentRepositoryInterface::class, $commentRepositoryMock);
    }

    /**
     * @param string $type
     * @param int $exactly
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function adminContextMock(string $type, int $exactly = 1): void
    {
        if ($type == 'User') {
            $this->user = $this->createUser();
//            $this->user->setRolePermissions($this->createRolePermissions());
            if ($exactly > 0) {
                $this->user->setId($this->entityId);
                $this->userRepositoryMockFind($exactly);
            }
        } elseif ($type == 'Role') {
            $this->role = $this->createRole();
            if ($exactly > 0) {
                $this->role->setId($this->entityId);
                $this->roleRepositoryMockFind($exactly);
            }
        } elseif ($type == 'Permission') {
            $this->permission = $this->createPermission();
            if ($exactly > 0) {
                $this->permission->setId($this->entityId);
                $this->permissionRepositoryMockFind($exactly);
            }
        } elseif ($type == 'Log') {
            $this->log = $this->createLog();
            if ($exactly > 0) {
                $this->log->setId($this->logId);
                $this->logRepositoryMockFindOneBy($exactly);
            }
        } elseif ($type == 'Comment') {
            $user = $this->createUser();
            $user->setId($this->entityId);
            $this->comment = $this->createComment($user);
            if ($exactly > 0) {
                $this->comment->setId($this->entityId);
                $this->commentRepositoryMockFind($exactly);
            }
        } else {
            $entityMock = $this->getMockBuilder(EntityDto::class)
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();

            $entityFactory = $this->getMockBuilder(EntityFactory::class)
                ->onlyMethods(['create'])
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();
            $entityFactory->expects($this->exactly(1))
                ->method('create')
                ->willReturn($entityMock);
            $this->container->set(EntityFactory::class, $entityFactory);
        }
    }

    /**
     * @param User $user
     *
     * @return void
     */
    protected function objectManagerMock(User $user): void
    {
        $objectManagerMock = $this->getMockBuilder(ObjectManager::class)
            ->onlyMethods(['find', 'remove', 'persist', 'clear', 'detach', 'refresh', 'flush', 'getRepository', 'getClassMetadata', 'getMetadataFactory', 'initializeObject', 'contains'])
            ->disableOriginalConstructor()
            ->getMock();
        $objectManagerMock->expects($this->exactly(1))
            ->method('find')
            ->willReturn($user);
        $this->container->set(ObjectManager::class, $objectManagerMock);
    }

    /**
     * @param User $user
     *
     * @return void
     */
    protected function entityRepositoryMock(User $user): void
    {
        $adminRepositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find', 'createQueryBuilder', 'createResultSetMappingBuilder', 'createNamedQuery', 'createNativeNamedQuery', 'clear', 'findAll', 'findBy', 'findOneBy', 'count', 'getClassName', 'matching'])
            ->disableOriginalConstructor()
            ->getMock();
        $adminRepositoryMock->expects($this->exactly(1))
            ->method('find')
            ->willReturn($user);
        $this->container->set(EntityRepository::class, $adminRepositoryMock);
    }

    /**
     * @return string
     */
    protected function getTokenConst(): string
    {
        return self::TOKEN;
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getCrudPostParametersUserEdit(string $token = ''): array
    {
        return [
            'referrer' => '/en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController',
            'ea' => [
                'newForm' => [
                    'btn' => 'saveAndReturn',
                ],
            ],
            'User' => [
                'uid' => $this->user->getUid(),
                'username' => $this->user->getUsername(),
                'fullName' => $this->user->getFullName(),
                'gender' => $this->user->getGender(),
                'email' => $this->user->getEmail(),
                'description' => $this->user->getDescription(),
                'status' => $this->user->getStatus(),
                'access' => $this->user->getAccess(),
                'rolePermissions' => $this->user->getRolePermissionsArray(),
                'dateBirthday' => $this->user->getDateBirthday()->format('Y-m-d'),
                'comments' => $this->user->getCommentsArray(),
                '_token' => $token
            ]
        ];
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getCrudPostParametersUserNew(string $token = ''): array
    {
        $parameters = $this->getCrudPostParametersUserEdit($token);
        unset($parameters['User']['comments']);

        return $parameters;
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getCrudPostParametersRoleEdit(string $token = ''): array
    {
        return [
            'referrer' => '/en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController',
            'ea' => [
                'newForm' => [
                    'btn' => 'saveAndReturn',
                ],
            ],
            'Role' => [
                'name' => $this->role->getName(),
                'description' => $this->role->getDescription(),
                'dateCreated' => $this->role->getDateCreated()->format('Y-m-d'),
                '_token' => $token
            ]
        ];
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getCrudPostParametersRoleNew(string $token = ''): array
    {
        $parameters = $this->getCrudPostParametersRoleEdit($token);
        unset($parameters['Role']['dateCreated']);
        $parameters['Role']['permissions'] = [0 => '1'];

        return $parameters;
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getCrudPostParametersPermissionEdit(string $token = ''): array
    {
        return [
            'referrer' => '/en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CPermissionCrudController',
            'ea' => [
                'newForm' => [
                    'btn' => 'saveAndReturn',
                ],
            ],
            'Permission' => [
                'name' => $this->permission->getName(),
                'description' => $this->permission->getDescription(),
                'dateCreated' => $this->permission->getDateCreated()->format('Y-m-d'),
                '_token' => $token
            ]
        ];
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getCrudPostParametersPermissionNew(string $token = ''): array
    {
        $parameters = $this->getCrudPostParametersPermissionEdit($token);
        unset($parameters['Permission']['dateCreated']);
        $parameters['Permission']['roles'] = [0 => '1'];

        return $parameters;
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getCrudPostParametersCommentEdit(string $token = ''): array
    {
        return [
            'referrer' => '/en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CCommentCrudController',
            'ea' => [
                'newForm' => [
                    'btn' => 'saveAndReturn',
                ],
            ],
            'Comment' => [
                'state' => $this->comment->getState(),
                '_token' => $token
            ]
        ];
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getCrudPostParametersCommentNew(string $token = ''): array
    {
        $parameters = $this->getCrudPostParametersCommentEdit($token);
//        unset($parameters['Comment']['comments']);

        return $parameters;
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getCrudPostParametersLogEdit(string $token = ''): array
    {
        return [
            'referrer' => '/en/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CRoleCrudController',
            'ea' => [
                'newForm' => [
                    'btn' => 'saveAndReturn',
                ],
            ],
            'log_form' => [
                'message' => isset($this->log) ? $this->log->getMessage() : '',
                'priority' => isset($this->log) ? $this->log->getPriority() : 0,
                'extra' => isset($this->log) ? $this->log->getExtra() : [],
                'submit' => '',
                '_token' => $token
            ]
        ];
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getCrudPostParametersLogNew(string $token = ''): array
    {
        return $this->getCrudPostParametersLogEdit($token);
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getCrudPostParametersDelete(string $token = ''): array
    {
        return [
            'token' => $token
        ];
    }

    /**
     * @return string
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function getCrudUrlDashboardIndex(): string
    {
        return $this->getCrudUrl(
            Action::INDEX,
            null,
            $this->options,
            $this->getDashboardFqcn()
        );
    }

    /**
     * @return string
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function getCrudUrlDashboardIndexRouteName(): string
    {
        $uri = $this->getCrudUrl(
            Action::INDEX,
            null,
            $this->options,
            $this->getDashboardFqcn()
        );

        return $this->replaceTextAfterMark($uri, 'routeName=admin');
    }

    /**
     * @param string $query
     *
     * @return string
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function getCrudUrlLogRouteName(string $query): string
    {
        $uri = $this->getCrudUrl(
            Action::INDEX,
            null,
            $this->options,
            $this->getDashboardFqcn()
        );

        return $this->replaceTextAfterMark($uri, $query);
    }

    /**
     * @param string $input
     * @param string $replacement
     *
     * @return string
     */
    private function replaceTextAfterMark(string $input, string $replacement): string
    {
        $markPosition = strpos($input, '?');
        if ($markPosition !== false) {
            return substr($input, 0, $markPosition + 1) . $replacement;
        }

        return $input;
    }

    /**
     * @param string|null $urlLog
     *
     * @return string
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function getSelectorIndex(?string $urlLog): string
    {
        $href = $urlLog ?? $this->getCrudUrlIndex();

        return 'a.menu-item-contents[href*="' . $href . '"]';
    }

    /**
     * @return string
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function getCrudUrlIndex(): string
    {
        return $this->getCrudUrl(
            Action::INDEX,
            null,
            $this->options,
            $this->getDashboardFqcn(),
            $this->getControllerFqcn()
        );
    }

    /**
     * @return string
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function getCrudUrlDetail(): string
    {
        return $this->getCrudUrl(
            Action::DETAIL,
            $this->entityId,
            $this->options,
            $this->getDashboardFqcn(),
            $this->getControllerFqcn()
        );
    }

    /**
     * @return string
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function getCrudUrlEdit(): string
    {
        return $this->getCrudUrl(
            Action::EDIT,
            $this->entityId,
            $this->options,
            $this->getDashboardFqcn(),
            $this->getControllerFqcn()
        );
    }

    /**
     * @return string
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function getCrudUrlNew(): string
    {
        return $this->getCrudUrl(
            Action::NEW,
            null,
            $this->options,
            $this->getDashboardFqcn(),
            $this->getControllerFqcn()
        );
    }

    /**
     * @return string
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function getCrudUrlDelete(): string
    {
        return $this->getCrudUrl(
            Action::DELETE,
            $this->entityId,
            $this->options,
            $this->getDashboardFqcn(),
            $this->getControllerFqcn()
        );
    }

    /**
     * @param Crawler $crawler
     * @param string $selector
     *
     * @return Link
     * @throws Exception
     */
    protected function getLink(Crawler $crawler, string $selector): Link
    {
        $linkNodes = $crawler->filter($selector);
        if ($linkNodes->count() == 0) {
            $this->debugFunction(self::class, 'Link not found.');
            throw new Exception('Link not found.');
        }
        $link = $linkNodes->link();
        $this->debugParameters(self::class, ['link->Uri' => $link->getUri()]);

        return $link;
    }

    /**
     * @param Crawler $crawler
     * @param string $selector
     *
     * @return string
     */
    protected function getHref(Crawler $crawler, string $selector): string
    {
        $href = $crawler->filter($selector)->attr('href');
        $this->debugParameters(self::class, ['href' => $href]);

        return $href;
    }

    /**
     * @param string $uri
     * @param string $key
     *
     * @return string
     */
    protected function getValue(string $uri, string $key): string
    {
        $request = Request::create($uri);
        $value = (string)$request->query->get($key);
        $this->debugParameters(self::class, [$key => $value]);

        return $value;
    }

    /**
     * @param string $type
     * @param string $action
     *
     * @return void
     * @throws Exception
     */
    protected function adminContext(string $type, string $action = 'edit'): void
    {
        if ($type == 'User') {
            if ($action == 'edit') {
                $userRepository = $this->entityManager->getRepository(User::class);
                $this->user = $userRepository->find($this->entityId);
            } elseif ($action == 'new') {
                $this->user = $this->createUser();
                $comment = $this->createComment($this->user);
                $this->user->addComment($comment);
            }
        } elseif ($type == 'Role') {
            if ($action == 'edit') {
                $roleRepository = $this->entityManager->getRepository(Role::class);
                $this->role = $roleRepository->find($this->entityId);
            } elseif ($action == 'new') {
                $this->role = $this->createRole();
                $this->role->setName($this->role->getName() . ' same');
            }
        } elseif ($type == 'Permission') {
            if ($action == 'edit') {
                $permissionRepository = $this->entityManager->getRepository(Permission::class);
                $this->permission = $permissionRepository->find($this->entityId);
            } elseif ($action == 'new') {
                $this->permission = $this->createPermission();
                $this->permission->setName($this->permission->getName() . ' same');
            }
        } elseif ($type == 'Comment') {
            if ($action == 'edit') {
                $commentRepository = $this->entityManager->getRepository(Comment::class);
                $this->comment = $commentRepository->find($this->entityId);
            }
        } elseif ($type == 'Log') {
            if ($action == 'edit') {
                $logRepository = $this->documentManager->getRepository(Log::class);
                $log = $this->getLog($logRepository);
                if (isset($log)) {
                    $this->log = $log;
                }
            } elseif ($action == 'new') {
                $this->log = $this->createLog();
                $this->log->setMessage($this->log->getMessage() . ' same');
            }
        }
    }

    /**
     * @param DocumentRepository|GridFSRepository|ViewRepository $logRepository
     *
     * @return Log|null
     */
    protected function getLog($logRepository): ?Log
    {
        $log = $this->logFindOneBy($logRepository, Logger::INFO);
        if (! isset($log)) {
            $log = $this->logFindOneBy($logRepository, Logger::NOTICE);
            if (! isset($log)) {
                $log = $this->logFindOneBy($logRepository, Logger::WARNING);
                if (! isset($log)) {
                    $log = $this->logFindOneBy($logRepository, Logger::ERROR);
                    if (! isset($log)) {
                        $log = $this->logFindOneBy($logRepository, Logger::CRITICAL);
                        if (! isset($log)) {
                            $log = $this->logFindOneBy($logRepository, Logger::ALERT);
                            if (! isset($log)) {
                                $log = $this->logFindOneBy($logRepository, Logger::EMERGENCY);
                            }
                        }
                    }
                }
            }
        }

        return $log;
    }

    /**
     * @param DocumentRepository|GridFSRepository|ViewRepository $logRepository
     * @param int $priority
     *
     * @return Log|null
     */
    protected function logFindOneBy($logRepository, int $priority): ?Log
    {
        return $logRepository->findOneBy(['priority' => $priority]);
    }
}
