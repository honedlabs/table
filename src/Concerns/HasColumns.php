<?php

namespace Honed\Table\Concerns;

use Honed\Table\Columns\Column;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HasColumns
{
    /**
     * Retrieved columns with authorization applied.
     *
     * @var array<int,\Honed\Table\Columns\Column>|null
     */
    protected $cachedColumns;

    /**
     * The columns to be used for the table.
     *
     * @var array<int,\Honed\Table\Columns\Column>|null
     */
    protected $columns;

    /**
     * Determine if the table has columns.
     */
    public function hasColumns(): bool
    {
        return ! empty($this->getColumns());
    }

    /**
     * @template T of \Honed\Table\Columns\Column
     *
     * @param  array<int,T>|Collection<int,T>  $columns
     * @return $this
     */
    public function addColumns(array|Collection $columns): static
    {
        if ($columns instanceof Collection) {
            $columns = $columns->all();
        }

        /** @var array<int, \Honed\Table\Columns\Column> $columns */
        $this->columns = \array_merge($this->columns ?? [], $columns);

        return $this;
    }

    /**
     * Get the columns for the table.
     *
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function getColumns(): array
    {
        return $this->cachedColumns ??= $this->getSourceColumns();
    }

    /**
     * Get the source columns for the table, with permissions applied.
     *
     * @return array<int,\Honed\Table\Columns\Column>
     */
    protected function getSourceColumns(): array
    {
        $columns = match (true) {
            \method_exists($this, 'columns') => $this->columns(),
            isset($this->columns) => $this->columns,
            default => [],
        };

        return Arr::where(
            $columns,
            static fn (Column $column): bool => $column->isAllowed()
        );
    }

    /**
     * Get the columns which are active for toggling.
     *
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function getActiveColumns(): array
    {
        return Arr::where(
            $this->getColumns(),
            static fn (Column $column): bool => $column->isActive()
        );
    }

    /**
     * Get the sortable columns for the table.
     *
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function getSortableColumns(): array
    {
        return Arr::where(
            $this->getColumns(),
            static fn (Column $column): bool => $column->isSortable()
        );
    }

    /**
     * Get the searchable attributes for the table.
     *
     * @return array<int,string>
     */
    public function getSearchableColumns(): array
    {
        return Arr::where(
            $this->getColumns(),
            static fn (Column $column): bool => $column->isSearchable()
        );
    }

    /**
     * Get the key column for the table.
     */
    public function getKeyColumn(): ?Column
    {
        return Arr::first(
            $this->getColumns(),
            static fn (Column $column): bool => $column->isKey()
        );
    }
}
