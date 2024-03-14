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

namespace App\Factory;

use App\Entity\Comment;
use App\Entity\User;
use DateTime;

/**
 * Class CommentFactory - is the Factory Method design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/FactoryMethod/README.html
 * @package App\Factory
 */
class CommentFactory
{
    public function __construct()
    {
    }

    /**
     * @param User $user
     * @param string $author
     * @param string $email
     * @param string $text
     * @param string $state
     *
     * @return Comment
     */
    public function create(
        User $user,
        string $author = '',
        string $email = '',
        string $text = '',
        string $state = '',
    ): Comment {
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setAuthor($author);
        $comment->setEmail($email);
        $comment->setText($text);
        $comment->setCreatedAt(new DateTime());
        if ($state != '') {
            $comment->setState($state);
        }

        return $comment;
    }
}
