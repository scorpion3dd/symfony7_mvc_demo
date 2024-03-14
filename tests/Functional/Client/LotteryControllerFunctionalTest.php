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

namespace App\Tests\Functional\Client;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LotteryControllerFunctionalTest - for all functional tests
 * in LotteryController with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 107 - Business process - Lottery
 * @link https://www.atlassian.com/software/confluence/bp/107
 *
 * @package App\Tests\Functional\Client
 */
class LotteryControllerFunctionalTest extends BaseControllerFunctional
{
    public const FULL_FILE_NAME = '/../data/Client/LotteryController/london1.jpg';

    /**
     * @testCase 1031 - Functional test actions for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1030
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2031 - For LotteryController actions without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2031
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     * without AUTH
     *
     * @return void
     * @throws Exception
     */
    public function testLottery(): void
    {
        $this->debugFunction(self::class, 'testLottery');

        $this->debugFunction(self::class, 'Act 1: GET');
        $this->client->request(Request::METHOD_GET, self::ROUTE_URL_INDEX_NO_LOCALE);
        static::assertResponseRedirects();

        $this->debugFunction(self::class, 'Act 2: GET /en/about');
        $this->client->request(Request::METHOD_GET, self::ROUTE_URL_ABOUT_EN);
        $this->assertPageTitleContains('About - This is the Simple Web Demo Free Lottery Management Application.');
        $this->assertSelectorTextContains('h2', 'Application name: Simple Web Demo Free Lottery Management Application');
        $this->assertResponseIsSuccessful();

        $this->debugFunction(self::class, 'Act 3: GET /fr/language');
        $this->client->request(Request::METHOD_GET, self::ROUTE_URL_LANGUAGE_FR);
        static::assertResponseRedirects();

        $this->debugFunction(self::class, 'Act 4: GET /fr/language with HTTP_REFERER is empty');
        $server = [
            'HTTP_REFERER' => ''
        ];
        $this->client->request(Request::METHOD_GET, self::ROUTE_URL_LANGUAGE_FR, [], [], $server);
        static::assertResponseRedirects();

        $this->debugFunction(self::class, 'Act 5: GET /fr/about');
        $this->client->request(Request::METHOD_GET, self::ROUTE_URL_ABOUT_FR);
        $this->assertPageTitleContains("À propos - Ceci est l'application de gestion de loterie gratuite Simple Web Demo");
        $this->assertSelectorTextContains('h2', "Nom de l'application: Démo Web simple Application de gestion de loterie gratuite");
        $this->assertResponseIsSuccessful();

        $this->debugFunction(self::class, 'Act 6: GET /en/');
        $this->client->request(Request::METHOD_GET, self::ROUTE_URL_INDEX_EN);
        $this->assertSelectorTextContains('h2', 'Welcome!');
        $this->assertResponseIsSuccessful();

        $this->debugFunction(self::class, 'Act 7: GET /en/ page=2');
        $page = 2;
        $crawler = $this->client->request(Request::METHOD_GET, self::ROUTE_URL_INDEX_EN, ['page' => $page]);
        $this->assertSelectorTextContains('h2', 'Welcome!');
        $this->assertResponseIsSuccessful();

        $this->debugFunction(self::class, 'Act 8: GET /en/ page=2 then click first link to action show');
        $selector = 'table.table-striped tr td:first-child a.link[href*="/en/lottery/"]';
        $crawlerForm = $this->client->click($this->getLink($crawler, $selector));
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('');
        $this->assertSelectorTextContains('h2', 'User:');
        $this->assertSelectorTextContains('h3', 'Add your comment');
        $this->assertSelectorExists('form[name="comment_form"]');

        $this->debugFunction(self::class, 'Act 9: in action show submit POST form comment_form');
        $form = $crawlerForm->selectButton('Submit')->form();
        $this->user();
        $comment = $this->createComment($this->user);
        $form['comment_form[author]'] = $comment->getAuthor();
        $form['comment_form[text]'] = $comment->getText();
        $form['comment_form[email]'] = $comment->getEmail();
        $form['comment_form[photo]'] = $this->getPostCommentUploadedFile(__DIR__ . self::FULL_FILE_NAME);
        $this->client->submit($form);
        static::assertResponseRedirects();

        $this->debugFunction(self::class, 'Act 10: in action show submit POST form comment_form '
            . 'with form errors - the CSRF token is invalid');
        $form = $crawlerForm->selectButton('Submit')->form();
        $this->user();
        $comment = $this->createComment($this->user);
        unset($form['comment_form[_token]']);
        $form['comment_form[author]'] = $comment->getAuthor();
        $form['comment_form[text]'] = $comment->getText();
        $form['comment_form[email]'] = $comment->getEmail();
        $form['comment_form[photo]'] = $this->getPostCommentUploadedFile(__DIR__ . self::FULL_FILE_NAME);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertPageTitleContains('');
        $this->assertSelectorTextContains('h2', 'User:');
        $this->assertSelectorTextContains('h3', 'Add your comment');
        $this->assertSelectorExists('form[name="comment_form"]');
    }
}
