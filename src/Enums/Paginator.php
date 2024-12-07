<?php

declare(strict_types=1);

namespace Honed\Table\Enums;

use Honed\Table\Table;

enum Paginator: string
{
    case None = 'none';
    case Simple = 'simple';
    case Cursor = 'cursor';

    /**
     * Retrieve the records and metadata based on the selected paginator.
     *
     * @return array{\Illuminate\Support\Collection, array<string, int|string|bool>}
     */
    public function paginate(Table $table): array
    {
        $builder = $table->getResource();

        return match ($this) {
            self::Cursor => [
                ($data = $builder->cursorPaginate(
                    perPage: $table->getPageCount(),
                    cursorName: $table->getPageAs(),
                )->withQueryString())->getCollection(),
                self::getMeta($data),
            ],
            self::None => [
                $data = $builder->get(),
                self::getMeta($data),
            ],
            default => [
                ($data = $builder->paginate(
                    perPage: $table->getPageCount(),
                    pageName: $table->getPageAs(),
                ))->getCollection(),
                self::getMeta($data),
            ],
        };
    }

    /**
     * Get metadata based on the current pagination type.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Pagination\CursorPaginator|\Illuminate\Pagination\LengthAwarePaginator  $data
     * @return array<string, int|string|bool>
     */
    public function getMeta($data): array
    {
        return match ($this) {
            self::None => [
                'empty' => $data->isEmpty(),
                'show' => $data->isNotEmpty(),
            ],
            self::Cursor => [
                'per_page' => $data->perPage(),
                'next_cursor' => $data->nextCursor()?->encode(),
                'prev_cusor' => $data->previousCursor()?->encode(),
                'next_url' => $data->nextPageUrl(),
                'prev_url' => $data->previousPageUrl(),
                'show' => $data->hasPages(),
                'empty' => $data->isEmpty(),
            ],
            default => [
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem() ?? 0,
                'to' => $data->lastItem() ?? 0,
                'total' => $data->total(),
                'links' => $data->onEachSide(1)->linkCollection()->splice(1, -1)->values()->toArray(),
                'first_url' => $data->url(1),
                'last_url' => $data->url($data->lastPage()),
                'next_url' => $data->nextPageUrl(),
                'prev_url' => $data->previousPageUrl(),
                'show' => $data->hasPages(),
                'empty' => $data->isEmpty(),
            ],
        };
    }
}
