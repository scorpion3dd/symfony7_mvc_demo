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

namespace App\Tests\Functional\Client;

use App\Entity\Comment;
use App\Entity\EntityInterface;
use App\Entity\User;
use App\Tests\BaseCrudController;
use Doctrine\ORM\Exception\NotSupported;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * Base class BaseControllerFunctional - for all functional (application) tests
 * in Client Controllers with connecting to external services, such as databases, message brokers, etc.
 *
 * @link https://symfony.com/doc/current/testing.html#types-of-tests
 *
 * @package App\Tests\Functional\Client
 */
class BaseControllerFunctional extends BaseCrudController
{
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
    protected static function getEntityName(): string
    {
        return EntityInterface::class;
    }

    /**
     * @return void
     * @throws NotSupported
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
     * @throws NotSupported
     */
    protected function comment(string $state = 'rejected'): void
    {
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $this->comment = $commentRepository->findOneBy(['state' => $state]);
    }
}
