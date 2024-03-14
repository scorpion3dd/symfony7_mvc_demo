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

use App\Entity\Role;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class RoleRepository - This is the custom repository class for Role entity
 * @package App\Repository
 *
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public const DEFAULT_ROLES = [
        'Administrator' => [
            'description' => 'A person who manages users, roles, etc.',
            'parent' => null,
            'permissions' => [
                'user.manage',
                'role.manage',
                'permission.manage',
                'profile.any.view',
            ],
        ],
        'Guest' => [
            'description' => 'A person who can log in and view own profile.',
            'parent' => null,
            'permissions' => [
                'profile.own.view',
            ],
        ],
    ];

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * @return array
     */
    public function getAllDefaultRoles(): array
    {
        return self::DEFAULT_ROLES;
    }
}
