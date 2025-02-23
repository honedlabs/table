<?php

declare(strict_types=1);

namespace Honed\Table\Concerns\Support;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

trait HasPaginator
{
    /**
     * The paginator to use for the table.
     *
     * @var 'cursor'|'simple'|'length-aware'|'none'|string|null
     */
    protected $paginator;

    /**
     * Retrieve the default paginator for the table.
     *
     * @return 'cursor'|'simple'|'length-aware'|'none'|string
     */
    public function getPaginator(): string
    {
        if (isset($this->paginator)) {
            return $this->paginator;
        }

        return type(config('table.paginator', 'length-aware'))->asString();
    }

    /**
     * Determine if the paginator is a length-aware paginator.
     */
    protected static function isLengthAware(string $paginator): bool
    {
        return \in_array($paginator, [
            'length-aware',
            \Illuminate\Contracts\Pagination\LengthAwarePaginator::class,
            \Illuminate\Pagination\LengthAwarePaginator::class,
        ]);
    }

    /**
     * Determine if the paginator is a simple paginator.
     */
    protected static function isSimple(string $paginator): bool
    {
        return \in_array($paginator, [
            'simple',
            \Illuminate\Contracts\Pagination\Paginator::class,
            \Illuminate\Pagination\Paginator::class,
        ]);
    }

    /**
     * Determine if the paginator is a cursor paginator.
     */
    protected static function isCursor(string $paginator): bool
    {
        return \in_array($paginator, [
            'cursor',
            \Illuminate\Contracts\Pagination\CursorPaginator::class,
            \Illuminate\Pagination\CursorPaginator::class,
        ]);
    }

    /**
     * Determine if the paginator is a collection, indicating no
     * pagination is to be applied.
     */
    protected static function isCollection(string $paginator): bool
    {
        return \in_array($paginator, [
            'none',
            'collection',
            \Illuminate\Support\Collection::class,
        ]);
    }

    /**
     * Get the metadata for the length-aware paginator.
     *
     * @param  \Illuminate\Pagination\LengthAwarePaginator<\Illuminate\Database\Eloquent\Model>  $paginator
     * @return array<string,mixed>
     */
    protected static function lengthAwarePaginatorMetadata(LengthAwarePaginator $paginator): array
    {
        return \array_merge(static::simplePaginatorMetadata($paginator), [
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'first' => $paginator->url(1),
            'last' => $paginator->url($paginator->lastPage()),
            'links' => $paginator->linkCollection()->slice(1, -1)->toArray(),
        ]);
    }

    /**
     * Get the metadata for the simple paginator.
     *
     * @param  \Illuminate\Contracts\Pagination\Paginator<\Illuminate\Database\Eloquent\Model>  $paginator
     * @return array<string,mixed>
     */
    protected static function simplePaginatorMetadata(Paginator $paginator): array
    {
        return [
            'prev' => $paginator->previousPageUrl(),
            'current' => $paginator->currentPage(),
            'next' => $paginator->nextPageUrl(),
            'per_page' => $paginator->perPage(),
        ];
    }

    /**
     * Get the metadata for the cursor paginator.
     *
     * @param  \Illuminate\Pagination\CursorPaginator<\Illuminate\Database\Eloquent\Model>  $paginator
     * @return array<string,mixed>
     */
    protected static function cursorPaginatorMetadata(CursorPaginator $paginator): array
    {
        return [
            'prev' => $paginator->previousPageUrl(),
            'next' => $paginator->nextPageUrl(),
            'per_page' => $paginator->perPage(),
        ];
    }
}
