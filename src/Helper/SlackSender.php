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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class SlackSender
 * @package App\Helper
 */
class SlackSender
{
    public const CONTENT_TYPE = 'application/json';
    private const METHOD = 'POST';

    private string $endpoint;

    /**
     * @param HttpClientInterface $client
     * @param string $key
     * @param string $url
     */
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire('%app.slackTokenPost%')] string $key,
        #[Autowire('%app.slackTokenUrl%')] string $url,
    ) {
        $this->endpoint = sprintf($url, $key);
    }

    /**
     * @param Comment $comment
     *
     * @return int
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function send(Comment $comment): int
    {
        $response = $this->client->request(self::METHOD, $this->endpoint, [
            'headers' => [
                'Content-Type' => self::CONTENT_TYPE,
            ],
            'json' => [
                'text' => $comment->getText()
            ],
        ]);

        return $response->getStatusCode();
    }
}
