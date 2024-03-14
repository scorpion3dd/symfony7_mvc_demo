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

namespace App\Tests\Functional\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class CommentApiResourceFunctionalTest - for all functional tests for API routes
 * in Comment Entity by ApiResource with Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Functional\Api
 */
class CommentApiResourceFunctionalTest extends BaseApiResourceFunctional
{
    public const FULL_FILE_NAME = '/../data/Api/CommentUploadController/london1.jpg';

    /**
     * @testCase 1088 - Functional test for all operations in Comment Entity
     * by ApiResource with AUTH - must be a success
     * @link https://www.testrail.com/testCase/1088
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2088 - For all operations in Comment Entity by ApiResource with AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2088
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * without AUTH
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testCommentApiResources(): void
    {
        $this->debugFunction(self::class, 'testCommentApiResources');

        $commentLast = $this->commentLastRecordPublished();
        $commentId = (string)$commentLast->getId() ?? '';
        $this->act1LoginPost();
        $this->actGetCollectionList('2', self::ROUTE_API_COMMENTS, self::API_CONTEXTS_COMMENT);
        $this->actGetCollectionUser('3', self::ROUTE_API_COMMENTS_USER, self::API_CONTEXTS_COMMENT);
        $this->actGet('4', self::ROUTE_API_COMMENTS . '/' . $commentId, self::API_CONTEXTS_COMMENT, 'Comment');
        $this->act5Post();
        $this->act6PostUpload();
        $this->act7Patch400();
        $this->actLogoutGet('8');
    }

    /**
     * @param string $act
     * @param string $route
     * @param string $context
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function actGetCollectionUser(string $act, string $route, string $context): void
    {
        $this->debugFunction(self::class, 'Act ' . $act . ': GET ' . $route);

        $commentLast = $this->commentLastRecord();
        $response = $this->getClientWithCredentials()->request(
            Request::METHOD_GET,
            $route . $commentLast->getUser()->getId()
        );
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertJsonContains(['@context' => $context]);
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
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act5Post(): void
    {
        $this->debugFunction(self::class, 'Act 5: POST ' . self::ROUTE_API_COMMENTS);

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
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act6PostUpload(): void
    {
        $this->debugFunction(self::class, 'Act 6: POST ' . self::ROUTE_API_COMMENTS);

        $parameters = [
            'author' => 'Aroner Jacobson',
            'text' => "It's a great idea, from API POST",
            'email' => 'leta786438@runolfsdottir.com',
            'user' => '/api/users/8'
        ];
        $file = $this->getPostCommentUploadedFile(__DIR__ . self::FULL_FILE_NAME);
        $response = static::createClient()->request(
            Request::METHOD_POST,
            self::ROUTE_API_COMMENTS_UPLOAD,
            $this->getPostCommentUploadedFileOptions($file, $parameters)
        );
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
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function act7Patch400(): void
    {
        $this->debugFunction(self::class, 'Act 7: PATCH ' . self::ROUTE_API_COMMENTS . '/1');

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

    /**
     * @param string $content
     *
     * @return void
     */
    protected function assertEntity(string $content): void
    {
        $this->assertComment($content);
    }
}
