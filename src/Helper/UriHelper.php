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

use InvalidArgumentException;

/**
 * Class UriHelper - Basic PSR-7 URI implementation.
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @link https://github.com/phly/http
 *   This class is based upon Matthew Weier O'Phinney's URI implementation in phly/http.
 * @package App\Helper
 */
class UriHelper implements UriInterface
{
    private static array $schemes = ['http' => 80, 'https' => 443];
    private static string $charUnreserved = 'a-zA-Z0-9_\\-\\.~';
    private static string $charSubDelims = '!\\$&\'\\(\\)\\*\\+,;=';
    private static array $replaceQuery = ['=' => '%3D', '&' => '%26'];
    /** @var string Uri scheme. */
    private string $scheme = '';
    /** @var string Uri user info. */
    private string $userInfo = '';
    /** @var string Uri host. */
    private string $host = '';
    /** @var int|null Uri port. */
    private ?int $port = null;
    /** @var string Uri path. */
    private string $path = '';
    /** @var string Uri query string. */
    private string $query = '';
    /** @var string Uri fragment. */
    private string $fragment = '';

    /**
     * @param string $uri URI to parse and wrap.
     */
    public function __construct(string $uri = '')
    {
        $this->setInit($uri);
    }

    /**
     * @param string $uri
     *
     * @return void
     */
    public function setInit(string $uri = ''): void
    {
        if ($uri != null) {
            $parts = parse_url($uri);
            if ($parts === false) {
                throw new InvalidArgumentException("Unable to parse URI: {$uri}");
            }
            $this->applyParts($parts);
        }
    }

    /**
     * @param string $uri
     *
     * @return $this
     */
    public function getInit(string $uri = ''): self
    {
        $new = clone $this;
        $new->setInit($uri);

        return $new;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return self::createUriString($this->scheme, $this->getAuthority(), $this->getPath(), $this->query, $this->fragment);
    }

    /**
     * Removes dot segments from a path and returns the new path.
     *
     * @param string $path
     *
     * @return string
     * @link http://tools.ietf.org/html/rfc3986#section-5.2.4
     */
    public static function removeDotSegments(string $path): string
    {
        static $noopPaths = ['' => true, '/' => true, '*' => true];
        static $ignoreSegments = ['.' => true, '..' => true];
        if (isset($noopPaths[$path])) {
            return $path;
        }
        $results = [];
        $segments = explode('/', $path);
        foreach ($segments as $segment) {
            if ($segment == '..') {
                array_pop($results);
            } elseif (! isset($ignoreSegments[$segment])) {
                $results[] = $segment;
            }
        }
        $newPath = implode('/', $results);
        // Add the leading slash if necessary
        if (substr($path, 0, 1) === '/' && substr($newPath, 0, 1) !== '/') {
            $newPath = '/' . $newPath;
        }
        // Add the trailing slash if necessary
        if ($newPath != '/' && isset($ignoreSegments[end($segments)])) {
            $newPath .= '/';
        }

        return $newPath;
    }

    /**
     * Resolve a base URI with a relative URI and return a new URI.
     *
     * @param UriInterface $base Base URI
     * @param string|UriInterface|null $rel Relative URI
     *
     * @return UriInterface
     */
    public static function resolve(UriInterface $base, string|UriInterface|null $rel): UriInterface
    {
        if ($rel === null || $rel === '') {
            return $base;
        }
        if (! $rel instanceof UriInterface) {
            $rel = new self($rel);
        }
        // Return the relative uri as-is if it has a scheme.
        if ($rel->getScheme()) {
            return $rel->withPath(static::removeDotSegments($rel->getPath()));
        }
        $relParts = ['scheme' => $rel->getScheme(), 'authority' => $rel->getAuthority(), 'path' => $rel->getPath(), 'query' => $rel->getQuery(), 'fragment' => $rel->getFragment()];
        $parts = ['scheme' => $base->getScheme(), 'authority' => $base->getAuthority(), 'path' => $base->getPath(), 'query' => $base->getQuery(), 'fragment' => $base->getFragment()];
        if (! empty($relParts['authority'])) {
            $parts['authority'] = $relParts['authority'];
            $parts['path'] = self::removeDotSegments($relParts['path']);
            $parts['query'] = $relParts['query'];
            $parts['fragment'] = $relParts['fragment'];
        } elseif (! empty($relParts['path'])) {
            if (substr($relParts['path'], 0, 1) == '/') {
                $parts['path'] = self::removeDotSegments($relParts['path']);
                $parts['query'] = $relParts['query'];
                $parts['fragment'] = $relParts['fragment'];
            } else {
                if (! empty($parts['authority']) && empty($parts['path'])) {
                    $mergedPath = '/';
                } else {
                    $mergedPath = substr($parts['path'], 0, (strrpos($parts['path'], '/') ?: 0) + 1);
                }
                $parts['path'] = self::removeDotSegments($mergedPath . $relParts['path']);
                $parts['query'] = $relParts['query'];
                $parts['fragment'] = $relParts['fragment'];
            }
        } elseif (! empty($relParts['query'])) {
            $parts['query'] = $relParts['query'];
        } elseif ($relParts['fragment'] != null) {
            $parts['fragment'] = $relParts['fragment'];
        }

        return new self(self::createUriString($parts['scheme'], $parts['authority'], $parts['path'], $parts['query'], $parts['fragment']));
    }

    /**
     * Create a new URI with a specific query string value removed.
     *
     * Any existing query string values that exactly match the provided key are
     * removed.
     *
     * Note: this function will convert "=" to "%3D" and "&" to "%26".
     *
     * @param UriInterface $uri URI to use as a base.
     * @param string $key Query string key value pair to remove.
     *
     * @return UriInterface
     */
    public static function withoutQueryValue(UriInterface $uri, string $key): UriInterface
    {
        $current = $uri->getQuery();
        if (! $current) {
            return $uri;
        }
        $result = [];
        foreach (explode('&', $current) as $part) {
            $subParts = explode('=', $part);
            if ($subParts[0] !== $key) {
                $result[] = $part;
            }
        }

        return $uri->withQuery(implode('&', $result));
    }

    /**
     * Create a new URI with a specific query string value.
     *
     * Any existing query string values that exactly match the provided key are
     * removed and replaced with the given key value pair.
     *
     * Note: this function will convert "=" to "%3D" and "&" to "%26".
     *
     * @param UriInterface $uri URI to use as a base.
     * @param string $key Key to set.
     * @param string|null $value Value to set.
     *
     * @return UriInterface
     */
    public static function withQueryValue(UriInterface $uri, string $key, ?string $value): UriInterface
    {
        $current = $uri->getQuery();
        $key = strtr($key, self::$replaceQuery);
        if (! $current) {
            $result = [];
        } else {
            $result = [];
            foreach (explode('&', $current) as $part) {
                $subParts = explode('=', $part);
                if ($subParts[0] !== $key) {
                    $result[] = $part;
                }
            }
        }
        if ($value !== null) {
            $result[] = $key . '=' . strtr($value, self::$replaceQuery);
        } else {
            $result[] = $key;
        }

        return $uri->withQuery(implode('&', $result));
    }

    /**
     * Create a URI from a hash of parse_url parts.
     *
     * @param array $parts
     *
     * @return self
     */
    public static function fromParts(array $parts): self
    {
        $uri = new self();
        $uri->applyParts($parts);

        return $uri;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getAuthority(): string
    {
        if (empty($this->host)) {
            return '';
        }
        $authority = $this->host;
        if (! empty($this->userInfo)) {
            $authority = $this->userInfo . '@' . $authority;
        }
        if ($this->isNonStandardPort($this->scheme, $this->host, $this->port ?? 0)) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * @return string
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path == null ? '' : $this->path;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @param string $scheme
     *
     * @return $this
     */
    public function withScheme($scheme): self
    {
        $scheme = $this->filterScheme($scheme);
        if ($this->scheme === $scheme) {
            return $this;
        }
        $new = clone $this;
        $new->scheme = $scheme;
        $new->port = $new->filterPort($new->scheme, $new->host, $new->port ?? 1);

        return $new;
    }

    /**
     * @param string $user
     * @param string $password
     *
     * @return $this
     */
    public function withUserInfo($user, $password = null): self
    {
        $info = $user;
        if ($password) {
            $info .= ':' . $password;
        }
        if ($this->userInfo === $info) {
            return $this;
        }
        $new = clone $this;
        $new->userInfo = $info;

        return $new;
    }

    /**
     * @param string $host
     *
     * @return $this
     */
    public function withHost($host): self
    {
        if ($this->host === $host) {
            return $this;
        }
        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    /**
     * @param int|null $port
     *
     * @return $this
     */
    public function withPort($port): self
    {
        $port = $this->filterPort($this->scheme, $this->host, $port ?? 1);
        if ($this->port === $port) {
            return $this;
        }
        $new = clone $this;
        $new->port = $port;

        return $new;
    }

    /**
     * @psalm-suppress DocblockTypeContradiction
     * @param string $path
     *
     * @return $this
     */
    public function withPath($path): self
    {
        if (! is_string($path)) {
            throw new InvalidArgumentException('Invalid path provided; must be a string');
        }
        /** @var string $path */
        $path = $this->filterPath($path);
        if ($this->path === $path) {
            return $this;
        }
        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    /**
     * @psalm-suppress DocblockTypeContradiction
     * @param string $query
     *
     * @return $this
     */
    public function withQuery($query): self
    {
        if (! is_string($query)) {
            throw new InvalidArgumentException('Query string must be a string');
        }
        if (class_exists($query) && ! method_exists($query, '__toString')) {
            throw new InvalidArgumentException('Query string must be a class and exists method __toString');
        }
        if (substr($query, 0, 1) === '?') {
            $query = substr($query, 1);
        }
        /** @var string $query */
        $query = $this->filterQueryAndFragment($query);
        if ($this->query === $query) {
            return $this;
        }
        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    /**
     * @param string $fragment
     *
     * @return $this
     */
    public function withFragment($fragment): self
    {
        if (substr($fragment, 0, 1) === '#') {
            $fragment = substr($fragment, 1);
        }
        /** @var string $fragment */
        $fragment = $this->filterQueryAndFragment($fragment);
        if ($this->fragment === $fragment) {
            return $this;
        }
        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    /**
     * Apply parse_url parts to a URI.
     * @param array $parts - Array of parse_url parts to apply.
     *
     * @return void
     */
    private function applyParts(array $parts): void
    {
        $this->scheme = isset($parts['scheme']) ? $this->filterScheme($parts['scheme']) : '';
        $this->userInfo = $parts['user'] ?? '';
        $this->host = $parts['host'] ?? '';
        $this->port = ! empty($parts['port']) ? $this->filterPort($this->scheme, $this->host, $parts['port']) : null;
        $parts['path'] = $parts['path'] ?? '';
        /** @var string $path */
        $path = $this->filterPath($parts['path']);
        /** @phpstan-ignore-next-line */
        $this->path = isset($parts['path']) ? $path : '';
        $parts['query'] = $parts['query'] ?? '';
        /** @var string $query */
        $query = $this->filterQueryAndFragment($parts['query']);
        /** @phpstan-ignore-next-line */
        $this->query = isset($parts['query']) ? $query : '';
        $parts['fragment'] = $parts['fragment'] ?? '';
        /** @var string $fragment */
        $fragment = $this->filterQueryAndFragment($parts['fragment']);
        /** @phpstan-ignore-next-line */
        $this->fragment = isset($parts['fragment']) ? $fragment : '';
        if (isset($parts['pass'])) {
            $this->userInfo .= ':' . $parts['pass'];
        }
    }

    /**
     * Create a URI string from its various parts
     * @param string $scheme
     * @param string $authority
     * @param string $path
     * @param string $query
     * @param string $fragment
     *
     * @return string
     */
    private static function createUriString(string $scheme, string $authority, string $path, string $query, string $fragment): string
    {
        $uri = '';
        if (! empty($scheme)) {
            $uri .= $scheme . '://';
        }
        if (! empty($authority)) {
            $uri .= $authority;
        }
        if ($path != null) {
            // Add a leading slash if necessary.
            if ($uri && substr($path, 0, 1) !== '/') {
                // @codeCoverageIgnoreStart
                $uri .= '/';
                // @codeCoverageIgnoreEnd
            }
            $uri .= $path;
        }
        if ($query != null) {
            $uri .= '?' . $query;
        }
        if ($fragment != null) {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }

    /**
     * Is a given port non-standard for the current scheme?
     * @param string $scheme
     * @param string $host
     * @param int $port
     *
     * @return bool
     */
    private static function isNonStandardPort(string $scheme, string $host, int $port): bool
    {
        if (! $scheme && $port) {
            return true;
        }
        if (! $host || ! $port) {
            return false;
        }

        return ! isset(self::$schemes[$scheme]) || $port !== self::$schemes[$scheme];
    }

    /**
     * @param string $scheme
     *
     * @return string
     */
    private function filterScheme(string $scheme): string
    {
        $scheme = strtolower($scheme);

        return rtrim($scheme, ':/');
    }

    /**
     * @param string $scheme
     * @param string $host
     * @param int $port
     *
     * @return int|null
     * @throws InvalidArgumentException If the port is invalid.
     */
    private function filterPort(string $scheme, string $host, int $port): ?int
    {
        if (1 > $port || 0xffff < $port) {
            throw new InvalidArgumentException(sprintf('Invalid port: %d. Must be between 1 and 65535', $port));
        }

        return $this->isNonStandardPort($scheme, $host, $port) ? $port : null;
    }

    /**
     * Filters the path of a URI
     * @param array|string $path
     *
     * @return array|string|null
     */
    private function filterPath(array|string $path): array|string|null
    {
        return preg_replace_callback(
            '/(?:[^' . self::$charUnreserved . self::$charSubDelims . ':@\\/%]+|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawUrlEncodeMatchZero'],
            $path
        );
    }

    /**
     * Filters the query string or fragment of a URI.
     * @param array|string $str
     *
     * @return array|string|null
     */
    private function filterQueryAndFragment(array|string $str): array|string|null
    {
        return preg_replace_callback(
            '/(?:[^' . self::$charUnreserved . self::$charSubDelims . '%:@\\/\\?]+|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawUrlEncodeMatchZero'],
            $str
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @param array $match
     *
     * @return string
     */
    private function rawUrlEncodeMatchZero(array $match): string
    {
        // @codeCoverageIgnoreStart
        return rawurlencode($match[0]);
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $requestUri
     *
     * @return bool
     */
    public function isApiAdminsNumeric(string $requestUri = ''): bool
    {
        $requestUriArr = explode('/', $requestUri);

        return count($requestUriArr) == 4
            && isset($requestUriArr[0]) && $requestUriArr[0] == ''
            && isset($requestUriArr[1]) && $requestUriArr[1] == 'api'
            && isset($requestUriArr[2]) && $requestUriArr[2] == 'admins'
            && isset($requestUriArr[3]) && is_numeric($requestUriArr[3]);
    }

    /**
     * @param string $requestUri
     * @param string $item
     *
     * @return bool
     */
    public function isApiAdminsItem(string $requestUri = '', string $item = ''): bool
    {
        $requestUriArr = explode('/', $requestUri);

        return count($requestUriArr) == 5
            && isset($requestUriArr[0]) && $requestUriArr[0] == ''
            && isset($requestUriArr[1]) && $requestUriArr[1] == 'api'
            && isset($requestUriArr[2]) && $requestUriArr[2] == 'admins'
            && isset($requestUriArr[3]) && is_numeric($requestUriArr[3])
            && isset($requestUriArr[4]) && $requestUriArr[4] == $item
            ;
    }

    /**
     * @param string $requestUri
     *
     * @return bool
     */
    public function isApiAdminsItemProvide(string $requestUri = ''): bool
    {
        $requestUriArr = explode('/', $requestUri);

        return count($requestUriArr) == 3
            && isset($requestUriArr[0]) && $requestUriArr[0] == ''
            && isset($requestUriArr[1]) && $requestUriArr[1] == 'admins'
            && isset($requestUriArr[2]) && $requestUriArr[2] == '{id}{._format}'
            ;
    }

    /**
     * @param string $requestUri
     * @param string $list
     *
     * @return bool
     */
    public function isApiAdminsListProvide(string $requestUri = '', string $list = 'list1'): bool
    {
        $requestUriArr = explode('/', $requestUri);

        return count($requestUriArr) == 3
            && isset($requestUriArr[0]) && $requestUriArr[0] == ''
            && isset($requestUriArr[1]) && $requestUriArr[1] == 'admins'
            && isset($requestUriArr[2]) && $requestUriArr[2] == $list
            ;
    }

    /**
     * @param string $requestUri
     * @param string $list
     *
     * @return bool
     */
    public function isApiAdminsList(string $requestUri = '', string $list = 'list1'): bool
    {
        $requestUriArr = explode('/', $requestUri);

        return count($requestUriArr) == 4
            && isset($requestUriArr[0]) && $requestUriArr[0] == ''
            && isset($requestUriArr[1]) && $requestUriArr[1] == 'api'
            && isset($requestUriArr[2]) && $requestUriArr[2] == 'admins'
            && isset($requestUriArr[3]) && $requestUriArr[3] == $list
            ;
    }

    /**
     * @param string $requestUri
     *
     * @return bool
     */
    public function isApiAdmins(string $requestUri = ''): bool
    {
        $requestUriArr = explode('/', $requestUri);

        return count($requestUriArr) == 3
            && isset($requestUriArr[0]) && $requestUriArr[0] == ''
            && isset($requestUriArr[1]) && $requestUriArr[1] == 'api'
            && isset($requestUriArr[2]) && $requestUriArr[2] == 'admins'
            ;
    }

    /**
     * @param string $requestUri
     *
     * @return bool
     */
    public function isApiCommentsItem(string $requestUri = ''): bool
    {
        return false !== strpos($requestUri, '/api/comments?userId=');
    }

    /**
     * @param string $requestUri
     *
     * @return bool
     */
    public function isApiCommentsUpload(string $requestUri = ''): bool
    {
        return false !== strpos($requestUri, '/api/comments/upload');
    }

    /**
     * @param UriHelper $urlR
     * @param string $localePath
     *
     * @return string
     */
    public function urlRedirectNew(self $urlR, string $localePath = ''): string
    {
        $pathR = $urlR->getPath();
        $pathArrR = explode('/', $pathR);
        if (isset($pathArrR[2]) && $pathArrR[2] != '') {
            $localePath .= $pathArrR[2];
        }
        if (isset($pathArrR[3]) && $pathArrR[3] != '') {
            $localePath .= '/' . $pathArrR[3];
        }
        if (isset($pathArrR[4]) && $pathArrR[4] != '') {
            $localePath .= '/' . $pathArrR[4];
        }
        $urlR = $urlR->withPath($localePath);

        return (string) $urlR;
    }
}
