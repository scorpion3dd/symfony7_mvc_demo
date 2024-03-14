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

use App\Helper\UriInterface;
use App\Helper\UriHelper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class UriHelperTest - Unit tests for helper UriHelper
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute with Mock
 *
 * @package App\Tests\Unit\Helper
 */
class UriHelperTest extends TestCase
{
    /** @var UriHelper $uriHelper */
    public UriHelper $uriHelper;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->uriHelper = new UriHelper();
    }

    /**
     * @testCase - method setInit - must be a success
     *
     * @dataProvider provideUri
     *
     * @param string $uri
     *
     * @return void
     */
    public function testSetInit(string $uri): void
    {
        $this->uriHelper->setInit($uri);
        $this->assertTrue(true);
    }

    /**
     * @return iterable
     */
    public static function provideUri(): iterable
    {
        yield '1' => ['/en/admin?routeName=logs'];
        yield '2' => ['/admin'];
        yield '3' => ['//postgres:postgres@tools.ietf.org:5432/'];
    }

    /**
     * @return void
     */
    public function testSetInitException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to parse URI: scheme://example_login:!#Password?@ZZZ@127.0.0.1/some_path');
        $this->expectExceptionCode(0);
        $this->uriHelper->setInit('scheme://example_login:!#Password?@ZZZ@127.0.0.1/some_path');
    }

    /**
     * @testCase - method getInit - must be a success
     *
     * @return void
     */
    public function testGetInit(): void
    {
        $object = $this->uriHelper->getInit('/admin');
        $this->assertInstanceOf(UriHelper::class, $object);
    }

    /**
     * @testCase - method __toString - must be a success
     *
     * @return void
     */
    public function testToString(): void
    {
        $uri = 'http://tools.ietf.org/html/rfc3986#section';
        $uriHelper = new UriHelper($uri);
        $objectString = (string)$uriHelper;
        $this->assertStringStartsWith($uri, $objectString);
    }

    /**
     * @testCase - method removeDotSegments - must be a success
     *
     * @dataProvider provideRemoveDotSegments
     *
     * @param string $expectedPath
     * @param string $path
     *
     * @return void
     */
    public function testRemoveDotSegments(string $expectedPath, string $path): void
    {
        $newPath = $this->uriHelper->removeDotSegments($path);
        $this->assertStringStartsWith($expectedPath, $newPath);
    }

    /**
     * @return iterable
     */
    public static function provideRemoveDotSegments(): iterable
    {
        $path = 'http://tools.ietf.org/html/rfc3986#section';
        yield '1' => [$path, $path];

        $path = '*';
        yield '2' => [$path, $path];

        $path = 'http://tools.ietf.org/../html/rfc3986#section';
        yield '3' => ['http://html/rfc3986#section', $path];

        $path = '/../html/rfc3986#section';
        yield '4' => ['/html/rfc3986#section', $path];

        $path = 'html/rfc3986#section/.';
        yield '5' => ['html/rfc3986#section', $path];
    }

    /**
     * @testCase - method resolve - must be a success
     *
     * @dataProvider provideResolve
     *
     * @param UriInterface $base
     * @param string|UriInterface|null $rel
     *
     * @return void
     */
    public function testResolve(UriInterface $base, string|UriInterface|null $rel): void
    {
        $newBase = $this->uriHelper->resolve($base, $rel);
        $this->assertInstanceOf(UriInterface::class, $newBase);
    }

    /**
     * @return iterable
     */
    public static function provideResolve(): iterable
    {
        $base = new UriHelper();
        $rel = 'http://tools.ietf.org/html/rfc3986#section';
        yield '1' => [$base, $rel];

        $rel = '';
        yield '2' => [$base, $rel];

        $rel = null;
        yield '3' => [$base, $rel];

        $rel = new UriHelper();
        yield '4' => [$base, $rel];

        $rel = new UriHelper('//postgres:postgres@tools.ietf.org:5432/');
        yield '5' => [$base, $rel];

        $rel = new UriHelper('/html/rfc/rfc3986#section');
        yield '6' => [$base, $rel];

        $base = new UriHelper('//postgres:postgres@tools.ietf.org:5432/');
        $rel = new UriHelper('html/rfc/rfc3986#section');
        yield '7' => [$base, $rel];

        $base = new UriHelper('//postgres:postgres@tools.ietf.org:5432');
        $rel = new UriHelper('html/rfc/rfc3986#section');
        yield '8' => [$base, $rel];

        $rel = '?uid=154862&id=123';
        yield '9' => [$base, $rel];

        $rel = '#uid154862';
        yield '10' => [$base, $rel];

        $base = new UriHelper('//postgres:postgres@tools.ietf.org');
        $rel = 'html/rfc/rfc3986#section';
        yield '11' => [$base, $rel];
    }

    /**
     * @testCase - method withoutQueryValue - must be a success
     *
     * @dataProvider provideWithoutQueryValue
     *
     * @param UriInterface $expected
     * @param UriInterface $uri
     * @param string $key
     *
     * @return void
     */
    public function testWithoutQueryValue(UriInterface $expected, UriInterface $uri, string $key): void
    {
        $newUri = $this->uriHelper->withoutQueryValue($uri, $key);
        $this->assertInstanceOf(UriInterface::class, $newUri);
        $this->assertInstanceOf(UriInterface::class, $expected);
    }

    /**
     * @return iterable
     */
    public static function provideWithoutQueryValue(): iterable
    {
        $expected = new UriHelper();
        $uri = new UriHelper();
        $key = '';
        yield '1' => [$expected, $uri, $key];

        $uri = new UriHelper('?uid=154862&id=123');
        yield '2' => [$expected, $uri, $key];
    }

    /**
     * @testCase - method withQueryValue - must be a success
     *
     * @dataProvider provideWithQueryValue
     *
     * @param UriInterface $expected
     * @param UriInterface $uri
     * @param string $key
     * @param string|null $value
     *
     * @return void
     */
    public function testWithQueryValue(UriInterface $expected, UriInterface $uri, string $key, ?string $value): void
    {
        $newUri = $this->uriHelper->withQueryValue($uri, $key, $value);
        $this->assertInstanceOf(UriInterface::class, $newUri);
        $this->assertInstanceOf(UriInterface::class, $expected);
    }

    /**
     * @return iterable
     */
    public static function provideWithQueryValue(): iterable
    {
        $expected = new UriHelper();
        $uri = new UriHelper();
        $key = '';
        $value = '';
        yield '1' => [$expected, $uri, $key, $value];

        $uri = new UriHelper('?uid=154862&id=123');
        yield '2' => [$expected, $uri, $key, $value];

        $uri = new UriHelper('?uid=154862&id=123');
        $value = null;
        yield '3' => [$expected, $uri, $key, $value];
    }

    /**
     * @testCase - method fromParts - must be a success
     *
     * @return void
     */
    public function testFromParts(): void
    {
        $parts = [];
        $uri = $this->uriHelper->fromParts($parts);
        $this->assertInstanceOf(UriInterface::class, $uri);
    }

    /**
     * @testCase - method getUserInfo - must be a success
     *
     * @return void
     */
    public function testGetUserInfo(): void
    {
        $userInfo = $this->uriHelper->getUserInfo();
        $this->assertIsString($userInfo);
    }

    /**
     * @testCase - method getHost - must be a success
     *
     * @return void
     */
    public function testGetHost(): void
    {
        $host = $this->uriHelper->getHost();
        $this->assertIsString($host);
    }

    /**
     * @testCase - method getPort - must be a success
     *
     * @return void
     */
    public function testGetPort(): void
    {
        $port = $this->uriHelper->getPort();
        $this->assertEmpty($port);
    }

    /**
     * @testCase - method withScheme - must be a success
     *
     * @dataProvider provideWithScheme
     *
     * @param UriInterface $uri
     * @param string $scheme
     *
     * @return void
     */
    public function testWithScheme(UriInterface $uri, string $scheme): void
    {
        $uriNew = $uri->withScheme($scheme);
        $this->assertInstanceOf(UriInterface::class, $uriNew);
    }

    /**
     * @return iterable
     */
    public static function provideWithScheme(): iterable
    {
        $scheme = 'http';
        $uri = new UriHelper('http://tools.ietf.org:5050');
        yield '1' => [$uri, $scheme];

        $uri = new UriHelper();
        yield '2' => [$uri, $scheme];
    }

    /**
     * @testCase - method withUserInfo - must be a success
     *
     * @dataProvider provideWithUserInfo
     *
     * @param UriInterface $uri
     * @param string $user
     * @param string|null $password
     *
     * @return void
     */
    public function testWithUserInfo(UriInterface $uri, string $user, ?string $password): void
    {
        $uriNew = $uri->withUserInfo($user, $password);
        $this->assertInstanceOf(UriInterface::class, $uriNew);
    }

    /**
     * @return iterable
     */
    public static function provideWithUserInfo(): iterable
    {
        $user = 'user';
        $password = 'password';
        $uri = new UriHelper('//user:password@tools.ietf.org:5432/');
        yield '1' => [$uri, $user, $password];

        $uri = new UriHelper();
        yield '2' => [$uri, $user, $password];
    }

    /**
     * @testCase - method withHost - must be a success
     *
     * @dataProvider provideWithHost
     *
     * @param UriInterface $uri
     * @param string $host
     *
     * @return void
     */
    public function testWithHost(UriInterface $uri, string $host): void
    {
        $uriNew = $uri->withHost($host);
        $this->assertInstanceOf(UriInterface::class, $uriNew);
    }

    /**
     * @return iterable
     */
    public static function provideWithHost(): iterable
    {
        $host = 'tools.ietf.org';
        $uri = new UriHelper('http://tools.ietf.org');
        yield '1' => [$uri, $host];

        $uri = new UriHelper();
        yield '2' => [$uri, $host];
    }

    /**
     * @testCase - method withPort - must be a success
     *
     * @dataProvider provideWithPort
     *
     * @param UriInterface $uri
     * @param int $port
     *
     * @return void
     */
    public function testWithPort(UriInterface $uri, int $port): void
    {
        $uriNew = $uri->withPort($port);
        $this->assertInstanceOf(UriInterface::class, $uriNew);
    }

    /**
     * @return iterable
     */
    public static function provideWithPort(): iterable
    {
        $port = 5050;
        $uri = new UriHelper('http://tools.ietf.org:5050');
        yield '1' => [$uri, $port];

        $uri = new UriHelper();
        yield '2' => [$uri, $port];
    }

    /**
     * @testCase - method withPort - must be a InvalidArgumentException
     *
     * @return void
     */
    public function testWithPortInvalidArgumentException(): void
    {
        $port = 0;
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid port: 0. Must be between 1 and 65535');
        $this->expectExceptionCode(0);
        $uriNew = $this->uriHelper->withPort($port);
        $this->assertInstanceOf(UriInterface::class, $uriNew);
    }

    /**
     * @testCase - method withPath - must be a success
     *
     * @dataProvider provideWithPath
     *
     * @param UriInterface $uri
     * @param mixed $path
     *
     * @return void
     */
    public function testWithPath(UriInterface $uri, mixed $path): void
    {
        $uriNew = $uri->withPath($path);
        $this->assertInstanceOf(UriInterface::class, $uriNew);
    }

    /**
     * @return iterable
     */
    public static function provideWithPath(): iterable
    {
        $path = '/admin';
        $uri = new UriHelper('http://tools.ietf.org:5050/admin');
        yield '1' => [$uri, $path];

        $uri = new UriHelper();
        yield '2' => [$uri, $path];
    }

    /**
     * @testCase - method withPath - must be a InvalidArgumentException
     *
     * @return void
     */
    public function testWithPathInvalidArgumentException(): void
    {
        $path = 1;
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid path provided; must be a string');
        $this->expectExceptionCode(0);
        $uriNew = $this->uriHelper->withPath($path);
        $this->assertInstanceOf(UriInterface::class, $uriNew);
    }

    /**
     * @testCase - method withQuery - must be a success
     *
     * @dataProvider provideWithQuery
     *
     * @param UriInterface $uri
     * @param mixed $query
     *
     * @return void
     */
    public function testWithQuery(UriInterface $uri, mixed $query): void
    {
        $uriNew = $uri->withQuery($query);
        $this->assertInstanceOf(UriInterface::class, $uriNew);
    }

    /**
     * @return iterable
     */
    public static function provideWithQuery(): iterable
    {
        $query = '?uid=548';
        $uri = new UriHelper('http://tools.ietf.org:5050?uid=548');
        yield '1' => [$uri, $query];

        $uri = new UriHelper();
        yield '2' => [$uri, $query];
    }

    /**
     * @testCase - method withPath - must be a InvalidArgumentException
     * Query string must be a string
     *
     * @return void
     */
    public function testWithQueryInvalidArgumentException(): void
    {
        $query = 1;
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Query string must be a string');
        $this->expectExceptionCode(0);
        $uriNew = $this->uriHelper->withQuery($query);
        $this->assertInstanceOf(UriInterface::class, $uriNew);
    }

    /**
     * @testCase - method withPath - must be a InvalidArgumentException
     * Query string must be a class and exists method __toString
     *
     * @return void
     */
    public function testWithQueryInvalidArgumentException2(): void
    {
        $query = 'StdClass';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Query string must be a class and exists method __toString');
        $this->expectExceptionCode(0);
        $uriNew = $this->uriHelper->withQuery($query);
        $this->assertInstanceOf(UriInterface::class, $uriNew);
    }

    /**
     * @testCase - method withFragment - must be a success
     *
     * @dataProvider provideWithFragment
     *
     * @param UriInterface $uri
     * @param string $fragment
     *
     * @return void
     */
    public function testWithFragment(UriInterface $uri, string $fragment): void
    {
        $uriNew = $uri->withFragment($fragment);
        $this->assertInstanceOf(UriInterface::class, $uriNew);
    }

    /**
     * @return iterable
     */
    public static function provideWithFragment(): iterable
    {
        $fragment = '#uid154862';
        $uri = new UriHelper('http://tools.ietf.org:5050#uid154862');
        yield '1' => [$uri, $fragment];

        $uri = new UriHelper();
        yield '2' => [$uri, $fragment];
    }

    /**
     * @testCase - method isApiAdminsNumeric - must be a success
     *
     * @dataProvider provideIsApiAdminsNumeric
     *
     * @param bool $expected
     * @param string $requestUri
     *
     * @return void
     */
    public function testIsApiAdminsNumeric(bool $expected, string $requestUri): void
    {
        $bool = $this->uriHelper->isApiAdminsNumeric($requestUri);
        $this->assertEquals($expected, $bool);
    }

    /**
     * @return iterable
     */
    public static function provideIsApiAdminsNumeric(): iterable
    {
        $requestUri = '/api/admins/2';
        $expected = true;
        yield '1' => [$expected, $requestUri];

        $requestUri = 'admins/2';
        $expected = false;
        yield '2' => [$expected, $requestUri];
    }

    /**
     * @testCase - method isApiAdminsItem - must be a success
     *
     * @dataProvider provideIsApiAdminsItem
     *
     * @param bool $expected
     * @param string $requestUri
     * @param string $item
     *
     * @return void
     */
    public function testIsApiAdminsItem(bool $expected, string $requestUri, string $item): void
    {
        $bool = $this->uriHelper->isApiAdminsItem($requestUri, $item);
        $this->assertEquals($expected, $bool);
    }

    /**
     * @return iterable
     */
    public static function provideIsApiAdminsItem(): iterable
    {
        $requestUri = '/api/admins/2/item';
        $item = 'item';
        $expected = true;
        yield '1' => [$expected, $requestUri, $item];

        $requestUri = 'admins/2';
        $item = 'item';
        $expected = false;
        yield '2' => [$expected, $requestUri, $item];
    }

    /**
     * @testCase - method isApiAdminsItemProvide - must be a success
     *
     * @dataProvider provideIsApiAdminsItemProvide
     *
     * @param bool $expected
     * @param string $requestUri
     *
     * @return void
     */
    public function testIsApiAdminsItemProvide(bool $expected, string $requestUri): void
    {
        $bool = $this->uriHelper->isApiAdminsItemProvide($requestUri);
        $this->assertEquals($expected, $bool);
    }

    /**
     * @return iterable
     */
    public static function provideIsApiAdminsItemProvide(): iterable
    {
        $requestUri = '/admins/{id}{._format}';
        $expected = true;
        yield '1' => [$expected, $requestUri];

        $requestUri = 'admins/2';
        $expected = false;
        yield '2' => [$expected, $requestUri];
    }

    /**
     * @testCase - method isApiAdminsListProvide - must be a success
     *
     * @dataProvider provideIsApiAdminsListProvide
     *
     * @param bool $expected
     * @param string $requestUri
     * @param string $list
     *
     * @return void
     */
    public function testIsApiAdminsListProvide(bool $expected, string $requestUri, string $list): void
    {
        $bool = $this->uriHelper->isApiAdminsListProvide($requestUri, $list);
        $this->assertEquals($expected, $bool);
    }

    /**
     * @return iterable
     */
    public static function provideIsApiAdminsListProvide(): iterable
    {
        $requestUri = '/admins/list1';
        $list = 'list1';
        $expected = true;
        yield '1' => [$expected, $requestUri, $list];

        $requestUri = 'admins/2';
        $list = 'item';
        $expected = false;
        yield '2' => [$expected, $requestUri, $list];
    }

    /**
     * @testCase - method isApiAdminsList - must be a success
     *
     * @dataProvider provideIsApiAdminsList
     *
     * @param bool $expected
     * @param string $requestUri
     * @param string $list
     *
     * @return void
     */
    public function testIsApiAdminsList(bool $expected, string $requestUri, string $list): void
    {
        $bool = $this->uriHelper->isApiAdminsList($requestUri, $list);
        $this->assertEquals($expected, $bool);
    }

    /**
     * @return iterable
     */
    public static function provideIsApiAdminsList(): iterable
    {
        $requestUri = '/api/admins/list1';
        $list = 'list1';
        $expected = true;
        yield '1' => [$expected, $requestUri, $list];

        $requestUri = 'admins/2';
        $list = 'item';
        $expected = false;
        yield '2' => [$expected, $requestUri, $list];
    }

    /**
     * @testCase - method isApiAdmins - must be a success
     *
     * @dataProvider provideIsApiAdmins
     *
     * @param bool $expected
     * @param string $requestUri
     *
     * @return void
     */
    public function testIsApiAdmins(bool $expected, string $requestUri): void
    {
        $bool = $this->uriHelper->isApiAdmins($requestUri);
        $this->assertEquals($expected, $bool);
    }

    /**
     * @return iterable
     */
    public static function provideIsApiAdmins(): iterable
    {
        $requestUri = '/api/admins';
        $expected = true;
        yield '1' => [$expected, $requestUri];

        $requestUri = 'admins/2';
        $expected = false;
        yield '2' => [$expected, $requestUri];
    }

    /**
     * @testCase - method isApiCommentsItem - must be a success
     *
     * @dataProvider provideIsApiCommentsItem
     *
     * @param bool $expected
     * @param string $requestUri
     *
     * @return void
     */
    public function testIsApiCommentsItem(bool $expected, string $requestUri): void
    {
        $bool = $this->uriHelper->isApiCommentsItem($requestUri);
        $this->assertEquals($expected, $bool);
    }

    /**
     * @return iterable
     */
    public static function provideIsApiCommentsItem(): iterable
    {
        $requestUri = '/api/comments?userId=';
        $expected = true;
        yield '1' => [$expected, $requestUri];

        $requestUri = 'comments/2';
        $expected = false;
        yield '2' => [$expected, $requestUri];
    }

    /**
     * @testCase - method isApiCommentsUpload - must be a success
     *
     * @dataProvider provideIsApiCommentsUpload
     *
     * @param bool $expected
     * @param string $requestUri
     *
     * @return void
     */
    public function testIsApiCommentsUpload(bool $expected, string $requestUri): void
    {
        $bool = $this->uriHelper->isApiCommentsUpload($requestUri);
        $this->assertEquals($expected, $bool);
    }

    /**
     * @return iterable
     */
    public static function provideIsApiCommentsUpload(): iterable
    {
        $requestUri = '/api/comments/upload';
        $expected = true;
        yield '1' => [$expected, $requestUri];

        $requestUri = 'comments/2';
        $expected = false;
        yield '2' => [$expected, $requestUri];
    }

    /**
     * @testCase - method urlRedirectNew - must be a success
     *
     * @return void
     */
    public function testUrlRedirectNew(): void
    {
        $expected = 'http://tools.ietf.org/comments/upload/photo';
        $uriHelper = new UriHelper('http://tools.ietf.org/api/comments/upload/photo');
        $urlR = $uriHelper->urlRedirectNew($uriHelper);
        $this->assertIsString($urlR);
        $this->assertEquals($expected, $urlR);
    }
}
