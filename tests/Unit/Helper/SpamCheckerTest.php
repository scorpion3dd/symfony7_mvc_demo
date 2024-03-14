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
use App\Helper\SpamChecker;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class SpamCheckerTest - Unit tests for helper SpamChecker
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Helper
 */
class SpamCheckerTest extends TestCase
{
    /**
     * @testCase - method getSpamScore - must be throw new RuntimeException
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testSpamScoreWithInvalidRequest(): void
    {
        $comment = new Comment();
        $comment->setCreatedAtValue();
        $context = [];

        $client = new MockHttpClient([new MockResponse(
            'invalid',
            ['response_headers' => ['x-akismet-debug-help: Invalid key']]
        )]);
        $checker = new SpamChecker($client, 'abcde', '', '');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to check for spam: invalid (Invalid key).');
        $this->expectExceptionCode(0);
        $checker->getSpamScore($comment, $context);
    }

    /**
     * @testCase - method getSpamScore - must be a success
     *
     * @dataProvider provideComments
     *
     * @param int $expectedScore
     * @param ResponseInterface $response
     * @param Comment $comment
     * @param array $context
     *
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testSpamScore(int $expectedScore, ResponseInterface $response, Comment $comment, array $context): void
    {
        $client = new MockHttpClient([$response]);
        $checker = new SpamChecker($client, 'abcde', '', '');

        $score = $checker->getSpamScore($comment, $context);
        $this->assertSame($expectedScore, $score);
    }

    /**
     * @return iterable
     */
    public static function provideComments(): iterable
    {
        $comment = new Comment();
        $comment->setCreatedAtValue();
        $context = [];

        $response = new MockResponse('', ['response_headers' => ['x-akismet-pro-tip: discard']]);
        yield 'blatant_spam' => [2, $response, $comment, $context];

        $response = new MockResponse('true');
        yield 'spam' => [1, $response, $comment, $context];

        $response = new MockResponse('false');
        yield 'ham' => [0, $response, $comment, $context];
    }
}
