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

namespace App\Tests\Unit\Controller;

use App\Repository\UserRepositoryInterface;
use App\Service\CommentServiceInterface;
use App\Service\UserServiceInterface;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LotteryControllerTest - for all unit tests in LotteryController
 * with Auth and without Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 107 - Business process - Lottery
 * @link https://www.atlassian.com/software/confluence/bp/107
 *
 * @package App\Tests\Unit\Controller
 */
class LotteryControllerTest extends BaseKernelTestCase
{
    protected const SLUG = 'VMRAZ11-01HJKRAHBKJ4J5B66G7J9R6DRS';

    /**
     * @testCase 1026 - Unit test indexNoLocale action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1026
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2026 - For LotteryController indexNoLocale action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2026
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
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
        $request = Request::create(self::ROUTE_URL_INDEX_NO_LOCALE, Request::METHOD_GET);
        $response = $this->dispatch($request);
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @testCase 1027 - Unit test about action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1027
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2027 - For LotteryController about action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2027
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
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
        $request = Request::create(self::ROUTE_URL_ABOUT, Request::METHOD_GET);
        $response = $this->dispatch($request);

        $this->assertStringContainsString(
            'About - This is the Simple Web Demo Free Lottery Management Application',
            $response->getContent()
        );
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @testCase 1028 - Unit test language action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1028
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2028 - For LotteryController language action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2028
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
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
        $request = Request::create(self::ROUTE_URL_LANGUAGE, Request::METHOD_GET);
        if ($version == '1') {
            $request = Request::create(self::ROUTE_URL_LANGUAGE, Request::METHOD_GET);
        } elseif ($version == '2') {
            $request = Request::create(self::ROUTE_URL_LANGUAGE, Request::METHOD_GET, [], [], [], $server);
        }
        $response = $this->dispatch($request);

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
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
     * @testCase 1029 - Unit test index action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1029
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2029 - For LotteryController index action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2029
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     *     Act:
     * GET /en/
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains string
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testIndex(): void
    {
        $page = 2;
        $template = 'pagination/sliding.html.twig';

        $paginatorMock = $this->createMock(PaginatorInterface::class);

        $userServiceMock = $this->userServiceMock();
        $userServiceMock->expects($this->exactly(1))
            ->method('getUsersPaginator')
            ->with(
                $this->equalTo($page),
                $this->equalTo($template),
            )
            ->willReturn($paginatorMock);
        $this->container->set(UserServiceInterface::class, $userServiceMock);

        $request = Request::create(self::ROUTE_URL_INDEX, Request::METHOD_GET, ['page' => $page]);
        $response = $this->dispatch($request);

        $this->assertStringContainsString('Welcome!', $response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @testCase 1030 - Unit test show action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1030
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2030 - For LotteryController show action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2030
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     *     Act:
     * GET /en/lottery/1
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains string
     *
     * https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html#doctrine-converter
     *
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testShowGet(): void
    {
        $user = $this->createUser();
        $user->setId(1);
        $repositoryMock = $this->userRepositoryMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->willReturn($user);
        $this->container->set(UserRepositoryInterface::class, $repositoryMock);

        $commentPaginator = null;
        $commentServiceMock = $this->commentServiceMock();
        $commentServiceMock->expects($this->exactly(1))
            ->method('getCommentPaginator')
            ->willReturn($commentPaginator);
        $this->container->set(CommentServiceInterface::class, $commentServiceMock);

        $this->formMock(false, false);

        $request = Request::create(self::ROUTE_URL_SHOW . self::SLUG, Request::METHOD_GET);
        $response = $this->dispatch($request);

        $this->assertStringContainsString('User:', $response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @testCase 1030 - Unit test show action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1030
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2030 - For LotteryController show action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2030
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     *     Act:
     * POST /en/lottery/1
     *   Form errors:
     * The CSRF token is invalid. Please try to resubmit the form.
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains string
     *
     * https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html#doctrine-converter
     *
     * @return void
     * @throws Exception
     */
    public function testShowPostFormErrors(): void
    {
        $user = $this->createUser();
        $user->setId(1);
        $repositoryMock = $this->userRepositoryMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->willReturn($user);
        $this->container->set(UserRepositoryInterface::class, $repositoryMock);

        $commentServiceMock = $this->commentServiceMock();
        $commentServiceMock->expects($this->exactly(1))
            ->method('send');
        $this->container->set(CommentServiceInterface::class, $commentServiceMock);

        $this->formMock(true, false);

        $request = Request::create(
            self::ROUTE_URL_SHOW . self::SLUG,
            Request::METHOD_POST,
            $this->getPostParametersComment(),
            [],
            $this->getPostFilesCommentUploadedFile()
        );
        $response = $this->dispatch($request);

        $this->assertStringContainsString('User:', $response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @testCase 1030 - Unit test show action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1030
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2030 - For LotteryController show action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2030
     * @bp 107 - Business process - Lottery
     * @link https://www.atlassian.com/software/confluence/bp/107
     *     Arrange:
     *     Act:
     * POST /en/lottery/1
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains string
     *
     * https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html#doctrine-converter
     *
     * @return void
     * @throws Exception
     */
    public function testShowPost(): void
    {
        $user = $this->createUser();
        $user->setId(1);
        $repositoryMock = $this->userRepositoryMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->willReturn($user);
        $this->container->set(UserRepositoryInterface::class, $repositoryMock);

        $this->formMock();

        $comment = $this->createComment($user);
        $comment->setId(1);
        $commentServiceMock = $this->commentServiceMock();
        $commentServiceMock->expects($this->exactly(1))
            ->method('savePhotoFile')
            ->willReturn($comment);
        $commentServiceMock->expects($this->exactly(1))
            ->method('save');
        $commentServiceMock->expects($this->exactly(1))
            ->method('sendNotificationMessage');
        $this->container->set(CommentServiceInterface::class, $commentServiceMock);

        $request = Request::create(
            self::ROUTE_URL_SHOW . self::SLUG,
            Request::METHOD_POST,
            $this->getPostParametersComment(),
            [],
            $this->getPostFilesCommentUploadedFile()
        );
        $response = $this->dispatch($request);

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
