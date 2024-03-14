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

use DateTime;
use DateTimeInterface;
use Doctrine\Persistence\ManagerRegistry;
use Gesdinet\JWTRefreshTokenBundle\Doctrine\RefreshTokenRepositoryInterface;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;

/**
 * Class RefreshTokenRepository
 * @package App\Repository
 *
 * @method RefreshToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefreshToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefreshToken[]    findAll()
 * @method RefreshToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefreshTokenRepository extends BaseRepository implements RefreshTokenRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    /**
     * @param DateTimeInterface|null $datetime
     *
     * @return RefreshToken[]
     */
    public function findInvalid($datetime = null)
    {
        if (null === $datetime) {
            $datetime = new DateTime();
            $datetime = $datetime->setTime(0, 0, 0);
        }

        return $this->createQueryBuilder('u')
            ->where('u.valid < :datetime')
            ->setParameter(':datetime', $datetime)
            ->getQuery()
            ->getResult();
    }
}
