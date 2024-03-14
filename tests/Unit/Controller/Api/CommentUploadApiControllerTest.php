<?php
/**
 * This file is part of the Simple Web Demo Free CommentUpload Management Application.
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
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CommentUploadApiControllerTest - Unit tests for CommentUploadController without Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Unit\Controller
 */
class CommentUploadApiControllerTest extends BaseKernelTestCase
{
    public const FULL_FILE_NAME = '/../../data/Controller/Api/CommentUploadController/london1.jpg';

    /**
     * @testCase 1048 - Unit test invoke action for CommentUploadController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1048
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2048 - For CommentUploadController invoke action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2048
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * without AUTH
     * Accept: application/ld+json
     *     Act:
     * POST /api/comments/upload
     * Accept: application/ld+json
     * Content-Type: multipart/form-data; boundary=WebAppBoundary
     *
     * --WebAppBoundary
     * Content-Disposition: form-data; name="photoFile"; filename="london.jpg"
     * Content-Type: image/jpeg
     *
     * < \tests\Integration\data\Api\CommentUploadController\london.jpg
     * --WebAppBoundary--
     * Content-Disposition: form-data; name="author"
     * Content-Type: text/plain
     *
     * Aroner Jacobson
     * --WebAppBoundary--
     * Content-Disposition: form-data; name="text"
     * Content-Type: text/plain
     *
     * It's a great idea, from API POST
     * --WebAppBoundary--
     * Content-Disposition: form-data; name="email"
     * Content-Type: text/plain
     *
     * leta786438@runolfsdottir.com
     * --WebAppBoundary--
     * Content-Disposition: form-data; name="user"
     * Content-Type: text/plain
     *
     * /api/users/8
     * --WebAppBoundary--
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains Json
     *
     * https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html#doctrine-converter
     *
     * @dataProvider provideApiCommentUpload
     *
     * @param string $version
     * @param string $fileName
     * @param array $parameters
     *
     * @return void
     * @throws Exception
     */
    public function testApiCommentUpload(string $version, string $fileName, array $parameters): void
    {
        self::markTestSkipped(self::class . ' skipped testApiCommentUpload');
        $user = $this->createUser();
        $user->setId(1);
        $repositoryMock = $this->userRepositoryMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->willReturn($user);
        $this->container->set(UserRepositoryInterface::class, $repositoryMock);

        $comment = $this->createComment($user);
        $comment->setId(1);
        $commentServiceMock = $this->commentServiceMock();
        $commentServiceMock->expects($this->exactly(1))
            ->method('savePhotoFileApi')
            ->willReturn($comment);
        $commentServiceMock->expects($this->exactly(1))
            ->method('save');
        $commentServiceMock->expects($this->exactly(1))
            ->method('sendNotificationMessage');
        $this->container->set(CommentServiceInterface::class, $commentServiceMock);

        $file = $this->getPostCommentUploadedFile(__DIR__ . $fileName);
        $headers = [
            self::HEADER_ACCEPT => self::HEADER_APPLICATION_JSON_LD,
            self::HEADER_CONTENT_TYPE => self::HEADER_MULTIPART_FORM_DATA,
        ];
        $request = Request::create(
            self::ROUTE_API_COMMENTS_UPLOAD,
            Request::METHOD_POST,
            $parameters,
            [],
            ['photoFile' => $file],
            $headers
        );
        $response = $this->dispatch($request);
        if ($version == '1') {
            $expected = '';
            $content = $response->getContent();
            $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
            $this->assertIsString($expected, $content);
            $this->assertJsonStringEqualsJsonString($expected, $content);
            $this->assertJson($content);
        } else {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        }
    }

    /**
     * @return iterable
     */
    public static function provideApiCommentUpload(): iterable
    {
        $version = '1';
        $fileName = self::FULL_FILE_NAME;
        $parameters = [
            'author' => 'Aroner Jacobson',
            'text' => "It's a great idea, from API POST",
            'email' => 'leta7864381@runolfsdottir.com',
            'user' => '/api/users/8'
        ];
        yield $version => [$version, $fileName, $parameters];

//        $version = '2';
//        $parameters = [
//            'author' => '',
//            'text' => "It's a great idea, from API POST",
//            'email' => 'leta786438@runolfsdottir.com',
//            'user' => '/api/users/8'
//        ];
//        yield $version => [$version, $fileName, $parameters];
//
//        $version = '3';
//        $parameters = [
//            'author' => 'Aroner Jacobson',
//            'text' => "",
//            'email' => 'leta786438@runolfsdottir.com',
//            'user' => '/api/users/8'
//        ];
//        yield $version => [$version, $fileName, $parameters];
//
//        $version = '4';
//        $parameters = [
//            'author' => 'Aroner Jacobson',
//            'text' => "It's a great idea, from API POST",
//            'email' => '',
//            'user' => '/api/users/8'
//        ];
//        yield $version => [$version, $fileName, $parameters];
//
//        $version = '5';
//        $parameters = [
//            'author' => 'Aroner Jacobson',
//            'text' => "It's a great idea, from API POST",
//            'email' => 'leta786438@runolfsdottir.com',
//            'user' => ''
//        ];
//        yield $version => [$version, $fileName, $parameters];
//
//        $version = '6';
//        $fileName = 'qwerty.jpg';
//        $parameters = [
//            'author' => 'Aroner Jacobson',
//            'text' => "It's a great idea, from API POST",
//            'email' => 'leta786438@runolfsdottir.com',
//            'user' => '/api/users/8'
//        ];
//        yield $version => [$version, $fileName, $parameters];
    }
}
