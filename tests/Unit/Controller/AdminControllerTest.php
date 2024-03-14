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

use App\Repository\CommentRepositoryInterface;
use App\Service\CommentServiceInterface;
use App\Tests\Unit\BaseKernelTestCase;
use Doctrine\ORM\EntityRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;

/**
 * Class AdminControllerTest - Unit tests for AdminController with Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 109 - Business process - Admin
 * @link https://www.atlassian.com/software/confluence/bp/109
 *
 * @package App\Tests\Unit\Controller
 */
class AdminControllerTest extends BaseKernelTestCase
{
    /**
     * @testCase 1036 - Unit test reviewComment action for AdminController without AUTH - must be a success
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
     * https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html#doctrine-converter
     *
     * @dataProvider provideReviewComment
     *
     * @param string $expected
     * @param string $transition
     * @return void
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testReviewComment(string $expected, string $transition): void
    {
        $user = $this->createUser();
        $user->setId(1);
        $comment = $this->createComment($user);
        $comment->setId(1);
//        $repositoryMock = $this->commentRepositoryMock();
        $repositoryMock = $this->createMock(EntityRepository::class);
        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->willReturn($comment);
        $this->container->set(CommentRepositoryInterface::class, $repositoryMock);

        $commentServiceMock = $this->commentServiceMock();
        $commentServiceMock->expects($this->exactly(1))
            ->method('reviewComment')
            ->willReturn($transition);
        $this->container->set(CommentServiceInterface::class, $commentServiceMock);

        $id = 1;
        $request = Request::create(self::ROUTE_URL_COMMENT_REVIEW . $id, Request::METHOD_GET);
        $response = $this->dispatch($request);

        $this->assertStringContainsString($expected, $response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @return iterable
     */
    public static function provideReviewComment(): iterable
    {
        $version = '1';
        $expected = 'Comment already reviewed or not in the right state.';
        $transition = '';
        yield $version => [$expected, $transition];

        $version = '2';
        $expected = 'Comment reviewed, thank you!';
        $transition = 'publish';
        yield $version => [$expected, $transition];
    }

    /**
     * @testCase 1037 - Unit test purgeHttpCache action for AdminController without AUTH - must be a success
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
     * @param string $version
     * @param string $expectedString
     * @param int $expectedStatusCode
     * @param bool $isProd
     *
     * @return void
     */
    public function testPurgeHttpCache(string $version, string $expectedString, int $expectedStatusCode, bool $isProd): void
    {
        $commentServiceMock = $this->commentServiceMock();
        $commentServiceMock->expects($this->exactly(1))
            ->method('isProd')
            ->willReturn($isProd);
        $this->container->set(CommentServiceInterface::class, $commentServiceMock);

        $storeMock = $this->storeMock();
        if ($version == '2') {
            $storeMock->expects($this->exactly(1))
                ->method('purge');
        }
        $this->container->set(StoreInterface::class, $storeMock);

        $request = Request::create(self::ROUTE_URL_PURGE_HTTP_CACHE, Request::METHOD_PURGE);
        $response = $this->dispatch($request);

        $this->assertStringContainsString($expectedString, $response->getContent());
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    /**
     * @return iterable
     */
    public static function providePurgeHttpCache(): iterable
    {
        $version = '1';
        $expectedString = 'KO';
        $isProd = true;
        $expectedStatusCode = Response::HTTP_BAD_REQUEST;
        yield $version => [$version, $expectedString, $expectedStatusCode, $isProd];

        $version = '2';
        $expectedString = 'Done';
        $isProd = false;
        $expectedStatusCode = Response::HTTP_OK;
        yield $version => [$version, $expectedString, $expectedStatusCode, $isProd];
    }
}
