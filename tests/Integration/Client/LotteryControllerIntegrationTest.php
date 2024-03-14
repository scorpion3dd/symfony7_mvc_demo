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

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LotteryControllerIntegrationTest - for all integration tests
 * in LotteryController with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 107 - Business process - Lottery
 * @link https://www.atlassian.com/software/confluence/bp/107
 *
 * @package App\Tests\Integration\Client
 */
class LotteryControllerIntegrationTest extends BaseControllerIntegration
{
    public const FULL_FILE_NAME = '/../data/Client/LotteryController/london1.jpg';

    /**
     * @testCase 1026 - Integration test indexNoLocale action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1026
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2026 - For LotteryController indexNoLocale action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2026
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     * without AUTH
     *     Act:
     * GET /
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws Exception
     */
    public function testIndexNoLocale(): void
    {
        $this->client->request(Request::METHOD_GET, self::ROUTE_URL_INDEX_NO_LOCALE);
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1027 - Integration test about action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1027
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2027 - For LotteryController about action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2027
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     * without AUTH
     *     Act:
     * GET /en/about
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains string
     *
     * @return void
     * @throws Exception
     */
    public function testAbout(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, self::ROUTE_URL_ABOUT);
        $html = $crawler->html();
        $this->assertStringContainsString(
            'About - This is the Simple Web Demo Free Lottery Management Application',
            $html
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @testCase 1028 - Integration test language action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1028
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2028 - For LotteryController language action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2028
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     * without AUTH
     *     Act:
     * GET /en/language
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @dataProvider provideLanguage
     *
     * @param string $version
     * @param array $server
     *
     * @return void
     * @throws Exception
     */
    public function testLanguage(string $version, array $server): void
    {
        if ($version == '1') {
            $this->client->request(Request::METHOD_GET, self::ROUTE_URL_LANGUAGE);
        } elseif ($version == '2') {
            $this->client->request(Request::METHOD_GET, self::ROUTE_URL_LANGUAGE, [], [], $server);
        }
        static::assertResponseRedirects();
    }

    /**
     * @return iterable
     */
    public static function provideLanguage(): iterable
    {
        $version = '1';
        $server = [];
        yield $version => [$version, $server];

        $version = '2';
        $server = [
            'HTTP_REFERER' => '/en/about'
        ];
        yield $version => [$version, $server];
    }

    /**
     * @testCase 1029 - Integration test index action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1029
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2029 - For LotteryController index action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2029
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     * without AUTH
     *     Act:
     * GET /en/
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains string
     *
     * @return void
     * @throws Exception
     */
    public function testIndex(): void
    {
        $page = 2;
        $crawler = $this->client->request(Request::METHOD_GET, self::ROUTE_URL_INDEX, ['page' => $page]);
        $html = $crawler->html();
        $this->assertStringContainsString('Welcome!', $html);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @testCase 1030 - Integration test show action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1030
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2030 - For LotteryController show action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2030
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     * without AUTH
     * from DB get User, then from User get Slug (FAY.MAGNOLIA13-01HM2K3Z2VY8B55H98Q2D0RTRS)
     *     Act:
     * GET /en/lottery/FAY.MAGNOLIA13-01HM2K3Z2VY8B55H98Q2D0RTRS
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains string
     *
     * @return void
     * @throws Exception
     */
    public function testShowGet(): void
    {
        $this->user();
        $crawler = $this->client->request(Request::METHOD_GET, self::ROUTE_URL_SHOW . $this->user->getSlug());
        $html = $crawler->html();
        $this->assertStringContainsString('User:', $html);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @testCase 1030 - Integration test show action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1030
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2030 - For LotteryController show action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2030
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     * without AUTH
     * from DB get User, then from User get Slug (FAY.MAGNOLIA13-01HM2K3Z2VY8B55H98Q2D0RTRS)
     *     Act:
     * POST /en/lottery/FAY.MAGNOLIA13-01HM2K3Z2VY8B55H98Q2D0RTRS
     *   Form errors:
     * The CSRF token is invalid. Please try to resubmit the form.
     *     Assert:
     * StatusCode = 422 - HTTP_UNPROCESSABLE_ENTITY
     * Response content contains string
     *
     * @return void
     * @throws Exception
     */
    public function testShowPostFormErrors(): void
    {
        $this->user();
        $uri = self::ROUTE_URL_SHOW . $this->user->getSlug();
        $values = $this->getPostParametersComment();
        $crawler = $this->client->request(Request::METHOD_POST, $uri, $values);
        $html = $crawler->html();
        $this->assertStringContainsString('User:', $html);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @testCase 1030 - Integration test show action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1030
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2030 - For LotteryController show action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2030
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     * without AUTH
     * from DB get User, then from User get Slug (FAY.MAGNOLIA13-01HM2K3Z2VY8B55H98Q2D0RTRS)
     *     Act:
     * GET /en/lottery/FAY.MAGNOLIA13-01HM2K3Z2VY8B55H98Q2D0RTRS
     * get CSRF _token from form comment_form
     * POST /en/lottery/FAY.MAGNOLIA13-01HM2K3Z2VY8B55H98Q2D0RTRS
     * CrudPostParameters - Comment
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws Exception
     */
    public function testShowPost(): void
    {
        $this->user();
        $uri = self::ROUTE_URL_SHOW . $this->user->getSlug();

        $crawlerGet = $this->client->request(Request::METHOD_GET, $uri);
        $html = $crawlerGet->html();
        $this->assertStringContainsString('User:', $html);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawlerGet->selectButton('Submit')->form();
        $values = $form->getPhpValues();
        $token = $form['comment_form[_token]']->getValue();

        /** some new data */
        $newData = $this->getPostParametersComment($token);
        $newData['comment_form']['text'] .= ' - some new message';
        $values = array_merge($values, $newData);

        $this->client->request(Request::METHOD_POST, $uri, $values);
        static::assertResponseRedirects();
    }

    /**
     * @testCase 1030 - Integration test show action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1030
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2030 - For LotteryController show action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2030
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     * without AUTH
     * from DB get User, then from User get Slug (FAY.MAGNOLIA13-01HM2K3Z2VY8B55H98Q2D0RTRS)
     *     Act:
     * GET /en/lottery/FAY.MAGNOLIA13-01HM2K3Z2VY8B55H98Q2D0RTRS
     * get CSRF _token from form comment_form
     * POST /en/lottery/FAY.MAGNOLIA13-01HM2K3Z2VY8B55H98Q2D0RTRS
     * CrudPostParameters - Comment to form comment_form
     * PostFilesCommentUploadedFile - photo to form comment_form
     *     Assert:
     * StatusCode = 302 - Response redirects
     *
     * @return void
     * @throws Exception
     */
    public function testShowPostUploadedFile(): void
    {
        $this->user();
        $uri = self::ROUTE_URL_SHOW . $this->user->getSlug();

        $crawlerGet = $this->client->request(Request::METHOD_GET, $uri);
        $html = $crawlerGet->html();
        $this->assertStringContainsString('User:', $html);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawlerGet->selectButton('Submit')->form();
        $values = $form->getPhpValues();
        $token = $form['comment_form[_token]']->getValue();

        /** some new data */
        $newData = $this->getPostParametersComment($token);
        $newData['comment_form']['text'] .= ' - some new message';
        $values = array_merge($values, $newData);
        $files = $this->getPostFilesCommentUploadedFile(__DIR__ . self::FULL_FILE_NAME);
        $server = $this->getServerUploadedFile();

        $this->client->request(Request::METHOD_POST, $uri, $values, $files, $server);
        static::assertResponseRedirects();
    }
}
