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

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\CommentUploadController;
use App\Repository\CommentRepository;
use ArrayObject;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use ApiPlatform\OpenApi\Model;

/**
 * Class Comment
 * @package App\Entity
 */
#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    description: 'Comment API Resource.',
    paginationEnabled: true,
    paginationItemsPerPage: 10,
    operations: [
        new Get(normalizationContext: ['groups' => 'comment:item']),
        new GetCollection(
            paginationEnabled: true,
            paginationItemsPerPage: 5,
            description: 'Route for comments by user in lottery by FilterQueryCollectionExtension applyToCollection',
            uriTemplate: '/comments?userId=userId'
        ),
        new GetCollection(normalizationContext: ['groups' => 'comment:list']),
        new Post(
            normalizationContext: ['groups' => ['comment:upload', 'comment:create']],
            denormalizationContext: ['groups' => ['comment:create', 'comment:uploadInput']],
            description: 'Route for Create comment (input parameters in POST format with upload photo file) by user',
            deserialize: false,
            inputFormats: ['multipart' => ['multipart/form-data']],
            uriTemplate: '/comments/upload',
            controller: CommentUploadController::class,
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'photoFile' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ],
                                    'author' => [
                                        'type' => 'string',
                                        'format' => 'string'
                                    ],
                                    'text' => [
                                        'type' => 'string',
                                        'format' => 'string'
                                    ],
                                    'email' => [
                                        'type' => 'string',
                                        'format' => 'email'
                                    ],
                                    'user' => [
                                        'type' => 'string',
                                        'format' => 'iri of user'
                                    ]
                                ]
                            ]
                        ]
                    ])
                )
            )
        ),
        new Post(
            normalizationContext: ['groups' => 'comment:item'],
            denormalizationContext: ['groups' => ['comment:create']],
            description: 'Route for Create comment (input parameters in JSON format without upload photo file) by user',
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => 'comment:item'],
            denormalizationContext: ['groups' => ['comment:update']]
        )
    ],
    order: ['createdAt' => 'DESC']
)]
#[ApiFilter(SearchFilter::class, properties: ['user' => 'exact'])]
class Comment implements EntityInterface
{
    public const STATE_SUBMITTED = 'submitted';
    public const STATE_HAM = 'ham';
    public const STATE_POTENTIAL_SPAM = 'potential_spam';
    public const STATE_SPAM = 'spam';
    public const STATE_REJECTED = 'rejected';
    public const STATE_READY = 'ready';
    public const STATE_PUBLISHED = 'published';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['comment:list', 'comment:item'])]
    #[Assert\Type(Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: false)]
    #[Assert\Type(Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['comment:list', 'comment:item', 'comment:create'])]
    private ?string $author = null;

    #[ORM\Column(type: Types::TEXT, unique: false)]
    #[Assert\Type(Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 1024)]
    #[Groups(['comment:list', 'comment:item', 'comment:create'])]
    private ?string $text = null;

    #[ORM\Column(type: 'string', length: 255, unique: false)]
    #[Assert\Type(Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['comment:list', 'comment:item', 'comment:create'])]
    private ?string $email = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['comment:list', 'comment:item'])]
    #[Assert\Type(Types::DATETIME_MUTABLE)]
    private ?DateTime $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:list', 'comment:item', 'comment:create'])]
    private ?User $user = null;

    #[Groups(['comment:uploadInput'])]
    #[Vich\UploadableField(mapping: 'comments', fileNameProperty: 'photoFilename')]
    public ?File $photoFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['comment:list', 'comment:item', 'comment:upload'])]
    #[Assert\Type(Types::STRING)]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $photoFilename = null;

    #[ORM\Column(type: 'string', length: 255, options: ['default' => 'submitted'])]
    #[Groups(['comment:list', 'comment:item', 'comment:update'])]
    #[Assert\Type(Types::STRING)]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $state = 'submitted';

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getAuthor() . ' (' . $this->getEmail() . '): ' . $this->getText();
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
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * @param string $author
     *
     * @return $this
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

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
     * @param DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return void
     */
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @return string
     */
    public function getUserIri(): string
    {
        $userId = (! empty($this->user)) ? $this->user->getId() : 0;

        return '/api/users/' . $userId;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     *
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhotoFilename(): ?string
    {
        return $this->photoFilename;
    }

    /**
     * @param string|null $photoFilename
     *
     * @return $this
     */
    public function setPhotoFilename(?string $photoFilename): self
    {
        $this->photoFilename = $photoFilename;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return $this
     */
    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string[]
     */
    public static function getStateList(): array
    {
        return [
            self::STATE_SUBMITTED,
            self::STATE_HAM,
            self::STATE_POTENTIAL_SPAM,
            self::STATE_SPAM,
            self::STATE_REJECTED,
            self::STATE_READY,
            self::STATE_PUBLISHED,
        ];
    }

    /**
     * @return string[]
     */
    public static function getStateChoices(): array
    {
        return [
            self::STATE_SUBMITTED => self::STATE_SUBMITTED,
            self::STATE_HAM => self::STATE_HAM,
            self::STATE_POTENTIAL_SPAM => self::STATE_POTENTIAL_SPAM,
            self::STATE_SPAM => self::STATE_SPAM,
            self::STATE_REJECTED => self::STATE_REJECTED,
            self::STATE_READY => self::STATE_READY,
            self::STATE_PUBLISHED => self::STATE_PUBLISHED,
        ];
    }

    /**
     * @param bool $empty
     *
     * @return string
     * @throws Exception
     */
    public static function randomStateComment(bool $empty = true): string
    {
        $statesComment = self::getStateList();
        if ($empty) {
            $statesComment[] = '';
        }
        /** @phpstan-ignore-next-line */
        return $statesComment[random_int(0, count($statesComment) - 1)];
    }

    /**
     * @return File|null
     */
    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    /**
     * @param File|null $photoFile
     */
    public function setPhotoFile(?File $photoFile = null): void
    {
        $this->photoFile = $photoFile;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}
