<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\Column;

trait HasColumns
{
    /**
     * The columns to be used for the table.
     *
     * @var array<int,Column>
     */
    protected $columns = [];

    /**
     * The cached column headings.
     *
     * @var array<int,Column>|null
     */
    protected $headings;

    /**
     * Merge a set of columns with the existing columns.
     *
     * @param  Column|array<int,Column>  $columns
     * @return $this
     */
    public function columns($columns)
    {
        /** @var array<int,Column> */
        $columns = is_array($columns) ? $columns : func_get_args();

        $this->columns = [...$this->columns, ...$columns];

        return $this;
    }

    /**
     * Insert a column.
     *
     * @param  Column  $column
     * @return $this
     */
    public function column($column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * Retrieve the columns.
     *
     * @return array<int,Column>
     */
    public function getColumns()
    {
        return array_values(
            array_filter(
                $this->columns,
                static fn (Column $column) => $column->isAllowed()
            )
        );
    }

    /**
     * Set the cached headings.
     *
     * @param  array<int,Column>  $headings
     * @return void
     */
    public function setHeadings($headings)
    {
        $this->headings = $headings;
    }

    /**
     * Set the columns by overriding the existing columns.
     *
     * @param  array<int,Column>  $columns
     * @return void
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * Get the cached heading columns.
     *
     * @return array<int,Column>
     */
    public function getHeadings()
    {
        return $this->headings ?? $this->getColumns();
    }

    /**
     * Get the columns being applied.
     *
     * @return array<int,Column>
     */
    public function getActiveColumns()
    {
        return array_values(
            array_filter(
                $this->getColumns(),
                static fn (Column $column) => $column->isActive()
            )
        );
    }

    /**
     * Get the columns as an array.
     *
     * @return array<int,array<string,mixed>>
     */
    public function columnsToArray()
    {
        return array_map(
            static fn (Column $column) => $column->toArray(),
            $this->getColumns()
        );
    }
}
