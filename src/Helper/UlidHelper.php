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

use Symfony\Component\Uid\Ulid;

/**
 * Class UlidHelper - Universally Unique Lexicographically Sortable Identifier.
 * @see https://github.com/ulid/spec
 * @package App\Helper
 */
class UlidHelper
{
    /**
     * @return string
     */
    public static function generate(): string
    {
        return Ulid::generate();
    }

    /**
     * @param string $ulid
     *
     * @return bool
     */
    public static function isValid(string $ulid): bool
    {
        return Ulid::isValid($ulid);
    }
}
