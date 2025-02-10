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
     * @var Collection<int,\Honed\Table\Columns\Column>|null
     */
    protected $cachedColumns;

    /**
     * The columns to be used for the table.
     * 
     * @var array<int,\Honed\Table\Columns\Column>|null
     */
    protected $columns;

    /**
     * Set the columns for the table.
     *
     * @param  array<int,\Honed\Table\Columns\Column>|null  $columns
     */
    public function setColumns(?array $columns): void
    {
        if (\is_null($columns)) {
            return;
        }

        $this->columns = $columns;
    }
    
    /**
     * Determine if the table has columns.
     */
    public function hasColumns(): bool
    {
        return ! empty($this->getColumns());
    }

    /**
     * Get the columns for the table.
     *
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function getColumns(): array
    {
        return $this->cachedColumns ??= Arr::where(match(true) {
            \method_exists($this, 'columns') => $this->columns(),
            isset($this->columns) => $this->columns,
            default => [],
        }, static fn (Column $column): bool => $column->isAllowed());
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
