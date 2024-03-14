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

namespace App\Tests\Integration\Api;

use App\Tests\BaseApiControllerIntegrationTest;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class CommentApiResourceIntegrationTest - Integration tests for API routes in
 * Comment Entity by ApiResource with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Integration\Api
 */
class CommentApiResourceIntegrationTest extends BaseApiControllerIntegrationTest
{
    public const FULL_FILE_NAME = '/../data/Api/CommentUploadController/london1.jpg';

    /**
     * @testCase 1083 - Integration test GetCollection operation in Comment Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1083
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2083 - For GetCollection operation in Comment Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2083
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/comments
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 200 - HTTP_OK
     * Response Content contains Json
     * Response Header Content-Type contains application/ld+json; charset=utf-8
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testApiComments(): void
    {
        $response = $this->getClientWithCredentials()->request(Request::METHOD_GET, self::ROUTE_API_COMMENTS);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_COMMENT]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_COMMENTS]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertNotEmpty($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertNotEmpty($json[self::API_HYDRA_MEMBER]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1084 - Integration test GetCollection operation in Comment Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1084
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2084 - For GetCollection operation in Comment Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2084
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/comments?userId=1
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 200 - HTTP_OK
     * Response Content contains Json
     * Response Header Content-Type contains application/ld+json; charset=utf-8
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testApiCommentsUser(): void
    {
        $commentLast = $this->commentLastRecord();
        $response = $this->getClientWithCredentials()->request(
            Request::METHOD_GET,
            self::ROUTE_API_COMMENTS_USER . $commentLast->getUser()->getId()
        );
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_COMMENT]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_COMMENTS]);
        $this->assertJsonContains(['@type' => self::API_HYDRA_COLLECTION]);
        $json = json_decode($content, true);
        $this->assertIsInt($json[self::API_HYDRA_TOTAL_ITEMS]);
        $this->assertIsArray($json[self::API_HYDRA_MEMBER]);
        $this->assertNotEmpty($json[self::API_HYDRA_VIEW]);
        $this->assertNotEmpty($json[self::API_HYDRA_SEARCH]);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1085 - Integration test Get operation in Comment Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1085
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2085 - For Get operation in Comment Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2085
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request GET /api/comments/1
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 200 - HTTP_OK
     * Response Content contains Json
     * Response Header Content-Type contains application/ld+json; charset=utf-8
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testApiCommentsGet(): void
    {
        $commentLast = $this->commentLastRecordPublished();
        $response = $this->getClientWithCredentials()->request(
            Request::METHOD_GET,
            self::ROUTE_API_COMMENTS  . '/' . $commentLast->getId()
        );
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_COMMENT]);
        $this->assertJsonContains(['@id' => self::ROUTE_API_COMMENTS  . '/' . $commentLast->getId()]);
        $this->assertJsonContains(['@type' => 'Comment']);
        $this->assertComment($content);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1086 - Integration test Post operation in Comment Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1086
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2086 - For Post operation in Comment Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2086
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request POST /api/comments
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 200 - HTTP_OK
     * Response Content contains Json
     * Response Header Content-Type contains application/ld+json; charset=utf-8
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testApiCommentsPost(): void
    {
        $commentLast = $this->commentLastRecord();
        $number = 1;
        if (isset($commentLast)) {
            $number = $commentLast->getId() + 1;
        }
        $user = $this->userLastRecord();
        $commentNew = $this->createComment($user);
        $options = [
            'json' => [
                'author' => $commentNew->getAuthor() . (string)$number,
                'text' => $commentNew->getText(),
                'email' => $commentNew->getEmail(),
                'user' => $commentNew->getUserIri(),
            ]
        ];
        $response = $this->getClientWithCredentials()->request(Request::METHOD_POST, self::ROUTE_API_COMMENTS, $options);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => self::API_CONTEXTS_COMMENT]);
        $this->assertJsonContains(['@type' => 'Comment']);
        $this->assertJsonContains(['text' => $commentNew->getText()]);
        $this->assertJsonContains(['email' => $commentNew->getEmail()]);
        $this->assertComment($content);
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @testCase 1048 - Integration test invoke action for CommentUploadController without AUTH - must be a success
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
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testApiCommentsPostUpload(string $version, string $fileName, array $parameters): void
    {
        $file = $this->getPostCommentUploadedFile(__DIR__ . $fileName);
        $response = static::createClient()->request(
            Request::METHOD_POST,
            self::ROUTE_API_COMMENTS_UPLOAD,
            $this->getPostCommentUploadedFileOptions($file, $parameters)
        );
        if ($version == '1') {
            $content = $response->getContent();
            $this->assertIsString($content);
            $this->assertJson($content);
            $this->assertJsonContains(['@type' => 'Comment']);
            $this->assertJsonContains(['author' => $parameters['author']]);
            $this->assertJsonContains(['text' => $parameters['text']]);
            $this->assertJsonContains(['email' => $parameters['email']]);
            $this->assertJsonContains(['user' => $parameters['user']]);
            $json = json_decode($content, true);
            $this->assertJsonContains(['photoFilename' => $json['photoFilename']]);
            $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_CHARSET);
            $this->assertResponseIsSuccessful();
        } else {
            $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
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
            'email' => 'leta786438@runolfsdottir.com',
            'user' => '/api/users/8'
        ];
        yield $version => [$version, $fileName, $parameters];

        $version = '2';
        $parameters = [
            'author' => '',
            'text' => "It's a great idea, from API POST",
            'email' => 'leta786438@runolfsdottir.com',
            'user' => '/api/users/8'
        ];
        yield $version => [$version, $fileName, $parameters];

        $version = '3';
        $parameters = [
            'author' => 'Aroner Jacobson',
            'text' => "",
            'email' => 'leta786438@runolfsdottir.com',
            'user' => '/api/users/8'
        ];
        yield $version => [$version, $fileName, $parameters];

        $version = '4';
        $parameters = [
            'author' => 'Aroner Jacobson',
            'text' => "It's a great idea, from API POST",
            'email' => '',
            'user' => '/api/users/8'
        ];
        yield $version => [$version, $fileName, $parameters];

        $version = '5';
        $parameters = [
            'author' => 'Aroner Jacobson',
            'text' => "It's a great idea, from API POST",
            'email' => 'leta786438@runolfsdottir.com',
            'user' => ''
        ];
        yield $version => [$version, $fileName, $parameters];

        $version = '6';
        $fileName = 'qwerty.jpg';
        $parameters = [
            'author' => 'Aroner Jacobson',
            'text' => "It's a great idea, from API POST",
            'email' => 'leta786438@runolfsdottir.com',
            'user' => '/api/users/8'
        ];
        yield $version => [$version, $fileName, $parameters];
    }

    /**
     * @testCase 1087 - Integration test Patch operation in Comment Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1087
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2087 - For Patch operation in Comment Entity by ApiResource
     * with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2087
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * with AUTH
     *     Act:
     * Request PATCH /api/comments/1
     * Request Header Authorization: Bearer token
     * Request Header Accept: application/ld+json
     *     Assert:
     * Response StatusCode = 400 - HTTP_BAD_REQUEST
     * Response Header Content-Type contains application/ld+json
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testApiCommentsPatch400(): void
    {
        $commentLast = $this->commentLastRecordPublished();
        $number = 1;
        if (isset($commentLast)) {
            $number = $commentLast->getId() + 1;
        }
        $user = $this->userLastRecord();
        $commentNew = $this->createComment($user);
        $options = [
            'json' => [
                'author' => $commentNew->getAuthor() . (string)$number,
                'text' => $commentNew->getText(),
                'email' => $commentNew->getEmail(),
                'user' => $commentNew->getUserIri(),
            ]
        ];
        $this->getClientWithCredentials()->request(
            Request::METHOD_PATCH,
            self::ROUTE_API_COMMENTS . '/' . $commentLast->getId(),
            $options
        );
        $this->assertResponseHeaderSame(self::HEADER_CONTENT_TYPE, self::HEADER_APPLICATION_JSON_LD);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
