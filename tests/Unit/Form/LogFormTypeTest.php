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

use App\Document\Log;
use App\Form\LogFormType;
use Carbon\Carbon;
use Exception;
use Faker\Generator;
use Monolog\Logger;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class LogFormTypeTest - Unit tests for Form LogFormType
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Form
 */
class LogFormTypeTest extends TypeTestCase
{
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
        $priority = Logger::DEBUG;
        $log = new Log();
        $log->setId('65ca40763e0da355c00b06d0');
        $log->setMessage($this->faker->text(100));
        $log->setPriority($priority);
        $priorityList = Log::getPriorities();
        $priorityName = $priorityList[$priority];
        $log->setPriorityName($priorityName);
        $log->setExtra(['currentUserId=1']);
        $log->setTimestamp(Carbon::parse('2023-01-01'));
        $formData = [
            'id' => $log->getId(),
            'message' => $log->getMessage(),
            'extra' => $log->getExtra(),
            'priority' => $log->getPriority(),
            'timestamp' => $log->getTimestamp(),
        ];
        $form = $this->factory->create(LogFormType::class, $log);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $view = $form->createView();
        $children = $view->children;
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
