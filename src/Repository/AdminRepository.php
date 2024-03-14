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

namespace App\Repository;

use App\Entity\Admin;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Class AdminRepository
 * @package App\Repository
 *
 * @method Admin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Admin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Admin[]    findAll()
 * @method Admin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminRepository extends BaseRepository implements PasswordUpgraderInterface, AdminRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admin::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time
     * @param PasswordAuthenticatedUserInterface $user
     * @param string $newHashedPassword
     *
     * @return void
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (! $user instanceof Admin) {
            // @codeCoverageIgnoreStart
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
            // @codeCoverageIgnoreEnd
        }
        $user->setPassword($newHashedPassword);
        $this->save($user, true);
    }

    /**
     * @param string|null $value
     *
     * @return Admin|null
     * @throws NonUniqueResultException
     */
    public function findOneByLogin(?string $value): ?Admin
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
