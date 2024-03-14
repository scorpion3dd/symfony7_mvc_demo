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

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\RolePermission;
use App\Entity\UserRole;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class RolePermissionRepository
 * @package App\Repository
 *
 * @method RolePermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method RolePermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method RolePermission[]    findAll()
 * @method RolePermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RolePermissionRepository extends BaseRepository implements RolePermissionRepositoryInterface
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RolePermission::class);
    }

    /**
     * @param int $userId
     *
     * @return Query
     */
    public function findRolePermissionsBy(int $userId): Query
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select(
            'rp.id',
            'r.name as nameRole',
            'p.name as namePermission',
        )
            ->from(RolePermission::class, 'rp')
            ->join(UserRole::class, 'ur', 'WITH', 'rp.id = ur.rolePermissionId')
            ->leftJoin(Role::class, 'r', 'WITH', 'rp.role = r.id')
            ->leftJoin(Permission::class, 'p', 'WITH', 'rp.permission = p.id')
            ->where("ur.userId = :userId")
            ->setParameter('userId', $userId)
            ->orderBy('r.name, p.name');

        return $queryBuilder->getQuery();
    }

    /**
     * @return Query
     */
    public function findRolePermissions(): Query
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select(
            'rp.id',
            'r.name as nameRole',
            'p.name as namePermission',
        )
            ->from(RolePermission::class, 'rp')
            ->leftJoin(Role::class, 'r', 'WITH', 'rp.role = r.id')
            ->leftJoin(Permission::class, 'p', 'WITH', 'rp.permission = p.id')
            ->orderBy('r.name, p.name');

        return $queryBuilder->getQuery();
    }
}
