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

namespace App\Tests\Integration\Admin;

use App\Entity\Admin;
use App\Factory\LogFactory;
use App\Tests\BaseCrudController;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Base class BaseCrudControllerIntegration - for all integration tests
 * in Admin CrudControllers by EasyAdminBundle with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @link https://symfony.com/doc/current/testing.html#types-of-tests
 *
 * @package App\Tests\Integration\Admin
 * @property EntityManager $entityManager
 * @property DocumentManager $documentManager
 */
class BaseCrudControllerIntegration extends BaseCrudController
{
    /** @var DocumentManager $documentManager */
    protected DocumentManager $documentManager;

    /** @var LogFactory $logFactory */
    protected LogFactory $logFactory;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->logFactory = $this->container->get(LogFactory::class);
        $this->documentManager = $this->container->get(DocumentManager::class);
    }

    /**
     * @return void
     * @throws NotSupported
     */
    protected function auth(): void
    {
        $adminRepository = $this->entityManager->getRepository(Admin::class);
        $this->admin = $adminRepository->findOneBy(['username' => self::AUTH_USERNAME]);
    }
}
