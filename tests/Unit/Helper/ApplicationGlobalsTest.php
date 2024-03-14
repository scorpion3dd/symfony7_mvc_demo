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

use App\Helper\ApplicationGlobals;
use PHPUnit\Framework\TestCase;

/**
 * Class ApplicationGlobalsTest - Unit tests for helper ApplicationGlobals
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Helper
 */
class ApplicationGlobalsTest extends TestCase
{
    /** @var ApplicationGlobals $applicationGlobals */
    public ApplicationGlobals $applicationGlobals;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->applicationGlobals = new ApplicationGlobals();
    }

    /**
     * @testCase - method getType - must be a success
     *
     * @dataProvider provideType
     *
     * @param string $expectedType
     * @param string $setType
     *
     * @return void
     */
    public function testGetType(string $expectedType, string $setType): void
    {
        $applicationGlobals = new ApplicationGlobals($setType);
        $type = $applicationGlobals->getType();
        $this->assertSame($expectedType, $type);
    }

    /**
     * @testCase - method setType - must be a success
     *
     * @dataProvider provideType
     *
     * @param string $expectedType
     * @param string $setType
     *
     * @return void
     */
    public function testSetType(string $expectedType, string $setType): void
    {
        $this->applicationGlobals->setType($setType);
        $type = $this->applicationGlobals->getType();
        $this->assertSame($expectedType, $type);
    }

    /**
     * @return iterable
     */
    public static function provideType(): iterable
    {
        yield ApplicationGlobals::TYPE_APP_WORK => [ApplicationGlobals::TYPE_APP_WORK, ApplicationGlobals::TYPE_APP_WORK];
        yield ApplicationGlobals::TYPE_APP_FIXTURES => [ApplicationGlobals::TYPE_APP_FIXTURES, ApplicationGlobals::TYPE_APP_FIXTURES];
    }
}
