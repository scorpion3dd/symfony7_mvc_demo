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

namespace App\Tests\Unit\Validator;

use App\DTO\CommentDTO;
use App\Validator\ValidatorCommentUpload;
use App\Tests\Unit\BaseKernelTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;

/**
 * Class ValidatorCommentUploadTest - Unit tests for State ValidatorCommentUpload
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Validator
 */
class ValidatorCommentUploadTest extends BaseKernelTestCase
{
    public const FULL_FILE_NAME = '/../data/Service/CommentService/london1.jpg';

    /** @var LoggerInterface $loggerMock */
    private LoggerInterface $loggerMock;

    /** @var ValidatorCommentUpload $validator */
    private ValidatorCommentUpload $validator;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->validator = new ValidatorCommentUpload($this->loggerMock);
    }

    /**
     * @testCase - method validate - must be a success
     *
     * @dataProvider provideApiCommentUpload
     *
     * @param string $version
     * @param string $fileName
     * @param array $parameters
     *
     * @return void
     */
//    #[DataProvider('provideApiCommentUpload')]
    public function testValidate(string $version, string $fileName, array $parameters): void
    {
        $photoFile = $this->getPostCommentUploadedFile(__DIR__ . $fileName);
        if (isset($photoFile)) {
            $files = ['photoFile' => $photoFile];
            $request = new Request([], $parameters, [], [], $files);
        } else {
            $request = new Request([], $parameters);
        }
        if ($version == '1') {
            $response = $this->validator->validate($request);
            $this->assertInstanceOf(CommentDTO::class, $response);
            $this->assertEquals($parameters['userId'], $response->userId);
            $this->assertEquals($parameters['author'], $response->author);
            $this->assertEquals($parameters['email'], $response->email);
            $this->assertEquals($parameters['text'], $response->text);
            $this->assertInstanceOf(UploadedFile::class, $response->uploadedFile);
        } else {
            $this->expectException(BadRequestHttpException::class);
            $this->expectExceptionMessage('Is required: ');
            $this->validator->validate($request);
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
            'user' => '/api/users/8',
            'userId' => 8
        ];
        yield $version => [$version, $fileName, $parameters];

        $version = '2';
        $parameters = [
            'author' => '',
            'text' => "It's a great idea, from API POST",
            'email' => 'leta786438@runolfsdottir.com',
            'user' => '/api/users/8',
            'userId' => 8
        ];
        yield $version => [$version, $fileName, $parameters];

        $version = '3';
        $parameters = [
            'author' => 'Aroner Jacobson',
            'text' => "",
            'email' => 'leta786438@runolfsdottir.com',
            'user' => '/api/users/8',
            'userId' => 8
        ];
        yield $version => [$version, $fileName, $parameters];

        $version = '4';
        $parameters = [
            'author' => 'Aroner Jacobson',
            'text' => "It's a great idea, from API POST",
            'email' => '',
            'user' => '/api/users/8',
            'userId' => 8
        ];
        yield $version => [$version, $fileName, $parameters];

        $version = '5';
        $parameters = [
            'author' => 'Aroner Jacobson',
            'text' => "It's a great idea, from API POST",
            'email' => 'leta786438@runolfsdottir.com',
            'user' => '',
            'userId' => 0
        ];
        yield $version => [$version, $fileName, $parameters];

        $version = '6';
        $fileName = 'qwerty.jpg';
        $parameters = [
            'author' => 'Aroner Jacobson',
            'text' => "It's a great idea, from API POST",
            'email' => 'leta786438@runolfsdottir.com',
            'user' => '/api/users/8',
            'userId' => 8
        ];
        yield $version => [$version, $fileName, $parameters];
    }
}
