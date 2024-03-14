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

namespace App\Helper;

use App\Entity\Comment;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class SpamChecker
 * @package App\Helper
 */
class SpamChecker
{
    private const METHOD = 'POST';
    private const SHEMA = 'https://';
    private const LANG = 'en';
    private const CHARSET = 'UTF-8';
    private const HEADER_PRO = 'x-akismet-pro-tip';
    private const HEADER_DEBUG = 'x-akismet-debug-help';

    private string $endpoint;

    /**
     * @param HttpClientInterface $client
     * @param string $akismetKey
     * @param string $akismetUrl
     * @param string $domain
     */
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire('%app.akismetKey%')] string $akismetKey,
        #[Autowire('%app.akismetUrl%')] string $akismetUrl,
        #[Autowire('%app.domain%')] private string $domain,
    ) {
        $this->endpoint = sprintf($akismetUrl, $akismetKey);
    }

    /**
     * @param Comment $comment
     * @param array $context
     *
     * @return int Spam score: 0: not spam, 1: maybe spam, 2: blatant spam
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getSpamScore(Comment $comment, array $context): int
    {
        $createdAt = $comment->getCreatedAt();
        $response = $this->client->request(self::METHOD, $this->endpoint, [
            'body' => array_merge($context, [
                'blog' => self::SHEMA . $this->domain,
                'comment_type' => 'comment',
                'comment_author' => $comment->getAuthor(),
                'comment_author_email' => $comment->getEmail(),
                'comment_content' => $comment->getText(),
                'comment_date_gmt' => isset($createdAt) ? $createdAt->format('c') : '',
                'blog_lang' => self::LANG,
                'blog_charset' => self::CHARSET,
                'is_test' => true,
            ]),
        ]);

        $headers = $response->getHeaders();
        if ('discard' === ($headers[self::HEADER_PRO][0] ?? '')) {
            return 2;
        }

        $content = $response->getContent();
        if (isset($headers[self::HEADER_DEBUG][0])) {
            $header = $headers[self::HEADER_DEBUG][0];
            throw new RuntimeException(sprintf('Unable to check for spam: %s (%s).', $content, $header));
        }

        return 'true' === $content ? 1 : 0;
    }
}
