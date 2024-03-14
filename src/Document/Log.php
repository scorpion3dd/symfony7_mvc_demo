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

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\LogRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Exception;
use MongoDB\BSON\ObjectID;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Monolog\Logger;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Log
 * @package App\Document
 */
#[ODM\Document(collection: "logs", repositoryClass: LogRepository::class)]
#[ApiResource(
    description: 'Log API Resource.',
    paginationEnabled: true,
    paginationItemsPerPage: 5,
    normalizationContext: ['groups' => ['log:read']],
    denormalizationContext: ['groups' => ['log:create']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
class Log implements DocumentInterface
{
    #[Groups(['log:read', 'log:create'])]
    #[ODM\Id]
    /** @var mixed|null $id */
    protected mixed $id = null;

    #[Groups(['log:read', 'log:create'])]
    #[ODM\Field(type: "collection")]
    /** @var array $extra */
    protected array $extra = [];

    #[Groups(['log:read', 'log:create'])]
    #[ODM\Field(type: "string")]
    #[Assert\Type(Types::STRING)]
    #[Assert\NotBlank]
    /** @var string $message */
    protected string $message = '';

    #[Groups(['log:read', 'log:create'])]
    #[ODM\Field(type: "int")]
    #[Assert\Type(Types::INTEGER)]
    #[Assert\NotBlank]
    /** @var int $priority */
    private int $priority = 1;

    #[Groups(['log:read', 'log:create'])]
    #[ODM\Field(type: "string")]
    #[Assert\Type(Types::STRING)]
    #[Assert\NotBlank]
    /** @var string $priorityName */
    protected string $priorityName = '';

    #[Groups(['log:read', 'log:create'])]
    #[ODM\Field(type: "date")]
    #[Assert\Type(Types::DATETIME_MUTABLE)]
    /** @var DateTime|null $timestamp */
    protected ?DateTime $timestamp = null;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        /** @psalm-suppress InvalidClass **/
        return (string) new ObjectID($this->id);
    }

    /**
     * @param mixed $id
     *
     * @return $this
     */
    public function setId(mixed $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @psalm-suppress DeprecatedConstant
     *
     * @return string[]
     */
    public static function getPriorities(): array
    {
        return [
            Logger::EMERGENCY  => 'EMERGENCY',
            Logger::ALERT  => 'ALERT',
            Logger::CRITICAL   => 'CRITICAL',
            Logger::ERROR    => 'ERROR',
            Logger::WARNING   => 'WARNING',
            Logger::NOTICE => 'NOTICE',
            Logger::INFO   => 'INFO',
            Logger::DEBUG  => 'DEBUG',
        ];
    }

    /**
     * @return int
     * @throws Exception
     */
    public static function getPriorityRandom(): int
    {
        return (int)array_rand(self::getPriorities());
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return string
     */
    public function getPriorityName(): string
    {
        return $this->priorityName;
    }

    /**
     * @param string $priorityName
     *
     * @return $this
     */
    public function setPriorityName(string $priorityName): self
    {
        $this->priorityName = $priorityName;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTimestamp(): ?DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime|null $timestamp
     *
     * @return $this
     */
    public function setTimestamp(?DateTime $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @return string
     */
    public function getExtraString(): string
    {
        $extra = '';
        if (! empty($this->extra)) {
            $extra = implode(';', $this->extra);
        }

        return $extra;
    }

    /**
     * @param array $extra
     *
     * @return $this
     */
    public function setExtra(array $extra): self
    {
        $this->extra = $extra;

        return $this;
    }
}
