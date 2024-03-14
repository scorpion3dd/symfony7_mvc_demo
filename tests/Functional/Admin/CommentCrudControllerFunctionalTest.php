<?php
/**
 * This file is part of the Simple Web Demo Free Admin Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Symfony Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2023-2024 scorpion3dd
 */

declare(strict_types=1);

namespace App\Tests\Functional\Admin;

use App\Controller\Admin\CommentCrudController;
use App\Entity\Comment;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class CommentCrudControllerFunctionalTest - for all functional tests
 * in CommentCrudController with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 104 - Business process - Comment
 * @link https://www.atlassian.com/software/confluence/bp/104
 *
 * @package App\Tests\Functional\Admin
 */
class CommentCrudControllerFunctionalTest extends BaseCrudControllerFunctional
{
    public const TEXT_USER_COMMENTS = 'User Comments';
    public const TEXT_EDIT_USER_COMMENT = 'Edit User Comment';

    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return CommentCrudController::class;
    }

    /**
     * @return string
     */
    protected static function getEntityName(): string
    {
        return Comment::class;
    }

    /**
     * @testCase 1040 - Functional test actions for CommentCrudController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1040
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2040 - For CommentCrudController actions without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2040
     * @bp 104 - Business process - Comment
     * @link https://www.atlassian.com/software/confluence/bp/104
     *     Arrange:
     * without AUTH
     *
     * @return void
     * @throws Exception
     */
    public function testAdminCommentCrud(): void
    {
        $this->debugFunction(self::class, 'testAdminCommentCrud');

        $this->act1LoginGet();
        $this->act2LoginPost();
        $this->act3AdminGet();
        $this->actIndexGet('4', self::TEXT_USER_COMMENTS, 'CommentCrudController');
        $this->act5DetailGet(Comment::class, 'CommentCrudController');
        $this->act6EditGet(self::TEXT_EDIT_USER_COMMENT, 'CommentCrudController');
        $this->act7EditPost(Comment::class, 'CommentCrudController');
        $this->act8DetailGet($this->entity->getAuthor(), 'CommentCrudController');
        $this->actIndexGet('9', self::TEXT_USER_COMMENTS, 'CommentCrudController');

        $this->actIndexGet('11', self::TEXT_USER_COMMENTS, 'CommentCrudController');
        $this->act12DeleteGet('CommentCrudController');
        $this->act13DeleteDelete('UserCrudController');
        $this->actIndexGet('14', self::TEXT_USER_COMMENTS, 'CommentCrudController');
        $this->act15LogoutGet();
        $this->act16GetEntityFqcn(
            CommentCrudController::class,
            'UserCrudController',
            Comment::class
        );
    }

    /**
     * @return string
     */
    protected function getTitle(): string
    {
        return $this->entity->getAuthor();
    }

    /**
     * @param array $param
     *
     * @return void
     */
    protected function assertByDbEdit(array $param): void
    {
        if ($this->assertByDb) {
            $this->assertEquals($param['state'], $this->comment->getState());
        }
    }

    /**
     * @return void
     */
    protected function assertByDbDelete(): void
    {
        if ($this->assertByDb) {
            $this->assertNull($this->comment);
        }
    }

    /**
     * @param Crawler $crawler
     *
     * @return array
     * @throws Exception
     */
    protected function getFormEdit(Crawler $crawler): array
    {
        $formEdit = $crawler->selectButton('Save changes')->form();
        $param = [];
        $param['state'] = Comment::randomStateComment();
        $formEdit['Comment[state]']->setValue($param['state']);

        return [$formEdit, $param];
    }
}
