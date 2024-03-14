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

namespace App\Tests\UnitAdminForm\Controller;

use App\Tests\BaseCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Factory\FormFactory;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorageFactory;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Base class BaseCrudControllerForm - for all unit tests
 * in AdminForm CrudControllers by EasyAdminBundle
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @link https://symfony.com/doc/current/testing.html#types-of-tests
 *
 * @package App\Tests\UnitAdminForm\Controller
 */
class BaseCrudControllerForm extends BaseCrudController
{
    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->prepareDbMySqlMock();
    }

    /**
     * @param bool $isSubmitted
     * @param bool $isValid
     * @param string $method
     * @param string $entityName
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function formBuilderMock(bool $isSubmitted, bool $isValid, string $method, string $entityName): void
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
        if ($method == 'createNewFormBuilder') {
            $entity = null;
            if ($entityName == 'Role') {
                $entity = $this->role;
            } elseif ($entityName == 'Permission') {
                $entity = $this->permission;
            } elseif ($entityName == 'User') {
                $entity = $this->user;
            } elseif ($entityName == 'Comment') {
                $entity = $this->comment;
            }
            $formMock->expects($this->exactly(1))
                ->method('getData')
                ->willReturn($entity);
        }
        $formBuilderMock = $this->getMockForAbstractClass(FormBuilderInterface::class);
        $formBuilderMock->expects($this->exactly(1))
            ->method('getForm')
            ->willReturn($formMock);

        $formFactoryMock = $this->getMockBuilder(FormFactory::class)
            ->onlyMethods(['createEditFormBuilder', 'createEditForm', 'createNewFormBuilder', 'createNewForm', 'createFiltersForm'])
            ->disableOriginalConstructor()
            ->getMock();
        $formFactoryMock->expects($this->exactly(1))
            ->method($method)
            ->willReturn($formBuilderMock);
        $this->container->set(FormFactory::class, $formFactoryMock);
    }

    /**
     * @param KernelBrowser $client
     *
     * @return void
     */
    public function prepareCookie(KernelBrowser $client): void
    {
        $cookie = new Cookie(self::MOCK_SESSID, self::SESSION_ID);
        $client->getCookieJar()->set($cookie);
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return void
     * @throws Exception
     */
    public function prepareSession(string $key, mixed $value): void
    {
        /** @var MockFileSessionStorageFactory $sessionStorageFactory */
        $sessionStorageFactory = $this->getContainer()->get('session.storage.factory.mock_file');
        /** @var MockFileSessionStorage $sessionStorage */
        $sessionStorage = $sessionStorageFactory->createStorage(null);
        $sessionStorage->setId(self::SESSION_ID);
        $sessionStorage->start();
        $sessionStorage->setSessionData([
            '_sf2_attributes' => [$key => $value],
        ]);
        $sessionStorage->save();
    }

    /**
     * @param KernelBrowser $client
     *
     * @return SessionInterface
     * @throws Exception
     */
    public function provideSession(KernelBrowser $client): SessionInterface
    {
        $container = $client->getContainer();
        foreach ($client->getCookieJar()->all() as $cookie) {
            // MOCKSESSID / PHPSESSID
            if (str_ends_with(strtoupper($cookie->getName()), 'SESSID')) {
                $sessionStorage = $container->get('session.storage.factory')->createStorage(null);
                $sessionStorage->setId($cookie->getValue());
                $session = new Session($sessionStorage);
                $session->start();

                return $session;
            }
        }
        $session = match (true) {
            $container->has('session.factory') => $container->get('session.factory')->createSession(),
            $container->has('session') => $container->get('session'),
            default => throw new Exception('Cannot initialize session! Please, login first, check if session is available, or ensure, that session factory is available!'),
        };
        $session->start();
        $cookie = new Cookie($session->getName(), $session->getId(), null, null, 'localhost');
        $client->getCookieJar()->set($cookie);

        return $session;
    }

    /**
     * @param string $tokenId
     *
     * @return string
     * @throws Exception
     */
    protected function generateCsrfToken(string $tokenId = ''): string
    {
        return (string) $this->getContainer()->get(CsrfTokenManagerInterface::class)->getToken($tokenId);
    }
}
