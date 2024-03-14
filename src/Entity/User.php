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
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\Api\LotteryController;
use App\Enum\Roles;
use App\Repository\UserRepository;
use App\Helper\UlidHelper;
use App\State\UserGetCollectionProvider;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['uid', 'username', 'email'])]
#[ORM\UniqueConstraint(name: 'uid_UNIQUE', columns: ['uid'])]
#[ORM\UniqueConstraint(name: 'username_UNIQUE', columns: ['username'])]
#[ORM\UniqueConstraint(name: 'email_UNIQUE', columns: ['email'])]
#[ApiResource(
    description: 'User API Resource.',
    paginationEnabled: true,
    paginationItemsPerPage: 5,
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:create']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(
            paginationEnabled: true,
            paginationItemsPerPage: 5,
            description: 'Route for users in lottery by FilterQueryCollectionExtension applyToCollection',
            uriTemplate: '/users/lottery'
        ),
        new GetCollection(
            paginationEnabled: true,
            paginationItemsPerPage: 5,
            description: 'Route for users in lottery by Provider',
            uriTemplate: '/users/lottery1',
            provider: UserGetCollectionProvider::class
        ),
        new GetCollection(
            paginationEnabled: true,
            paginationItemsPerPage: 5,
            name: 'users_lottery',
            description: 'Route for users in lottery by Controller',
            uriTemplate: '/users/lottery2',
            controller: LotteryController::class,
            openapi: new Operation(
                summary: 'Users in lottery by Controller',
                responses: [
                    '200' => [
                        'description' => 'Users in lottery',
                        'content' => [
                            'application/ld+json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'uid' => ['type' => 'string'],
                                        'username' => ['type' => 'string'],
                                        'email' => ['type' => 'string'],
                                        'fullName' => ['type' => 'string'],
                                        'description' => ['type' => 'string'],
                                        'status' => ['type' => 'integer'],
                                        'access' => ['type' => 'integer'],
                                        'gender' => ['type' => 'integer'],
                                        'dateBirthday' => ['type' => 'datetime'],
                                        'rolePermissions' => ['type' => 'object'],
                                        'createdAt' => ['type' => 'datetime'],
                                    ]
                                ],
                                'example' => [
                                    'uid' => '01HGDMB5QTTAP8PDX1YRM6CXCX',
                                    'username' => 'test_003',
                                    'email' => 'test_003@lottery.com',
                                    'fullName' => 'Test 003',
                                    'description' => 'Test by API',
                                    'status' => 1,
                                    'access' => 1,
                                    'gender' => 1,
                                    'dateBirthday' => '2023-11-16T14:41:05+01:00',
                                    'rolePermissions' => [],
                                    'createdAt' => '2023-11-16T14:41:05+01:00',
                                ]
                            ]
                        ]
                    ],
                    '404' => [
                        'description' => 'Not found any users in lottery'
                    ]
                ]
            )
        ),
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
class User implements UserInterface, EntityInterface
{
    public const STATUS_ACTIVE_ID = 1;
    public const STATUS_ACTIVE = 'Active';
    public const STATUS_DISACTIVE_ID = 2;
    public const STATUS_DISACTIVE = 'Disactive';

    public const GENDER_MALE_ID = 1;
    public const GENDER_MALE = 'Male';
    public const GENDER_FEMALE_ID = 2;
    public const GENDER_FEMALE = 'Female';

    public const ACCESS_YES_ID = 1;
    public const ACCESS_YES = 'Yes';
    public const ACCESS_NO_ID = 2;
    public const ACCESS_NO = 'No';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(Types::INTEGER)]
    private ?int $id = null;

    #[Groups(['user:read', 'user:create', 'user:passwordRead'])]
    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Assert\Type(Types::STRING)]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $uid = null;

    #[Groups(['user:read', 'user:create', 'user:passwordRead'])]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Type(Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    private ?string $username = null;

    #[Groups(['user:read', 'user:create', 'user:passwordRead'])]
    #[ORM\Column(type: 'string', length: 128, unique: true, nullable: false)]
    #[Assert\Type(Types::STRING)]
    #[Assert\Email]
    #[Assert\Length(min: 2, max: 128)]
    private ?string $email = null;

    #[Groups(['user:read', 'user:create', 'user:passwordRead'])]
    #[ORM\Column(type: 'string', length: 256, unique: false, nullable: false)]
    #[Assert\Type(Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 256)]
    private ?string $fullName = null;

    #[Groups(['user:read', 'user:create'])]
    #[ORM\Column(type: 'string', length: 1024, unique: false, nullable: true)]
    #[Assert\Type(Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 1024)]
    private ?string $description = null;

    #[Groups(['user:read', 'user:create'])]
    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Type(Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\Range(min: self::STATUS_ACTIVE_ID, max: self::STATUS_DISACTIVE_ID)]
    private int|null $status = null;

    #[Groups(['user:read', 'user:create'])]
    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Type(Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\Range(min: self::ACCESS_YES_ID, max: self::ACCESS_NO_ID)]
    private int|null $access = null;

    #[Groups(['user:read', 'user:create'])]
    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Type(Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\Range(min: self::GENDER_MALE_ID, max: self::GENDER_FEMALE_ID)]
    private int|null $gender = null;

    #[Groups(['user:read', 'user:create'])]
    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    private ?DateTime $dateBirthday = null;

    #[ORM\Column]
    private array $roles = [];

    private int|null $rolesId = 0;

    /**
     * https://www.doctrine-project.org/projects/doctrine-orm/en/3.1/reference/association-mapping.html#many-to-many-bidirectional
     */
    #[Groups(['user:read', 'user:create'])]
    #[ORM\ManyToMany(targetEntity: RolePermission::class, inversedBy: "users")]
    #[ORM\JoinTable(name: "user_role")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "role_permission_id", referencedColumnName: "id")]
    /** @var iterable $rolePermissions */
    private iterable $rolePermissions;

    #[Groups(['user:read', 'user:create'])]
    #[ORM\Column]
    #[Assert\Type(Types::DATETIME_MUTABLE)]
    private ?DateTime $createdAt = null;

    #[Groups(['user:read', 'user:create'])]
    #[ORM\Column]
    #[Assert\Type(Types::DATETIME_MUTABLE)]
    private ?DateTime $updatedAt = null;

    /*
     * orphanRemoval: true - if deleted user then need delete all comments to this user
     */
    #[Groups(['user:list', 'user:item'])]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[Groups(['user:list', 'user:item'])]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\Type(Types::STRING)]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->uid = UlidHelper::generate();
        $this->comments = new ArrayCollection();
        $this->rolePermissions = new ArrayCollection();
    }

    /**
     * @param SluggerInterface $slugger
     *
     * @return void
     */
    public function computeSlug(SluggerInterface $slugger): void
    {
        if (! $this->slug || '-' === $this->slug) {
            $this->slug = (string) $slugger->slug((string) $this)->lower();
        }
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function buildSlug(): string
    {
        return strtoupper($this->username . '-' . $this->uid);
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return array
     */
    public function getCommentsArray(): array
    {
        $comments = [];
        /** @var Comment $comment */
        foreach ($this->comments as $comment) {
            $commentArr = [];
            $commentArr['author'] = $comment->getAuthor();
            $commentArr['text'] = $comment->getText();
            $commentArr['email'] = $comment->getEmail();
            $comments[] = $commentArr;
        }

        return $comments;
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @param Comment $comment
     *
     * @return $this
     */
    public function addComment(Comment $comment): self
    {
        if (! $this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    /**
     * @param Comment $comment
     *
     * @return $this
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->setUser(null) == $this) {
                // @codeCoverageIgnoreStart
                $comment->setUser(null);
                // @codeCoverageIgnoreEnd
            }
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $username = $this->username;

        return $username ?? '';
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = Roles::ROLE_USER;

        return array_unique($roles);
    }

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**

    /**
     * @see UserInterface
     *
     * @return void
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
//         $this->plainPassword = null;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string|null $fullName
     *
     * @return $this
     */
    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

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
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     *
     * @return $this
     */
    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAccess(): ?int
    {
        return $this->access;
    }

    /**
     * @param int|null $access
     *
     * @return $this
     */
    public function setAccess(?int $access): self
    {
        $this->access = $access;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDateBirthday(): ?DateTime
    {
        return $this->dateBirthday;
    }

    /**
     * @param DateTime|null $dateBirthday
     *
     * @return $this
     */
    public function setDateBirthday(?DateTime $dateBirthday): self
    {
        $this->dateBirthday = $dateBirthday;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGender(): ?int
    {
        return $this->gender;
    }

    /**
     * @param int|null $gender
     *
     * @return $this
     */
    public function setGender(?int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|null $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->uid;
    }

    /**
     * @param string|null $uid
     *
     * @return $this
     */
    public function setUid(?string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @return string[]
     */
    public static function getGenderList(): array
    {
        return [
            self::GENDER_MALE_ID => self::GENDER_MALE,
            self::GENDER_FEMALE_ID => self::GENDER_FEMALE
        ];
    }

    /**
     * @return int[]
     */
    public static function getGenderChoices(): array
    {
        return [
            self::GENDER_MALE => self::GENDER_MALE_ID,
            self::GENDER_FEMALE => self::GENDER_FEMALE_ID
        ];
    }

    /**
     * @return int[]
     */
    public static function getAccessChoices(): array
    {
        return [
            self::ACCESS_YES => self::ACCESS_YES_ID,
            self::ACCESS_NO => self::ACCESS_NO_ID
        ];
    }

    /**
     * @return int[]
     */
    public static function getStatusChoices(): array
    {
        return [
            self::STATUS_ACTIVE => self::STATUS_ACTIVE_ID,
            self::STATUS_DISACTIVE => self::STATUS_DISACTIVE_ID
        ];
    }

    /**
     * @return int
     * @throws Exception
     */
    public static function randomGenderId(): int
    {
        return random_int(self::GENDER_MALE_ID, self::GENDER_FEMALE_ID);
    }

    /**
     * @return string
     */
    public function getGenderAsString(): string
    {
        $list = self::getGenderList();
        if (isset($list[$this->gender])) {
            return $list[$this->gender];
        }

        return 'Unknown';
    }

    /**
     * @return int
     * @throws Exception
     */
    public static function randomStatusId(): int
    {
        return random_int(self::STATUS_ACTIVE_ID, self::STATUS_DISACTIVE_ID);
    }

    /**
     * @return int
     * @throws Exception
     */
    public static function randomAccessId(): int
    {
        return random_int(self::ACCESS_YES_ID, self::ACCESS_NO_ID);
    }

    /**
     * @return string
     */
    public function getRolesAsString(): string
    {
        $roleList = '';
        $count = count($this->roles);
        $i = 0;
        foreach ($this->roles as $role) {
            if ($role instanceof Role) {
                $roleList .= $role->getName();
            }
            if (is_string($role)) {
                if ($role === 'ROLE_USER') {
                    $role = 'Guest';
                }
                $roleList .= $role;
            }
            if ($i < $count - 1) {
                $roleList .= ', ';
            }
            $i++;
        }

        return $roleList;
    }

    /**
     * @return array
     */
    public function getRolesChoices(): array
    {
        $roleList = [];
        if (empty($this->roles)) {
            $this->roles[] = 'ROLE_USER';
        }
        foreach ($this->roles as $key => $role) {
            if ($role instanceof Role) {
                $roleList[] = $role->getName();
            }
            if (is_string($role)) {
                if ($role === 'ROLE_USER') {
                    $role = 'Guest';
                }
                $roleList[$role] = $key;
            }
        }

        return $roleList;
    }

    /**
     * @return array
     */
    public static function getRolesChoicesStatic(): array
    {
        return (new User)->getRolesChoices();
    }

    /**
     * @return string
     */
    public function getRolePermissionsAsString(): string
    {
        $roleList = '';
        /** @var Collection $rolePermissions */
        $rolePermissions = $this->getRolePermissions();
        $count = $rolePermissions->count();
        $i = 0;
        foreach ($this->rolePermissions as $rolePermission) {
            if ($rolePermission instanceof RolePermission) {
                $role = $rolePermission->getRole();
                $roleName = $role ? $role->getName() : '';

                $permission = $rolePermission->getPermission();
                $permissionName = $permission ? $permission->getName() : '';

                $roleList .= $roleName . ' / ' . $permissionName;
            }
            if ($i < $count - 1) {
                $roleList .= ', ';
            }
            $i++;
        }

        return $roleList;
    }

    /**
     * @return int|null
     */
    public function getRolesId(): ?int
    {
        if (empty($this->rolesId)) {
            $this->rolesId = 0;
        }

        return $this->rolesId;
    }

    /**
     * @param int|null $rolesId
     *
     * @return $this
     */
    public function setRolesId(?int $rolesId): self
    {
        $this->rolesId = $rolesId;

        return $this;
    }

    /**
     * @return array
     */
    public function getRolePermissionsArray(): array
    {
        $rolePermissions = [];
        /** @var RolePermission $rolePermission */
        foreach ($this->rolePermissions as $rolePermission) {
            $rolePermissions[] = $rolePermission->getId();
        }

        return $rolePermissions;
    }

    /**
     * @return array
     */
    public function getRolePermissionsArrayIri(): array
    {
        $rolePermissions = [];
        /** @var RolePermission $rolePermission */
        foreach ($this->rolePermissions as $rolePermission) {
            $iri = [];
            $iri['@id'] = '/api/role_permissions/' . $rolePermission->getId();
            $rolePermissions[] = $iri;
        }

        return $rolePermissions;
    }

    /**
     * @return ArrayCollection|iterable
     */
    public function getRolePermissions(): ArrayCollection|iterable
    {
        return $this->rolePermissions;
    }

    /**
     * @param ArrayCollection|iterable $rolePermissions
     *
     * @return $this
     */
    public function setRolePermissions(ArrayCollection|iterable $rolePermissions): self
    {
        $this->rolePermissions = $rolePermissions;

        return $this;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}
