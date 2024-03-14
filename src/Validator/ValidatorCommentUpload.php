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

namespace App\Validator;

use App\DTO\CommentDTO;
use App\Util\LoggerTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ValidatorCommentUpload
 * @package App\Validator
 */
class ValidatorCommentUpload
{
    use LoggerTrait;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->debugConstruct(self::class);
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @psalm-suppress InternalMethod
     * @param Request $request
     *
     * @return CommentDTO
     */
    public function validate(Request $request): CommentDTO
    {
        $message = 'Is required: ';
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('photoFile');
        if (empty($uploadedFile)) {
            $message .= '"photoFile", ';
        }
        $author = $request->get('author', '');
        if (! $author) {
            $message .= '"author", ';
        }
        $text = $request->get('text', '');
        if (! $text) {
            $message .= '"text", ';
        }
        $email = $request->get('email', '');
        if (! $email) {
            $message .= '"email", ';
        }
        $userIri = $request->get('user', '');
        if (! $userIri) {
            $message .= '"user IRI", ';
        }
        $userAr = explode('/', $userIri);
        $userId = isset($userAr[3]) && is_numeric($userAr[3]) ? (int)$userAr[3] : 0;
        if (! $userId) {
            $message .= '"user userId", ';
        }
        if ($message != 'Is required: ') {
            throw new BadRequestHttpException($message);
        }
        /** @var UploadedFile $uploadedFile */
        return new CommentDTO($userId, $author, $email, $text, $uploadedFile);
    }
}
