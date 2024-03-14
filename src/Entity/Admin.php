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
use App\Controller\Api\GetMeController;
use App\Controller\Api\LogoutController;
use App\Enum\Roles;
use App\Repository\AdminRepository;
use App\State\AdminDeleteProcessor;
use App\State\AdminGetCollectionProvider;
use App\State\AdminGetProvider;
use App\State\AdminLoginProcessor;
use App\State\AdminRefreshTokenProcessor;
use App\State\UserPasswordHasherProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use stdClass;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Admin
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ApiResource(
    description: 'An Admin API Resource.',
    paginationEnabled: true,
    paginationItemsPerPage: 5,
    normalizationContext: ['groups' => ['admin:read']],
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            description: 'Route for information about Admins by Provider',
            name: 'admins_list1',
            uriTemplate: '/admins/list1',
            provider: AdminGetCollectionProvider::class
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            description: 'Route for information about Admins by EventSubscriber postWrite',
            name: 'admins_list2',
            uriTemplate: '/admins/list2'
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            description: 'Route for information about Admins by FilterQueryCollectionExtension applyToCollection'
        ),
        new Get(
            security: "is_granted('ROLE_ADMIN')",
            description: 'Route for information about Admin by Provider',
            provider: AdminGetProvider::class
        ),
        new Get(
            security: "is_granted('ROLE_ADMIN')",
            description: 'Route for information about Admin by EventSubscriber postWrite',
            name: 'admins_item',
            uriTemplate: '/admins/{id}/item',
        ),
        new Get(
            security: "is_granted('ROLE_ADMIN')",
            description: 'Route for information about me by Controller',
            name: 'users_me',
            uriTemplate: '/me',
            controller: GetMeController::class,
            openapi: new Operation(
                summary: 'Information about me by Controller',
                responses: [
                    '200' => [
                        'description' => 'Information about me',
                        'content' => [
                            'application/ld+json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'ulid' => ['type' => 'string'],
                                        'identifier' => ['type' => 'string'],
                                        'roles' => ['type' => 'array'],
                                    ]
                                ],
                                'example' => [
                                    'ulid' => '01HGDMB5QTTAP8PDX1YRM6CXCX',
                                    'identifier' => 'test_003',
                                    'roles' => ['ROLE_ADMIN'],
                                ]
                            ]
                        ]
                    ],
                    '404' => [
                        'description' => 'Not found information about me'
                    ]
                ]
            )
        ),
        new Get(
            security: "is_granted('ROLE_ADMIN')",
            description: 'Route for logout me by Controller',
            name: 'api_logout',
            uriTemplate: '/logout',
            controller: LogoutController::class,
            openapi: new Operation(
                summary: 'Logout me by Controller',
                responses: [
                    '200' => [
                        'description' => 'Logout me'
                    ],
                    '404' => [
                        'description' => 'Not found me'
                    ]
                ]
            )
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            processor: UserPasswordHasherProcessor::class,
            uriTemplate: '/registration',
            description: 'Route for registration',
            denormalizationContext: ['groups' => ['admin:registration']]
        ),
        new Post(
            processor: AdminLoginProcessor::class,
            uriTemplate: '/login',
            description: 'Route for login',
            denormalizationContext: ['groups' => ['admin:login']]
        ),
        new Post(
            processor: AdminRefreshTokenProcessor::class,
            uriTemplate: '/token/refresh',
            description: 'Route for refresh token',
            denormalizationContext: ['groups' => ['admin:refresh']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            processor: UserPasswordHasherProcessor::class,
            denormalizationContext: ['groups' => ['admin:password']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            processor: AdminDeleteProcessor::class
        )
    ]
)]
class Admin implements UserInterface, EntityInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(Types::INTEGER)]
    private ?int $id = null;

    #[Groups(['admin:read', 'admin:registration', 'admin:login'])]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Type(Types::STRING)]
    #[Assert\Length(min: 2, max: 180)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\Type(Types::STRING)]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    #[Groups(['admin:read'])]
    #[Assert\Type(Types::STRING)]
    #[Assert\Length(min: 2, max: 500)]
    private ?string $token = null;

    #[Groups(['admin:read', 'admin:refresh'])]
    #[Assert\Type(Types::STRING)]
    private ?string $refreshToken = null;

    #[Groups(['admin:registration', 'admin:login', 'admin:password'])]
    #[SerializedName('password')]
    #[Assert\Type(Types::STRING)]
    private ?string $plainPassword = null;

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
        // guarantee every user at least has ROLE_ADMIN
        $roles[] = Roles::ROLE_ADMIN;

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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

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
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return stdClass
     */
    public function getProxyForTokens(): stdClass
    {
        $obj = new stdClass();
        $obj->id = $this->id;
        $obj->custom = 'custom data';

        return $obj;
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @param string|null $refreshToken
     */
    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}
