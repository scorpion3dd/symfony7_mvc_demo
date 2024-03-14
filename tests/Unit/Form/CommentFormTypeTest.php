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

namespace App\Tests\Unit\Form;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentFormType;
use DateTime;
use Exception;
use Faker\Generator;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Validation;

/**
 * Class CommentFormTypeTest - Unit tests for Form CommentFormType
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Form
 */
class CommentFormTypeTest extends TypeTestCase
{
    public const FULL_FILE_NAME = '/../data/Service/CommentService/london1.jpg';

    /** @var Generator $faker */
    protected readonly Generator $faker;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = \Faker\Factory::create();
    }

    /**
     * @throws Exception
     */
    public function testSubmitValidData()
    {
        $genderId = User::randomGenderId();
        $gender = $genderId == User::GENDER_MALE_ID ? User::GENDER_MALE : User::GENDER_FEMALE;
        $comment = new Comment();
        $comment->setId(1);
        $comment->setAuthor($this->faker->name($gender));
        $comment->setEmail($this->faker->email());
        $comment->setText($this->faker->text(1024));
        $comment->setCreatedAt(new DateTime());
        $comment->setState(Comment::randomStateComment());
        $photoFile = new File(__DIR__ . self::FULL_FILE_NAME);
        $comment->setPhotoFile($photoFile);
        $formData = [
            'author' => $comment->getAuthor(),
            'text' => $comment->getText(),
            'email' => $comment->getEmail(),
            'photo' => $comment->getPhotoFile(),
        ];
        $validator = Validation::createValidator();
        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension($validator))
            ->getFormFactory();
        $form = $formFactory->create(CommentFormType::class, $comment);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $view = $form->createView();
        $children = $view->children;
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
