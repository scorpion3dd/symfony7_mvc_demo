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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserRole
 * @package App\Entity
 */
#[ORM\Entity]
#[UniqueEntity(fields: ['userId', 'rolePermissionId'])]
#[ORM\UniqueConstraint(name: 'userId_rolePermissionId_UNIQUE', columns: ['userId', 'rolePermissionId'])]
#[ORM\Table(name: "user_role")]
class UserRole implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\Type(Types::INTEGER)]
    #[Assert\NotBlank]
    private int|null $userId = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\Type(Types::INTEGER)]
    #[Assert\NotBlank]
    private int|null $rolePermissionId = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\Type(Types::INTEGER)]
    private int|null $adminArchivedId = null;

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
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRolePermissionId(): ?int
    {
        return $this->rolePermissionId;
    }

    /**
     * @param int $rolePermissionId
     *
     * @return $this
     */
    public function setRolePermissionId(int $rolePermissionId): self
    {
        $this->rolePermissionId = $rolePermissionId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAdminArchivedId(): ?int
    {
        return $this->adminArchivedId;
    }

    /**
     * @param int|null $adminArchivedId
     *
     * @return $this
     */
    public function setAdminArchivedId(?int $adminArchivedId): self
    {
        $this->adminArchivedId = $adminArchivedId;

        return $this;
    }
}
