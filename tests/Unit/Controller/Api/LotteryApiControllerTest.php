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

namespace App\Tests\Unit\Controller;

use App\Service\UserServiceInterface;
use App\Tests\Unit\BaseKernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LotteryApiControllerTest - Unit tests for LotteryController without Auth
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 110 - Business process - API
 * @link https://www.atlassian.com/software/confluence/bp/110
 *
 * @package App\Tests\Unit\Controller
 */
class LotteryApiControllerTest extends BaseKernelTestCase
{
    /** @var array|array[] $users */
    private array $users = [
        [
            'id' => 1,
            'uid' => '01HMPW5A2XAG0FHSYQ04R9MFDH',
            'email' => 'resident-1@lottery.com',
            'fullName' => 'Rory Ryan III',
            'dateBirthday' => [
                'date' => '2024-01-27 20:09:08.587135',
                'timezone' => 'Europe/Madrid',
                'timezone_type' => 3,
            ],
            'gender' => 1,
            'slug' => 'KARLI.KOVACEK1-01HMPW5A2XAG0FHSYQ04R9MFDH',
            'commentsCount' => 0
        ]
    ];

    /**
     * @testCase 1047 - Unit test invoke action for LotteryController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1047
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2047 - For LotteryController invoke action without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2047
     * @bp 110 - Business process - API
     * @link https://www.atlassian.com/software/confluence/bp/110
     *     Arrange:
     * without AUTH
     * Accept: application/ld+json
     *     Act:
     * GET /api/users/lottery2
     *     Assert:
     * StatusCode = 200 - HTTP_OK
     * Response content contains Json
     *
     * https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html#doctrine-converter
     *
     * @dataProvider provideApiLottery
     *
     * @param string $version
     * @param int $page
     *
     * @return void
     */
    public function testApiLottery(string $version, int $page): void
    {
        $userServiceMock = $this->userServiceMock();
        $userServiceMock->expects($this->exactly(1))
            ->method('getUsersLottery')
            ->willReturn($this->users);
        $this->container->set(UserServiceInterface::class, $userServiceMock);

        $uri = self::ROUTE_API_USERS_LOTTERY2;
        if ($version == '2') {
            $uri .= '?page=' . $page;
        }
        $request = Request::create($uri, Request::METHOD_GET, [], [], [], $this->getHeaders());
        $response = $this->dispatch($request);
        $expected = '{"users_lottery":[{"id":1,"uid":"01HMPW5A2XAG0FHSYQ04R9MFDH","email":"resident-1@lottery.com","fullName":"Rory Ryan III","dateBirthday":{"date":"2024-01-27 20:09:08.587135","timezone_type":3,"timezone":"Europe\/Madrid"},"gender":1,"slug":"KARLI.KOVACEK1-01HMPW5A2XAG0FHSYQ04R9MFDH","commentsCount":0}]}';
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertIsString($expected, $content);
        $this->assertJsonStringEqualsJsonString($expected, $content);
        $this->assertJson($content);
    }

    /**
     * @return iterable
     */
    public static function provideApiLottery(): iterable
    {
        $version = '1';
        $page = 0;
        yield $version => [$version, $page];

        $version = '2';
        $page = 2;
        yield $version => [$version, $page];
    }
}
