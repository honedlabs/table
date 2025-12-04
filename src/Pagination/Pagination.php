<?php

declare(strict_types=1);

namespace Honed\Table\Pagination;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

readonly class Pagination
{
    /**
     * Create a pagination data instance for the given paginator.
     */
    public static function for(mixed $paginator): PaginationData
    {
        return match (true) {
            $paginator instanceof LengthAwarePaginator => static::forLengthAware($paginator),
            $paginator instanceof Paginator => static::forSimple($paginator),
            $paginator instanceof CursorPaginator => static::forCursor($paginator),
            $paginator instanceof Collection => static::forCollection($paginator),
            default => throw new RuntimeException(
                'Unable to determine the pagination type for the given paginator.'
            ),
        };
    }

    /**
     * Create a pagination data instance for the given length-aware paginator.
     *
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, *>  $paginator
     */
    public static function forLengthAware(LengthAwarePaginator $paginator): LengthAwareData
    {
        return LengthAwareData::make($paginator);
    }

    /**
     * Create a pagination data instance for the given simple paginator.
     *
     * @param  \Illuminate\Contracts\Pagination\Paginator<int, *>  $paginator
     */
    public static function forSimple(Paginator $paginator): SimpleData
    {
        return SimpleData::make($paginator);
    }

    /**
     * Create a pagination data instance for the given cursor paginator.
     *
     * @param  \Illuminate\Contracts\Pagination\CursorPaginator<int, *>  $paginator
     */
    public static function forCursor(CursorPaginator $paginator): CursorData
    {
        return CursorData::make($paginator);
    }

    /**
     * Create a pagination data instance for the given collection.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, *>  $paginator
     */
    public static function forCollection(Collection $paginator): PaginationData
    {
        return PaginationData::make($paginator);
    }
}
