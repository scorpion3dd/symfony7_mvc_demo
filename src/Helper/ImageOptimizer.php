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

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

/**
 * Class ImageOptimizer
 * @package App\Helper
 */
class ImageOptimizer
{
    public const MAX_WIDTH = 200;
    public const MAX_HEIGHT = 150;

    private Imagine $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    /**
     * @psalm-suppress PossiblyInvalidArrayAccess
     * @param string $filename
     *
     * @return void
     */
    public function resize(string $filename): void
    {
        if (file_exists($filename)) {
            /** @phpstan-ignore-next-line */
            list($iwidth, $iheight) = getimagesize($filename);
            $ratio = $iwidth / $iheight;
            $width = self::MAX_WIDTH;
            $height = self::MAX_HEIGHT;
            if ($width / $height > $ratio) {
                $width = $height * $ratio;
            } else {
                $height = $width / $ratio;
            }
            $photo = $this->imagine->open($filename);
            $photo->resize(new Box((int) round($width), (int) round($height)))->save($filename);
        }
    }
}
