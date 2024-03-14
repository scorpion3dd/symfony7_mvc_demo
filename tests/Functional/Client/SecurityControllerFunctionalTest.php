<?php
/**
 * This file is part of the Simple Web Demo Free Security Management Application.
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
 * Class SecurityControllerFunctionalTest - for all functional tests
 * in SecurityController with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 108 - Business process - Security
 * @link https://www.atlassian.com/software/confluence/bp/108
 *
 * @package App\Tests\Functional\Client
 */
class SecurityControllerFunctionalTest extends BaseControllerFunctional
{
    public const USERNAME = 'admin1';
    public const PASSWORD = 'admin1';

    /**
     * @testCase 1035 - Functional test actions for SecurityController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1035
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2035 - For SecurityController actions without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2035
     * @bp 108 - Business process - Security
     * @link https://www.atlassian.com/software/confluence/bp/108
     *     Arrange:
     * without AUTH
     *
     * @return void
     * @throws Exception
     */
    public function testSecurity(): void
    {
        $this->debugFunction(self::class, 'testSecurity');

        $this->debugFunction(self::class, 'Act 1: GET /en/registration');
        $this->client->request(Request::METHOD_GET, self::ROUTE_URL_REGISTRATION);
        $this->assertPageTitleContains('Registration!');
        $this->assertSelectorTextContains('h1.h3', 'Please registration');
        static::assertResponseIsSuccessful();

        $this->debugFunction(self::class, 'Act 2: GET /en/login');
        $crawlerForm = $this->client->request(Request::METHOD_GET, self::ROUTE_URL_LOGIN);
        $this->assertPageTitleContains('Log in!');
        $this->assertSelectorTextContains('h1.h3', 'Please sign in');
        static::assertResponseIsSuccessful();

        $this->debugFunction(self::class, 'Act 3: POST form submit /en/login');
        $form = $crawlerForm->selectButton('Sign in')->form();
        $form['username'] = self::USERNAME;
        $form['password'] = self::PASSWORD;
        $this->client->submit($form);
        static::assertResponseRedirects();
        $this->assertPageTitleContains('Redirecting to /en/admin');

        $this->debugFunction(self::class, 'Act 4: Redirecting to GET /en/admin');
        $crawler = $this->client->request(Request::METHOD_GET, self::ROUTE_URL_ADMIN);
        $this->assertPageTitleContains('Dashboard');
        $this->assertSelectorTextContains('h1', 'Dashboard');
        $this->assertSelectorTextContains('h2', 'Welcome to Admin application');
        $this->assertSelectorTextContains('strong', 'You have successfully enter in your Admin application!');
        $this->assertSelectorExists('canvas.admin_chart[id=chart_users]');

        $this->debugFunction(self::class, 'Act 5: link click Logout - GET /en/logout');
        $selector = 'a.menu-item-contents[href*="' . self::ROUTE_URL_LOGOUT . '"]';
        $this->client->click($this->getLink($crawler, $selector));
        static::assertResponseRedirects();
        $this->assertPageTitleContains('Redirecting to');
        $this->assertPageTitleContains(self::ROUTE_URL_LOGIN);
    }
}
