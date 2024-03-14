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

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

$cors1 = getenv('CORS_ALLOW_ORIGIN');
$cors2 = getenv('PHP_CORS_ALLOW_ORIGIN');
$cors3 = getenv('DATABASE_URL');
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
