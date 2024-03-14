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

use App\Helper\ImageOptimizer;
use PHPUnit\Framework\TestCase;

/**
 * Class ImageOptimizerTest - Unit tests for helper ImageOptimizer
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Helper
 */
class ImageOptimizerTest extends TestCase
{
    /** @var ImageOptimizer $imageOptimizer */
    public ImageOptimizer $imageOptimizer;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->imageOptimizer = new ImageOptimizer();
    }

    /**
     * @testCase - method resize - must be a success
     *
     * @dataProvider provideResize
     *
     * @param int $expectedWidth
     * @param int $expectedHeight
     * @param string $filename
     *
     * @return void
     */
    public function testResize(int $expectedWidth, int $expectedHeight, string $filename): void
    {
        $this->imageOptimizer->resize($filename);
        list($width, $height) = getimagesize($filename);
        if ($expectedWidth == $width) {
            $this->assertSame($expectedWidth, $width);
        } elseif ($expectedHeight == $height) {
            $this->assertSame($expectedHeight, $height);
        }
    }

    /**
     * @return iterable
     */
    public static function provideResize(): iterable
    {
        $path = __DIR__ . '/../data/Helper/ImageOptimizer/';
        $fullFileNameFrom1 = $path . 'london1.jpg';
        $fullFileNameTo1 = $path . 'london_album.jpg';
        copy($fullFileNameFrom1, $fullFileNameTo1);
        yield 'london1' => [ImageOptimizer::MAX_WIDTH, ImageOptimizer::MAX_HEIGHT, $fullFileNameTo1];

        $fullFileNameFrom2 = $path . 'london2.jpg';
        $fullFileNameTo2 = $path . 'london_portret.jpg';
        copy($fullFileNameFrom2, $fullFileNameTo2);
        yield 'london2' => [ImageOptimizer::MAX_WIDTH, ImageOptimizer::MAX_HEIGHT, $fullFileNameTo2];
    }
}
