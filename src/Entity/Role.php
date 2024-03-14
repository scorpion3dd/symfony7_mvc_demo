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
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\RoleRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Role
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[UniqueEntity(fields: ['name'])]
#[ORM\UniqueConstraint(name: 'name_UNIQUE', columns: ['name'])]
#[ApiResource(
    description: 'Role API Resource.',
    paginationEnabled: true,
    paginationItemsPerPage: 10,
    normalizationContext: ['groups' => ['role:read']],
    denormalizationContext: ['groups' => ['role:create']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
class Role implements EntityInterface
{
    public const REDIS_ROLE = 'role:';
    public const REDIS_ROLE_TEST = 'role:test:';
    public const REDIS_SETS_ROLES = 'roles';
    public const REDIS_SETS_ROLES_TEST = 'roles:test';
    public const REDIS_ROLE_SET = 'role:set';
    public const REDIS_ROLE_SET_TEST = 'role:test:set';
    public const REDIS_SETS_ROLES_TTL = 600;
    public const REDIS_SETS_ROLES_TTL_TEST = 600;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(Types::INTEGER)]
    private ?int $id = null;

    #[Groups(['user:read', 'role:read', 'role:create'])]
    #[ORM\Column(type: 'string', length: 128, unique: true)]
    #[Assert\Type(Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 128)]
    private ?string $name = null;

    #[Groups(['user:read', 'role:read', 'role:create'])]
    #[ORM\Column(type: 'string', length: 1024, unique: false)]
    #[Assert\Type(Types::STRING)]
    #[Assert\Length(min: 2, max: 1024)]
    private ?string $description = null;

    #[Groups(['role:read', 'role:create'])]
    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(Types::DATETIME_MUTABLE)]
    private ?DateTime $dateCreated = null;

    /**
     * https://www.doctrine-project.org/projects/doctrine-orm/en/3.1/reference/association-mapping.html#many-to-many-self-referencing
     */
    #[Groups(['role:read', 'role:create'])]
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'childRoles')]
    #[ORM\JoinTable(name: "role_hierarchy")]
    #[ORM\JoinColumn(name: "child_role_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "parent_role_id", referencedColumnName: "id")]
    private Collection $parentRoles;

    /**
     * https://www.doctrine-project.org/projects/doctrine-orm/en/3.1/reference/association-mapping.html#many-to-many-self-referencing
     */
    #[Groups(['role:read', 'role:create'])]
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'parentRoles')]
//    #[ORM\JoinTable(name: "role_hierarchy")]
//    #[ORM\JoinColumn(name: "parent_role_id", referencedColumnName: "id")]
//    #[ORM\InverseJoinColumn(name: "child_role_id", referencedColumnName: "id")]
    protected Collection $childRoles;

    /**
     * https://www.doctrine-project.org/projects/doctrine-orm/en/3.1/reference/association-mapping.html#many-to-many-bidirectional
     */
    #[Groups(['role:read', 'role:create'])]
    #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: 'roles')]
    #[ORM\JoinTable(name: "role_permission")]
    #[ORM\JoinColumn(name: "role_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "permission_id", referencedColumnName: "id")]
    private Collection $permissions;

    public function __construct()
    {
        $this->parentRoles = new ArrayCollection();
        $this->childRoles = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $name = $this->name;

        return $name ?? '';
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?? '';
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param DateTime $dateCreated
     *
     * @return $this
     */
    public function setDateCreated(DateTime $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getParentRoles(): Collection
    {
        return $this->parentRoles;
    }

    /**
     * @return Collection
     */
    public function getChildRoles(): Collection
    {
        return $this->childRoles;
    }

    /**
     * @return array
     */
    public function getPermissionsArray(): array
    {
        $permissions = [];
        /** @var Permission $permission */
        foreach ($this->permissions as $permission) {
            $permissions[] = $permission->getId();
        }

        return $permissions;
    }

    /**
     * @return array
     */
    public function getPermissionsArrayIri(): array
    {
        $permissions = [];
        /** @var RolePermission $permission */
        foreach ($this->permissions as $permission) {
            $permissions[] = '/api/permissions/' . $permission->getId();
        }

        return $permissions;
    }

    /**
     * @return Collection
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @param Permission $permission
     *
     * @return $this
     */
    public function addPermission(Permission $permission): self
    {
        if (! $this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
            $permission->addRole($this);
        }

        return $this;
    }

    /**
     * @param Permission $permission
     *
     * @return $this
     */
    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->removeElement($permission)) {
            $permission->removeRole($this);
        }

        return $this;
    }

    /**
     * @param Permission $permission
     *
     * @return void
     */
    public function setPermissions(Permission $permission): void
    {
        $this->permissions->add($permission);
    }

    /**
     * @param Role $role
     *
     * @return bool
     */
    public function addParent(Role $role): bool
    {
        if ($this->getId() == $role->getId()) {
            return false;
        }
        if (! $this->hasParent($role)) {
            // @codeCoverageIgnoreStart
            $this->parentRoles->add($role);
            $role->getChildRoles()->add($this);

            return true;
            // @codeCoverageIgnoreEnd
        }

        return false;
    }

    /**
     * @return void
     */
    public function clearParentRoles(): void
    {
        $this->parentRoles = new ArrayCollection();
    }

    /**
     * @param Role $role
     *
     * @return void
     */
    public function setParentRole(Role $role): void
    {
        $this->parentRoles->add($role);
    }

    /**
     * @param Role $role
     *
     * @return bool
     */
    public function hasParent(Role $role): bool
    {
        return $this->getParentRoles()->contains($role);
    }

    /**
     * @param Role $role
     *
     * @return bool
     */
    public function addChild(Role $role): bool
    {
        if ($this->getId() == $role->getId()) {
            return false;
        }
        if (! $this->hasChild($role)) {
            // @codeCoverageIgnoreStart
            $this->childRoles->add($role);
            $role->getParentRoles()->add($this);

            return true;
            // @codeCoverageIgnoreEnd
        }

        return false;
    }

    /**
     * @param Role $role
     *
     * @return bool
     */
    public function hasChild(Role $role): bool
    {
        return $this->getChildRoles()->contains($role);
    }

    /**
     * @param Role $role
     *
     * @return void
     */
    public function setChildRole(Role $role): void
    {
        $this->childRoles->add($role);
    }
}
