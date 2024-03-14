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

namespace App\Tests\Integration\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminControllerIntegrationTest - for all integration tests
 * in AdminController with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 109 - Business process - Admin
 * @link https://www.atlassian.com/software/confluence/bp/109
 *
 * @package App\Tests\Integration\Client
 */
class AdminControllerIntegrationTest extends BaseControllerIntegration
{
    /**
     * @testCase 1036 - Integration test reviewComment action for AdminController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1036
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2036 - For AdminController reviewComment action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2036
     * @bp 109 - Business process - Admin
     * @link https://www.atlassian.com/software/confluence/bp/109
     *     Arrange:
     * without AUTH
     *     Act:
     * GET /admin/comment/review/1
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains string
     *
     * @dataProvider provideReviewComment
     *
     * @param string $expected
     * @param string $state
     *
     * @return void
     */
    public function testReviewComment(string $expected, string $state): void
    {
        $this->comment($state);
        $id = (! empty($this->comment)) ? (string)$this->comment->getId() : 1;
        $crawler = $this->client->request(Request::METHOD_GET, self::ROUTE_URL_COMMENT_REVIEW . $id);
        $html = $crawler->html();
        $this->assertStringContainsString($expected, $html);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @return iterable
     */
    public static function provideReviewComment(): iterable
    {
        $version = '1';
        $expected = 'Comment already reviewed or not in the right state.';
        $state = 'rejected';
        yield $version => [$expected, $state];

        $version = '2';
        $expected = 'Comment reviewed, thank you!';
        $state = 'ham';
        yield $version => [$expected, $state];

        $version = '3';
        $expected = 'Comment reviewed, thank you!';
        $state = 'potential_spam';
        yield $version => [$expected, $state];
    }

    /**
     * @testCase 1037 - Integration test purgeHttpCache action for AdminController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1037
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2037 - For AdminController purgeHttpCache action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2037
     * @bp 109 - Business process - Admin
     * @link https://www.atlassian.com/software/confluence/bp/109
     *     Arrange:
     * without AUTH
     *     Act:
     * PURGE /admin/http-cache/users
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains string
     *
     * @dataProvider providePurgeHttpCache
     *
     * @param string $expectedString
     * @param int $expectedStatusCode
     *
     * @return void
     */
    public function testPurgeHttpCache(string $expectedString, int $expectedStatusCode): void
    {
        $crawler = $this->client->request(Request::METHOD_PURGE, self::ROUTE_URL_PURGE_HTTP_CACHE);
        $html = $crawler->html();
        $this->assertStringContainsString($expectedString, $html);
        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }

    /**
     * @return iterable
     */
    public static function providePurgeHttpCache(): iterable
    {
        $version = '1';
        $expectedString = 'Done';
        $expectedStatusCode = Response::HTTP_OK;
        yield $version => [$expectedString, $expectedStatusCode];
    }
}
