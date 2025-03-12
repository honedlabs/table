<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\Column;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HasColumns
{
    /**
     * The columns to be used for the table.
     *
     * @var array<int,\Honed\Table\Columns\Column>|null
     */
    protected $columns;

    /**
     * Whether the columns should be retrievable.
     *
     * @var bool
     */
    protected $withoutColumns = false;

    /**
     * Merge a set of columns with the existing columns.
     *
     * @template T of \Honed\Table\Columns\Column
     *
     * @param  array<int,T>|Collection<int,T>  $columns
     * @return $this
     */
    public function addColumns($columns)
    {
        if ($columns instanceof Collection) {
            $columns = $columns->all();
        }

        $this->columns = \array_merge($this->columns ?? [], $columns);

        return $this;
    }

    /**
     * Add a single column to the list of columns.
     *
     * @param  \Honed\Table\Columns\Column  $column
     * @return $this
     */
    public function addColumn($column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * Set the columns to not be retrieved.
     *
     * @return $this
     */
    public function withoutColumns()
    {
        $this->withoutColumns = true;

        return $this;
    }

    /**
     * Determine if the columns should not be retrieved.
     *
     * @return bool
     */
    public function isWithoutColumns()
    {
        return $this->withoutColumns;
    }

    /**
     * Get the columns for the table.
     *
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function getColumns()
    {
        if ($this->isWithoutColumns()) {
            return [];
        }

        return once(function () {
            $methodColumns = method_exists($this, 'columns') ? $this->columns() : [];
            $propertyColumns = $this->columns ?? [];

            return \array_values(
                \array_filter(
                    \array_merge($propertyColumns, $methodColumns),
                    static fn (Column $column): bool => $column->isAllowed()
                )
            );
        });
    }

    /**
     * Determine if the table has columns.
     *
     * @return bool
     */
    public function hasColumns()
    {
        return filled($this->getColumns());
    }

    /**
     * Get the columns which are active for toggling.
     *
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function getActiveColumns()
    {
        return \array_values(
            \array_filter(
                $this->getColumns(),
                static fn (Column $column): bool => $column->isActive()
            )
        );
    }

    /**
     * Get the sortable columns for the table or a list of columns.
     *
     * @param  array<int,\Honed\Table\Columns\Column>|null  $columns
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function getColumnSorts($columns = null)
    {
        $columns = $columns ?? $this->getColumns();

        return \array_values(
            \array_filter(
                $columns,
                static fn (Column $column) => $column->isSortable()
            )
        );
    }

    /**
     * Get the searchable columns from the table or a list of columns.
     *
     * @param  array<int,\Honed\Table\Columns\Column>|null  $columns
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function getColumnSearches($columns = null)
    {
        $columns = $columns ?? $this->getColumns();

        return \array_values(
            \array_filter(
                $columns,
                static fn (Column $column) => $column->isSearchable()
            )
        );
    }

    /**
     * Get the key column for the table.
     *
     * @return \Honed\Table\Columns\Column|null
     */
    public function getKeyColumn()
    {
        return Arr::first(
            $this->getColumns(),
            static fn (Column $column): bool => $column->isKey()
        );
    }

    /**
     * Get the columns as an array.
     *
     * @return array<int,array<string,mixed>>
     */
    public function columnsToArray()
    {
        return \array_map(
            static fn (Column $column) => $column->toArray(),
            $this->getColumns()
        );
    }
}
