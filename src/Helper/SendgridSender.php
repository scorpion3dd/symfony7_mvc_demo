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
use App\Util\LoggerTrait;
use Exception;
use Psr\Log\LoggerInterface;
use SendGrid;
use SendGrid\Mail\Mail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Class SendgridSender
 * @package App\Helper
 */
class SendgridSender
{
    use LoggerTrait;

    private const SUBJECT = 'Sending with SendGrid is Fun';

    /** @var SendGrid $sendgrid */
    private SendGrid $sendgrid;

    /**
     * @param string $key
     * @param string $adminEmail
     * @param string $adminName
     * @param LoggerInterface $logger
     */
    public function __construct(
        #[Autowire('%app.sendGridApiKey%')] private string $key,
        #[Autowire('%app.defaultAdminEmail%')] private string $adminEmail,
        #[Autowire('%app.defaultAdminName%')] private string $adminName,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->sendgrid = new SendGrid($this->key);
    }

    /**
     * @param Comment $comment
     *
     * @return int|null
     */
    public function send(Comment $comment): ?int
    {
        try {
            $email = new Mail();
            $email->setFrom($this->adminEmail, $this->adminName);
            $email->setSubject(self::SUBJECT);
            $email->addTo($comment->getEmail() ?? '', "UserTo " . $comment->getAuthor());
            $email->addContent("text/plain", $comment->getText() ?? '');
            $email->addContent("text/html", "<strong>" . ($comment->getText() ?? '') . "</strong>");
            $response = $this->sendgrid->send($email);

            return $response->statusCode();
        } catch (Exception $ex) {
            // @codeCoverageIgnoreStart
            $this->exception(self::class, $ex);

            return null;
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @param SendGrid $sendgrid
     */
    public function setSendgrid(SendGrid $sendgrid): void
    {
        $this->sendgrid = $sendgrid;
    }
}
