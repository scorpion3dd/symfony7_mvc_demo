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

namespace App\Tests\Unit\DataFixtures;

use App\Helper\ApplicationGlobals;
use App\Tests\Unit\BaseKernelTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use PHPUnit\Framework\MockObject\Exception;

/**
 * Base class BaseTestAppFixtures - for all unit tests by Fixtures
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\DataFixtures
 */
class BaseTestAppFixtures extends BaseKernelTestCase
{
    /** @var ApplicationGlobals $appGlobalsMock */
    protected $appGlobalsMock;

    /** @var LoggerInterface $loggerMock */
    protected $loggerMock;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->appGlobalsMock = $this->createMock(ApplicationGlobals::class);
    }

    /**
     * @return SymfonyStyle
     * @throws Exception
     */
    public function getSymfonyStyle(): SymfonyStyle
    {
        $symfonyStyle = $this->createMock(SymfonyStyle::class);
        $symfonyStyle->expects(self::once())->method('listing');
        $symfonyStyle->expects(self::once())->method('title');
        $symfonyStyle->expects(self::once())->method('success');

        return $symfonyStyle;
    }
}
