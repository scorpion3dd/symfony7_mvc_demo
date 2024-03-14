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

/**
 * Class ApplicationGlobals
 * @package App\Helper
 */
class ApplicationGlobals
{
    public const TYPE_APP_WORK = 'AppWork';
    public const TYPE_APP_FIXTURES = 'AppFixtures';
    public const TYPE_APP_TESTS = 'AppTests';
    public const TYPE_APP_TESTS_HIDE = 'AppTestsHide';
    public const TYPE_APP_TESTS_INTEGRATION = 'AppTestsIntegration';

    private string $type;

    /**
     * @param string $type
     */
    public function __construct(string $type = self::TYPE_APP_WORK)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
