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

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\RolePermissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RolePermission
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: RolePermissionRepository::class)]
#[UniqueEntity(fields: ['permission', 'role'])]
#[ORM\UniqueConstraint(name: 'permission_role_UNIQUE', columns: ['permission', 'role'])]
#[ORM\Table(name: "role_permission")]
#[ApiResource(
    openapi: false,
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:create']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
class RolePermission implements EntityInterface
{
    #[Groups(['user:read', 'user:create'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(Types::INTEGER)]
    private ?int $id = null;

    #[Groups(['user:read', 'user:create'])]
    #[ORM\OneToOne(targetEntity: Permission::class)]
    #[ORM\JoinColumn(name: "permission_id", referencedColumnName: "id")]
    private Permission|null $permission = null;

    #[Groups(['user:read', 'user:create'])]
    #[ORM\OneToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: "role_id", referencedColumnName: "id")]
    private Role|null $role = null;

    /**
     * https://www.doctrine-project.org/projects/doctrine-orm/en/3.1/reference/association-mapping.html#many-to-many-bidirectional
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: "rolePermissions")]
    private iterable $users;

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (! empty($this->role) && ! empty($this->permission)) {
            return $this->role->getName() . ' / ' . $this->permission->getName();
        } else {
            return ' / ';
        }
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Permission|null
     */
    public function getPermission(): ?Permission
    {
        return $this->permission;
    }

    /**
     * @param Permission $permission
     *
     * @return $this
     */
    public function setPermission(Permission $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * @return Role|null
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function setRole(Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return iterable
     */
    public function getUsers(): iterable
    {
        return $this->users;
    }

    /**
     * @param iterable $users
     */
    public function setUsers(iterable $users): void
    {
        $this->users = $users;
    }
}
