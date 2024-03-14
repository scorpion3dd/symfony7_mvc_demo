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

namespace App\Service;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\RolePermission;
use App\Entity\User;
use App\Entity\UserRole;
use App\Factory\RolePermissionFactory;
use App\Factory\UserRoleFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class RolePermissionService
 * @package App\Service
 */
class RolePermissionService extends BaseService implements RolePermissionServiceInterface
{
    /**
     * @param RolePermissionFactory $rolePermFactory
     * @param UserRoleFactory $userRoleFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly RolePermissionFactory $rolePermFactory,
        private readonly UserRoleFactory $userRoleFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
        $this->debugConstruct(self::class);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param User $user
     *
     * @return EntityManagerInterface
     */
    public function persistRolePermissionByUser(EntityManagerInterface $entityManager, User $user): EntityManagerInterface
    {
        $rolePermissions = $user->getRolePermissions();
        /** @var RolePermission $rolePermission */
        foreach ($rolePermissions as $rolePermission) {
            $userRoleOld = $entityManager->getRepository(UserRole::class)->findOneBy([
                'userId' => $user->getId(),
                'rolePermissionId' => $rolePermission->getId() ?? 0,
            ]);
            if (empty($userRoleOld)) {
                /** @var RolePermission|null $rp */
                $rp = $entityManager->getRepository(RolePermission::class)->findOneBy([
                    'id' => $rolePermission->getId() ?? 0,
                ]);
                if (! empty($rp)) {
                    $userRole = $this->userRoleFactory->create($rp, $user);
                    $entityManager->persist($userRole);
                }
            }
        }

        return $entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Permission $permission
     * @param string $method
     *
     * @return EntityManagerInterface
     */
    public function persistRolePermissionByPermission(
        EntityManagerInterface $entityManager,
        Permission $permission,
        string $method = 'create'
    ): EntityManagerInterface {
        $collection = $permission->getRoles();
        $newRoles = $collection->getValues();
        if ($method == 'update') {
            $oldRolePermissions = $entityManager->getRepository(RolePermission::class)->findBy([
                'permission' => $permission->getId(),
            ]);
            /** @var RolePermission $oldRP */
            foreach ($oldRolePermissions as $oldRP) {
                $condition = false;
                /** @var Role $newRole */
                foreach ($newRoles as $newRole) {
                    $role = $oldRP->getRole();
                    $roleId = $role ? $role->getId() : 0;
                    if ($roleId == $newRole->getId()) {
                        $condition = true;
                        break;
                    }
                }
                if (! $condition) {
                    $entityManager->remove($oldRP);
                }
            }
        }
        $entityManager->flush();
        /** @var Role $role */
        foreach ($newRoles as $role) {
            $rolePermissionOld = null;
            if ($method == 'update') {
                /** @phpstan-ignore-next-line */
                $rolePermissionOld = $entityManager->getRepository(RolePermission::class)->findOneBy([
                    'role' => $role->getId(),
                    'permission' => $permission->getId(),
                ]);
            }
            if (empty($rolePermissionOld)) {
                $rolePermission = $this->rolePermFactory->create($role, $permission);
                $entityManager->persist($rolePermission);
            }
        }

        return $entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Role $role
     * @param string $method
     *
     * @return EntityManagerInterface
     */
    public function persistRolePermissionByRole(
        EntityManagerInterface $entityManager,
        Role $role,
        string $method = 'create'
    ): EntityManagerInterface {
        $collection = $role->getPermissions();
        $newPermissiones = $collection->getValues();
        if ($method == 'update') {
            $oldRolePermissions = $entityManager->getRepository(RolePermission::class)->findBy([
                'role' => $role->getId()
            ]);
            /** @var RolePermission $oldRP */
            foreach ($oldRolePermissions as $oldRP) {
                $condition = false;
                /** @var Permission $newPermission */
                foreach ($newPermissiones as $newPermission) {
                    $permission = $oldRP->getPermission();
                    $permissionId = $permission ? $permission->getId() : 0;
                    if ($permissionId == $newPermission->getId()) {
                        $condition = true;
                        break;
                    }
                }
                if (! $condition) {
                    $entityManager->remove($oldRP);
                }
            }
        }
        $entityManager->flush();
        /** @var Permission $permission */
        foreach ($newPermissiones as $permission) {
            $rolePermissionOld = null;
            if ($method == 'update') {
                /** @phpstan-ignore-next-line */
                $rolePermissionOld = $entityManager->getRepository(RolePermission::class)->findOneBy([
                    'role' => $role->getId(),
                    'permission' => $permission->getId(),
                ]);
            }
            if (empty($rolePermissionOld)) {
                $rolePermission = $this->rolePermFactory->create($role, $permission);
                $entityManager->persist($rolePermission);
            }
        }

        return $entityManager;
    }
}
