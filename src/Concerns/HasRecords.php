<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Action\Concerns\HasParameterNames;
use Honed\Action\InlineAction;
use Honed\Table\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasRecords
{
    use HasParameterNames;
    use Support\HasPagination;
    use Support\HasPaginator;

    /**
     * The records of the table retrieved from the resource.
     *
     * @var array<int,mixed>|null
     */
    protected $records = null;

    /**
     * The pagination metadata of the table.
     *
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
     * Determine if the table has records.
     */
    public function hasRecords(): bool
    {
        return ! \is_null($this->records);
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
     * Format the records using the provided columns.
     *
     * @param  array<int,\Honed\Table\Columns\Column>  $activeColumns
     */
    public function formatAndPaginate(array $activeColumns): void
    {
        if ($this->hasRecords()) {
            return;
        }

        [$records, $this->meta] = $this->retrievedRecords();

        $this->records = $this->formatRecords($records, $activeColumns);
    }

    /**
     * Retrieve the records from the underlying builder, returning the records
     * collection and pagination metadata.
     *
     * @return array{0:\Illuminate\Support\Collection<int,\Illuminate\Database\Eloquent\Model>,1:array<string,mixed>}
     */
    protected function retrievedRecords(): array
    {
        /**
         * @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
         */
        $builder = $this->getBuilder();

        $paginator = $this->getPaginator();

        return match (true) {
            static::isLengthAware($paginator) => $this->lengthAwarePaginateRecords($builder),
            static::isSimple($paginator) => $this->simplePaginateRecords($builder),
            static::isCursor($paginator) => $this->cursorPaginateRecords($builder),
            static::isCollection($paginator) => $this->collectRecords($builder),
            default => static::throwInvalidPaginatorException($paginator),
        };
    }

    /**
     * Format the records using the provided columns.
     *
     * @param  \Illuminate\Support\Collection<int,\Illuminate\Database\Eloquent\Model>  $records
     * @param  array<int,\Honed\Table\Columns\Column>  $activeColumns
     * @return array<int,array<string,mixed>>
     */
    protected function formatRecords(Collection $records, array $activeColumns): array
    {
        return $records->map(
            fn (Model $record) => $this->formatRecord($record, $activeColumns)
        )->all();
    }

    /**
     * Format a record using the provided columns.
     *
     * @param  array<int,\Honed\Table\Columns\Column>  $columns
     * @return array<string,mixed>
     */
    protected function formatRecord(Model $record, array $columns): array
    {
        [$named, $typed] = static::getNamedAndTypedParameters($record);

        $actions = collect($this->getInlineActions())
            ->filter(fn (InlineAction $action) => $action->isAllowed($named, $typed))
            ->map(fn (InlineAction $action) => $action->resolve($named, $typed)->toArray())
            ->all();

        $formatted = collect($columns)
            ->mapWithKeys(fn (Column $column) => $this->formatColumn($column, $record))
            ->all();

        return \array_merge($formatted, ['actions' => $actions]);
    }

    /**
     * Format a single column's value for the record.
     *
     *
     * @return array<string,mixed>
     */
    protected function formatColumn(Column $column, Model $record): array
    {
        /** @var string */
        $name = $column->getName();
        $key = Str::replace('.', '_', $name);

        return [$key => Arr::get($record, $name)];
    }

    /**
     * Length-aware paginate the records from the builder.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<T>  $builder
     * @return array{0:\Illuminate\Support\Collection<int,T>,1:array<string,mixed>}
     */
    protected function lengthAwarePaginateRecords(Builder $builder): array
    {
        /**
         * @var \Illuminate\Pagination\LengthAwarePaginator<T> $paginated
         */
        $paginated = $builder->paginate(
            perPage: $this->getRecordsPerPage(),
            pageName: $this->getPagesKey(),
        );

        $paginated->withQueryString();

        return [
            $paginated->getCollection(),
            $this->lengthAwarePaginatorMetadata($paginated),
        ];
    }

    /**
     * Simple paginate the records from the builder.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<T>  $builder
     * @return array{0:\Illuminate\Support\Collection<int,T>,1:array<string,mixed>}
     */
    protected function simplePaginateRecords(Builder $builder): array
    {
        /**
         * @var \Illuminate\Pagination\Paginator<T> $paginated
         */
        $paginated = $builder->simplePaginate(
            perPage: $this->getRecordsPerPage(),
            pageName: $this->getPagesKey(),
        );

        $paginated->withQueryString();

        return [
            $paginated->getCollection(),
            $this->simplePaginatorMetadata($paginated),
        ];
    }

    /**
     * Cursor paginate the records from the builder.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<T>  $builder
     * @return array{0:\Illuminate\Support\Collection<int,T>,1:array<string,mixed>}
     */
    protected function cursorPaginateRecords(Builder $builder): array
    {
        /**
         * @var \Illuminate\Pagination\CursorPaginator<T> $paginated
         */
        $paginated = $builder->cursorPaginate(
            perPage: $this->getRecordsPerPage(),
            cursorName: $this->getPagesKey(),
        );

        $paginated->withQueryString();

        return [
            $paginated->getCollection(),
            $this->cursorPaginatorMetadata($paginated),
        ];
    }

    /**
     * Collect the records from the builder.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<T>  $builder
     * @return array{0:\Illuminate\Support\Collection<int,T>,1:array<string,mixed>}
     */
    protected function collectRecords(Builder $builder): array
    {
        $retrieved = $builder->get();

        return [
            $retrieved,
            [],
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
