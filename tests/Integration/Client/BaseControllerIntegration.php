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

namespace App\Tests\Integration\Client;

use App\Entity\Comment;
use App\Entity\User;
use App\Helper\ApplicationGlobals;
use App\Tests\TestTrait;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Base class BaseControllerIntegration - for all integration tests
 * in Client Controllers
 * with connecting to external services, such as databases, message brokers, etc.
 *
 * @link https://symfony.com/doc/current/testing.html#types-of-tests
 *
 * @package App\Tests\Integration\Client
 */
class BaseControllerIntegration extends WebTestCase
{
    use TestTrait;

    /** @var User $user */
    protected User $user;

    /** @var Comment|null $comment */
    protected ?Comment $comment;

    /** @var Generator $faker */
    protected readonly Generator $faker;

    /** @var string $appDomain */
    protected string $appDomain;

    /** @var ApplicationGlobals $appGlobals */
    protected ApplicationGlobals $appGlobals;

    /** @var KernelBrowser $client */
    protected KernelBrowser $client;

    /** @var ContainerInterface $container */
    protected ContainerInterface $container;

    /** @var EntityManagerInterface $entityManager */
    protected EntityManagerInterface $entityManager;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->container = static::getContainer();
        $this->entityManager = $this->container->get(EntityManagerInterface::class);
        $this->appGlobals = $this->container->get(ApplicationGlobals::class);
        $this->appGlobals->setType(ApplicationGlobals::TYPE_APP_TESTS);
        $this->faker = \Faker\Factory::create();
        $this->appDomain = $this->container->getParameter('app.domain');
    }

    /**
     * @return void
     */
    protected function user(): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $this->user = $userRepository->findOneBy(['status' => 1]);
    }

    /**
     * @param string $state
     *
     * @return void
     */
    protected function comment(string $state = 'rejected'): void
    {
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $this->comment = $commentRepository->findOneBy(['state' => $state]);
    }
}
