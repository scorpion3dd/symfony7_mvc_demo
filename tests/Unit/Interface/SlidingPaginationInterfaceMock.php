<?php

namespace App\Tests\Unit\Interface;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @template TKey
 * @template TValue
 *
 * @template-extends PaginationInterface<TKey, TValue>
 */
interface SlidingPaginationInterfaceMock extends SlidingPaginationInterface
{
    public function setTemplate(string $template): void;

    public function setPageRange(int $range): void;

    public function getRoute(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function getParams(): array;

    /**
     * @param string[]|string|null $key
     * @param array<string, mixed> $params
     */
    public function isSorted(array|string|null $key = null, array $params = []): bool;

    /**
     * @return array<string, mixed>
     */
    public function getPaginationData(): array;

    /**
     * @return array<string, mixed>|null
     */
    public function getPaginatorOptions(): ?array;

    /**
     * @return array<string, mixed>|null
     */
    public function getCustomParameters(): ?array;
}
