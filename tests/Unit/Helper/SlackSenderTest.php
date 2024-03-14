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

namespace App\Tests\Unit\Helper;

use App\Entity\Comment;
use App\Helper\SlackSender;
use App\Tests\Unit\BaseKernelTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class SlackSenderTest - Unit tests for helper SlackSender
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Helper
 */
class SlackSenderTest extends BaseKernelTestCase
{
    /** @var string $key */
    private string $key;

    /** @var string $url */
    private string $url;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->key = $this->container->getParameter('app.slackTokenPost');
        $this->url = $this->container->getParameter('app.slackTokenUrl');
    }

    /**
     * @testCase - method send - must be a success
     *
     * @dataProvider provideComments
     *
     * @param int $expectedStatusCode
     * @param ResponseInterface $response
     * @param Comment $comment
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testSend(int $expectedStatusCode, ResponseInterface $response, Comment $comment): void
    {
        $clientMock = new MockHttpClient([$response]);
        $slackSender = new SlackSender($clientMock, $this->key, $this->url);
        $comment->setText($this->faker->text(100));
        $statusCode = $slackSender->send($comment);
        $this->assertSame($expectedStatusCode, $statusCode);
    }

    /**
     * @return iterable
     */
    public static function provideComments(): iterable
    {
        $comment = new Comment();

        $response = new MockResponse('json', ['headers' => [SlackSender::CONTENT_TYPE]]);
        yield '2' => [200, $response, $comment];

        $response = new MockResponse('true');
        yield '1' => [200, $response, $comment];

        $response = new MockResponse('false');
        yield '0' => [200, $response, $comment];
    }
}
