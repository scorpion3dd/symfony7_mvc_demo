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

use App\Helper\UlidHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class UlidHelperTest - Unit tests for helper UlidHelper
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Helper
 */
class UlidHelperTest extends TestCase
{
    /** @var UlidHelper $ulidHelper */
    public UlidHelper $ulidHelper;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->ulidHelper = new UlidHelper();
    }

    /**
     * @testCase - method generate - must be a success
     *
     * @return void
     */
    public function testGenerate(): void
    {
        $ulid = $this->ulidHelper->generate();
        $this->assertIsString($ulid);
        $this->assertGreaterThan(10, strlen($ulid));
    }

    /**
     * @testCase - method isValid - must be a success
     *
     * @dataProvider provideType
     *
     * @param bool $expectedValid
     * @param string $ulid
     *
     * @return void
     */
    public function testIsValid(bool $expectedValid, string $ulid): void
    {
        $valid = $this->ulidHelper->isValid($ulid);
        $this->assertEquals($expectedValid, $valid);
    }

    /**
     * @return iterable
     */
    public static function provideType(): iterable
    {
        yield 'true' => [true, '01HJP0H41BXSRHGX6NWTBW7M9Y'];
        yield 'false' => [false, ''];
    }
}
