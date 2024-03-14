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

use App\Helper\SendgridSender;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use SendGrid;
use SendGrid\Response;

/**
 * Class SendgridSenderTest - Unit tests for helper SendgridSender
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Helper
 */
class SendgridSenderTest extends BaseKernelTestCase
{
    /** @var SendgridSender $sendgridSender */
    public SendgridSender $sendgridSender;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $key = $this->container->getParameter('app.sendGridApiKey');
        $adminEmail = $this->container->getParameter('app.defaultAdminEmail');
        $adminName = $this->container->getParameter('app.defaultAdminName');
        $this->logger = $this->container->get(LoggerInterface::class);
        $this->sendgridSender = new SendgridSender($key, $adminEmail, $adminName, $this->logger);
    }

    /**
     * @testCase - method send - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testSend(): void
    {
        $statusCodeExpected = 202;
        $sendgrid = $this->createMock(SendGrid::class);
        $response = new Response($statusCodeExpected);
        $sendgrid->expects(self::once())->method('send')->willReturn($response);
        $this->sendgridSender->setSendgrid($sendgrid);

        $user = $this->createUser();
        $comment = $this->createComment($user);
        $statusCode = $this->sendgridSender->send($comment);
        $this->assertEquals($statusCodeExpected, $statusCode);
    }

    /**
     * @testCase - method send - must be throw new Exception
     *
     * @return void
     * @throws Exception
     */
    public function testSendWithException(): void
    {
        $sendgrid = $this->createMock(SendGrid::class);
        $sendgrid->expects(self::once())->method('send')->willReturn(null);
        $this->sendgridSender->setSendgrid($sendgrid);

        $user = $this->createUser();
        $comment = $this->createComment($user);

        $this->expectExceptionMessage('Call to a member function statusCode() on null');
        $this->expectExceptionCode(0);
        $statusCode = $this->sendgridSender->send($comment);
        $statusCodeExpected = null;
        $this->assertEquals($statusCodeExpected, $statusCode);
    }
}
