<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Support\Arr;
use Honed\Action\InlineAction;
use Honed\Table\Page;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

trait HasRecords
{
    use HasPagination;
    use HasPaginator;

    /**
     * @var array<int,\Honed\Table\Page>
     */
    protected $pages = [];

    /**
     * The records of the table retrieved from the resource.
     * 
     * @var array<int,mixed>|null
     */
    protected $records = null;

    /**
     * @var array<string,mixed>
     */
    protected $meta = [];

    /**
     * Get the records of the table.
     *
     * @return array<int,mixed>|null
     */
    public function getRecords(): ?array
    {
        return $this->records;
    }

    /**
     * Get the meta data of the table.
     * 
     * @return array<string,mixed>
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Determine if the table has records.
     */
    public function hasRecords(): bool
    {
        return ! \is_null($this->records);
    }

    /**
     * Get the page options of the table.
     * 
     * @return array<int,\Honed\Table\Page>
     */
    public function getPages(): array
    {
        return $this->pages;
    }
    
    /**
     * Format the records using the provided columns.
     * 
     * @param array<int,\Honed\Table\Columns\Column> $activeColumns
     */
    public function formatAndPaginate(array $activeColumns): void
    {
        if ($this->hasRecords()) {
            return;
        }

        /**
         * @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
         */
        $builder = $this->getBuilder();

        $paginator = $this->getPaginator();

        /**
         * @var array<int,\Illuminate\Database\Eloquent\Model> $records
         */
        [$records, $this->meta] = match (true) {
            $this->isLengthAware($paginator) => $this->paginateRecords($builder),

            $this->isSimple($paginator) => $this->simplePaginateRecords($builder),

            $this->isCursor($paginator) => $this->cursorPaginateRecords($builder),

            $this->isCollection($paginator) => $this->collectRecords($builder),
            
            default => static::throwInvalidPaginatorException($paginator),
        };

        $formattedRecords = [];

        foreach ($records as $record) {
            $formattedRecords[] = $this->formatRecord($record, $activeColumns);
        }
        
        $this->records = $formattedRecords;
    }

    /**
     * Get the number of records to show per page.
     */
    protected function getRecordsPerPage(): int
    {
        $pagination = $this->getPagination();
        
        if (! \is_array($pagination)) {
            return $pagination;
        }

        $perPage = $this->getRecordsFromRequest();

        $perPage = \in_array($perPage, type($this->getPagination())->asArray())
            ? $perPage
            : $this->getDefaultPagination();

        $this->pages = Arr::map($pagination, 
            static fn (int $amount) => Page::make($amount, $perPage)
        );

        return $perPage;
    }

    /**
     * Get the number of records to show per page from the request.
     */
    protected function getRecordsFromRequest(): int
    {
        /**
         * @var \Illuminate\Http\Request
         */
        $request = $this->getRequest();

        return $request->integer(
            $this->getRecordsKey(),
            $this->getDefaultPagination(),
        );
    }


    /**
     * Format a record using the provided columns.
     * 
     * @param \Illuminate\Database\Eloquent\Model $record
     * @param array<int,\Honed\Table\Columns\Column> $columns
     * 
     * @return array<string,mixed>
     */
    protected function formatRecord(Model $record, array $columns): array
    {
        $reducing = false;

        $actions = Arr::map(
            Arr::where(
                $this->inlineActions(),
                fn (InlineAction $action) => $action->isAllowed($record)
            ),
            fn (InlineAction $action) => $action->resolve()->toArray(),
        );

        $key = $record->{$this->getKeyname()};

        $formatted = ($reducing) ? [] : $record->toArray(); // @phpstan-ignore-line

        foreach ($columns as $column) {
            Arr::set($formatted, $column->getName(), $column->format($record));
        }

        Arr::set($formatted, 'actions', $actions);
        Arr::set($formatted, 'key', $key);

        return $formatted;
    }

    /**
     * Determine if the paginator is a length-aware paginator.
     */
    protected function isLengthAware(string $paginator): bool
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
    protected function isSimple(string $paginator): bool
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
    protected function isCursor(string $paginator): bool
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
    protected function isCollection(string $paginator): bool
    {
        return \in_array($paginator, [
            'none',
            'collection',
            Collection::class,
        ]);
    }

    /**
     * Length-aware paginate the records from the builder.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $builder
     * 
     * @return array{0:array<int,mixed>,1:array<string,mixed>}
     */
    protected function paginateRecords(Builder $builder): array
    {
        /**
         * @var \Illuminate\Pagination\LengthAwarePaginator<\Illuminate\Database\Eloquent\Model> $paginated
         */
        $paginated = $builder->paginate(
            perPage: $this->getRecordsPerPage(),
            pageName: $this->getPageKey(),
        );

        $paginated->withQueryString();

        return [
            $paginated->items(),
            $this->lengthAwarePaginatorMetadata($paginated),
        ];
    }

    /**
     * Simple paginate the records from the builder.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $builder
     * 
     * @return array{0:array<int,mixed>,1:array<string,mixed>}
     */
    protected function simplePaginateRecords(Builder $builder): array
    {
        /**
         * @var \Illuminate\Pagination\Paginator<\Illuminate\Database\Eloquent\Model> $paginated
         */
        $paginated = $builder->simplePaginate(
            perPage: $this->getRecordsPerPage(),
            pageName: $this->getPageKey(),
        );

        $paginated->withQueryString();

        return [
            $paginated->items(),
            $this->paginatorMetadata($paginated),
        ];
    }

    /**
     * Cursor paginate the records from the builder.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $builder
     * 
     * @return array{0:array<int,mixed>,1:array<string,mixed>}
     */
    protected function cursorPaginateRecords(Builder $builder): array
    {
        /**
         * @var \Illuminate\Pagination\CursorPaginator<\Illuminate\Database\Eloquent\Model> $paginated
         */
        $paginated = $builder->cursorPaginate(
            perPage: $this->getRecordsPerPage(),
            cursorName: $this->getPageKey(),
        );

        $paginated->withQueryString();

        return [
            $paginated->items(),
            $this->cursorPaginatorMetadata($paginated),
        ];
    }

    /**
     * Collect the records from the builder.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $builder
     * 
     * @return array{0:array<int,mixed>,1:array<string,mixed>}
     */
    protected function collectRecords(Builder $builder): array
    {
        $metadata = [];

        return [
            $builder->get(),
            $metadata,
        ];
    }

    /**
     * Get the metadata for the length-aware paginator.
     * 
     * @param \Illuminate\Pagination\LengthAwarePaginator<\Illuminate\Database\Eloquent\Model> $paginator
     * 
     * @return array<string,mixed>
     */
    protected function lengthAwarePaginatorMetadata(LengthAwarePaginator $paginator): array
    {
        return \array_merge($this->paginatorMetadata($paginator), [
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
     * @param \Illuminate\Contracts\Pagination\Paginator<\Illuminate\Database\Eloquent\Model> $paginator
     * 
     * @return array<string,mixed>
     */
    protected function paginatorMetadata(Paginator $paginator): array
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
     * @param \Illuminate\Pagination\CursorPaginator<\Illuminate\Database\Eloquent\Model> $paginator
     * 
     * @return array<string,mixed>
     */
    protected function cursorPaginatorMetadata(CursorPaginator $paginator): array
    {
        return [
            'prev' => $paginator->previousPageUrl(),
            'next' => $paginator->nextPageUrl(),
            'per_page' => $paginator->perPage(),
        ];
    }

    /**
     * Throw an exception for an invalid paginator type.
     */
    protected static function throwInvalidPaginatorException(string $paginator): never
    {
        throw new \InvalidArgumentException(
            \sprintf('The paginator [%s] is not valid.', $paginator
        ));
    }
    
}
