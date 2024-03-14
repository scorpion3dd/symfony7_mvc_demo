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

namespace App\Tests\Integration\Api;

use App\Tests\BaseApiControllerIntegrationTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class CommentUploadApiControllerIntegrationTest - Integration tests for CommentUploadController without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Integration\Api
 */
class CommentUploadApiControllerIntegrationTest extends BaseApiControllerIntegrationTest
{
    public const FULL_FILE_NAME = '/../data/Api/CommentUploadController/london1.jpg';

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
    public function testApiCommentUpload(string $version, string $fileName, array $parameters): void
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
        } elseif ($version == '7') {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
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

        $version = '7';
        $fileName = self::FULL_FILE_NAME;
        $parameters = [
            'author' => 'Aroner Jacobson',
            'text' => "It's a great idea, from API POST",
            'email' => 'leta786438@runolfsdottir.com',
            'user' => '/api/users/-8'
        ];
        yield $version => [$version, $fileName, $parameters];
    }
}
