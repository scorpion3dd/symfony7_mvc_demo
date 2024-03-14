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
 * Class RoleHierarchy
 * @package App\Entity
 */
#[ORM\Entity]
#[UniqueEntity(fields: ['parentRoleId', 'childRoleId'])]
#[ORM\UniqueConstraint(name: 'parentRoleId_childRoleId_UNIQUE', columns: ['parentRoleId', 'childRoleId'])]
#[ORM\Table(name: "role_hierarchy")]
class RoleHierarchy implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\Type(Types::INTEGER)]
    #[Assert\NotBlank]
    private int|null $parentRoleId = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\Type(Types::INTEGER)]
    #[Assert\NotBlank]
    private int|null $childRoleId = null;

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
    public function getParentRoleId(): ?int
    {
        return $this->parentRoleId;
    }

    /**
     * @param int|null $parentRoleId
     *
     * @return $this
     */
    public function setParentRoleId(?int $parentRoleId): self
    {
        $this->parentRoleId = $parentRoleId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getChildRoleId(): ?int
    {
        return $this->childRoleId;
    }

    /**
     * @param int|null $childRoleId
     *
     * @return $this
     */
    public function setChildRoleId(?int $childRoleId): self
    {
        $this->childRoleId = $childRoleId;

        return $this;
    }
}
