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

use App\Helper\UserChartHelper;
use App\Repository\UserRepository;
use App\Tests\Unit\BaseKernelTestCase;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * Class UserChartHelperTest - Unit tests for helper UserChartHelper
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Helper
 */
class UserChartHelperTest extends BaseKernelTestCase
{
    /** @var UserChartHelper $userChartHelper */
    private UserChartHelper $userChartHelper;

    /** @var TranslatorInterface|null $translator */
    private ?TranslatorInterface $translator;

    /** @var ChartBuilderInterface|null $chartBuilder */
    private ?ChartBuilderInterface $chartBuilder;

    /** @var UserRepository|null $userRepository */
    private ?UserRepository $userRepository;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->translator = $this->container->get(TranslatorInterface::class);
        $this->chartBuilder = $this->container->get(ChartBuilderInterface::class);
        $this->userRepository = $this->container->get(UserRepository::class);
        $this->userChartHelper = new UserChartHelper($this->translator, $this->chartBuilder, $this->userRepository);
    }

    /**
     * @testCase - method createChart - must be a success
     *
     * @return void
     */
    public function testCreateChart(): void
    {
        $chart = $this->userChartHelper->createChart();
        $this->assertInstanceOf(Chart::class, $chart);
        $this->assertEquals(Chart::TYPE_BAR, $chart->getType());
    }

    /**
     * @testCase - method getDataChartUsers - must be a success
     *
     * @dataProvider provideGetDataChartUsers
     *
     * @param string $dataChartUsersExpected
     * @param array $items
     *
     * @return void
     * @throws Exception
     */
    public function testGetDataChartUsers(string $dataChartUsersExpected, array $items): void
    {
        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->onlyMethods(['findUsersAccessed'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock->expects($this->exactly(1))
            ->method('findUsersAccessed')
            ->willReturn($items);
        $this->userChartHelper->setUserRepository($repositoryMock);

        $dataChartUsers = $this->userChartHelper->getDataChartUsers();
        $this->assertIsString($dataChartUsers);
        $this->assertGreaterThan(10, strlen($dataChartUsers));
        $this->assertStringStartsWith($dataChartUsersExpected, $dataChartUsers);
    }

    /**
     * @return iterable
     */
    public static function provideGetDataChartUsers(): iterable
    {
        $dataChartUsersExpected1 = '{ year: 2022, count_new_users: 4, count_accessed_users:';
        $item = [
            'countCreatedAt' => 4,
            'countUpdatedAt' => 4,
            'yearAt' => 2022,
        ];
        $items1 = [];
        $items1[] = $item;
        $item = [
            'countCreatedAt' => 2,
            'countUpdatedAt' => 3,
            'yearAt' => 2023,
        ];
        $items1[] = $item;
        yield '1' => [$dataChartUsersExpected1, $items1];

        $dataChartUsersExpected2 = '{ year: 2000, count_new_users:';
        $items2 = [];
        yield '2' => [$dataChartUsersExpected2, $items2];
    }

    /**
     * @testCase - method getJavascript - must be a success
     *
     * @return void
     */
    public function testGetJavascript(): void
    {
        $javascriptExpected = '<script type="text/javascript">';
        $javascript = $this->userChartHelper->getJavascript();
        $this->assertIsString($javascript);
        $this->assertGreaterThan(10, strlen($javascript));
        $this->assertStringStartsWith($javascriptExpected, $javascript);
    }
}
