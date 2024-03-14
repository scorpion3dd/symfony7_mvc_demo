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

namespace App\Tests\Unit\Entity;

use App\Entity\Comment;
use App\Entity\User;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class CommentTest - Unit tests for Entity Comment
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Entity
 */
class CommentTest extends BaseKernelTestCase
{
    public const FULL_FILE_NAME = '/../data/Service/CommentService/london1.jpg';

    /** @var Comment $comment */
    public Comment $comment;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $user = $this->createUser();
        $user->setId(1);
        $this->comment = $this->createComment($user);
    }

    /**
     * @testCase - function __toString - must be a success
     *
     * @return void
     */
    public function testToString(): void
    {
        $commentString = (string)$this->comment;
        $expected = (string) $this->comment->getAuthor() . ' (' . $this->comment->getEmail() . '): ' . $this->comment->getText();
        $this->assertEquals($expected, $commentString);
    }

    /**
     * @testCase - function getUserIri - must be a success
     *
     * @return void
     */
    public function testGetUserIri(): void
    {
        $expected = '/api/users/1';
        $this->assertEquals($expected, $this->comment->getUserIri());
    }

    /**
     * @testCase - function getUser - must be a success
     *
     * @return void
     */
    public function testGetUser(): void
    {
        $this->assertInstanceOf(User::class, $this->comment->getUser());
    }

    /**
     * @testCase - function getStateChoices - must be a success
     *
     * @return void
     */
    public function testGetStateChoices(): void
    {
        $expected = [
            Comment::STATE_SUBMITTED => Comment::STATE_SUBMITTED,
            Comment::STATE_HAM => Comment::STATE_HAM,
            Comment::STATE_POTENTIAL_SPAM => Comment::STATE_POTENTIAL_SPAM,
            Comment::STATE_SPAM => Comment::STATE_SPAM,
            Comment::STATE_REJECTED => Comment::STATE_REJECTED,
            Comment::STATE_READY => Comment::STATE_READY,
            Comment::STATE_PUBLISHED => Comment::STATE_PUBLISHED,
        ];
        $this->assertEquals($expected, $this->comment->getStateChoices());
    }

    /**
     * @testCase - function getPhotoFile - must be a success
     *
     * @return void
     */
    public function testGetPhotoFile(): void
    {
        $photoFile = new File(__DIR__ . self::FULL_FILE_NAME);
        $this->comment->setPhotoFile($photoFile);
        $this->assertEquals($photoFile, $this->comment->getPhotoFile());
    }
}
