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
use App\Repository\PermissionRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Permission
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[UniqueEntity(fields: ['name'])]
#[ORM\UniqueConstraint(name: 'name_UNIQUE', columns: ['name'])]
#[ApiResource(
    description: 'Permission API Resource.',
    paginationEnabled: true,
    paginationItemsPerPage: 10,
    normalizationContext: ['groups' => ['permission:read']],
    denormalizationContext: ['groups' => ['permission:create']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
class Permission implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(Types::INTEGER)]
    private ?int $id = null;

    #[Groups(['user:read', 'permission:read', 'permission:create'])]
    #[ORM\Column(type: 'string', length: 128, unique: true)]
    #[Assert\Type(Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 128)]
    private ?string $name = null;

    #[Groups(['user:read', 'permission:read', 'permission:create'])]
    #[ORM\Column(type: 'string', length: 1024, unique: false)]
    #[Assert\Type(Types::STRING)]
    #[Assert\Length(min: 2, max: 1024)]
    private ?string $description = null;

    #[Groups(['permission:read', 'permission:create'])]
    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(Types::DATETIME_MUTABLE)]
    private ?DateTime $dateCreated = null;

    /**
     * https://www.doctrine-project.org/projects/doctrine-orm/en/3.1/reference/association-mapping.html#many-to-many-bidirectional
     */
    #[Groups(['permission:read', 'permission:create'])]
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id ?? 0;
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
     * @return array
     */
    public function getRolesArray(): array
    {
        $roles = [];
        /** @var Permission $role */
        foreach ($this->roles as $role) {
            $roles[] = $role->getId();
        }

        return $roles;
    }

    /**
     * @return array
     */
    public function getRolesArrayIri(): array
    {
        $roles = [];
        /** @var RolePermission $role */
        foreach ($this->roles as $role) {
            $roles[] = '/api/roles/' . $role->getId();
        }

        return $roles;
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @param Role $role
     *
     * @return $this
     */
    public function addRole(Role $role): self
    {
        if (! $this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->addPermission($this);
        }

        return $this;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function removeRole(Role $role): self
    {
        if ($this->roles->removeElement($role)) {
            $role->removePermission($this);
        }

        return $this;
    }
}
