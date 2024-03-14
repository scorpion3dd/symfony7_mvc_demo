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

namespace App\Tests\Unit\Document;

use App\Document\Log;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class LogTest - Unit tests for Document Log
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package App\Tests\Unit\Document
 */
class LogTest extends BaseKernelTestCase
{
    /** @var Log $log */
    public Log $log;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->log = $this->createLog();
    }

    /**
     * @testCase - function getPriorityRandom - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetPriorityRandom(): void
    {
        $this->assertIsInt($this->log->getPriorityRandom());
    }

    /**
     * @testCase - function getExtraString - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetExtraString(): void
    {
        $expected = 'currentUserId=1';
        $extra = [$expected];
        $this->log->setExtra($extra);
        $this->assertEquals($expected, $this->log->getExtraString());
    }
}
