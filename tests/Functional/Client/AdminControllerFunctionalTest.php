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

namespace App\Tests\Functional\Client;

use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminControllerFunctionalTest - for all functional tests
 * in AdminController with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 109 - Business process - Admin
 * @link https://www.atlassian.com/software/confluence/bp/109
 *
 * @package App\Tests\Functional\Client
 */
class AdminControllerFunctionalTest extends BaseControllerFunctional
{
    /**
     * @testCase 1038 - Functional test actions for AdminController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1038
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2038 - For AdminController actions without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2038
     * @bp 109 - Business process - Admin
     * @link https://www.atlassian.com/software/confluence/bp/109
     *     Arrange:
     * without AUTH
     *
     * @return void
     * @throws Exception
     */
    public function testAdmin(): void
    {
        $this->debugFunction(self::class, 'testAdmin');

        $this->debugFunction(self::class, 'Act 1: GET /admin/comment/review/1 - state = rejected');
        $state = 'rejected';
        $this->comment($state);
        $id = (! empty($this->comment)) ? (string)$this->comment->getId() : 1;
        $this->client->request(Request::METHOD_GET, self::ROUTE_URL_COMMENT_REVIEW . $id);
        $this->assertSelectorTextContains('body', 'Comment already reviewed or not in the right state.');
        static::assertResponseIsSuccessful();

        $this->debugFunction(self::class, 'Act 2: GET /admin/comment/review/1 - state = ham');
        $state = 'ham';
        $this->comment($state);
        $id = (! empty($this->comment)) ? (string)$this->comment->getId() : 1;
        $this->client->request(Request::METHOD_GET, self::ROUTE_URL_COMMENT_REVIEW . $id);
        $this->assertSelectorTextContains('h2', 'Comment reviewed, thank you!');
        static::assertResponseIsSuccessful();

        $this->debugFunction(self::class, 'Act 3: GET /admin/comment/review/1 - state = potential_spam');
        $state = 'potential_spam';
        $this->comment($state);
        $id = (! empty($this->comment)) ? (string)$this->comment->getId() : 1;
        $this->client->request(Request::METHOD_GET, self::ROUTE_URL_COMMENT_REVIEW . $id);
        $this->assertSelectorTextContains('h2', 'Comment reviewed, thank you!');
        static::assertResponseIsSuccessful();

        $this->debugFunction(self::class, 'Act 4: GET /admin/http-cache/users');
        $crawler = $this->client->request(Request::METHOD_PURGE, self::ROUTE_URL_PURGE_HTTP_CACHE);
        $html = $crawler->html();
        $this->assertStringContainsString('Done', $html);
        static::assertResponseIsSuccessful();
    }
}
