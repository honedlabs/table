<?php

namespace Honed\Table\Concerns;

use Honed\Table\Columns\Column;
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
        return $this->getColumns()->isNotEmpty();
    }

    /**
     * Get the columns for the table.
     *
     * @return Collection<int,\Honed\Table\Columns\Column>
     */
    public function getColumns(): Collection
    {
        return $this->cachedColumns ??= collect(match(true) {
            \method_exists($this, 'columns') => $this->columns(),
            \property_exists($this, 'columns') && ! \is_null($this->columns) => $this->columns,
            default => [],
        })->filter(static fn (Column $column): bool => $column->isAllowed());
    }

    /**
     * Get the sortable columns for the table.
     *
     * @return Collection<int,\Honed\Table\Columns\Column>
     */
    public function getSortableColumns(): Collection
    {
        return $this->getColumns()
            ->filter(static fn (Column $column): bool => $column->isSortable())
            ->values();
    }

    /**
     * Get the searchable attributes for the table.
     *
     * @return Collection<int,string>
     */
    public function getSearchableColumns(): Collection
    {
        return $this->getColumns()
            ->filter(static fn (Column $column) => $column->isSearchable())
            ->map(static fn (Column $column): string => type($column->getName())->asString())
            ->values();
    }

    /**
     * Get the key column for the table.
     */
    public function getKeyColumn(): ?Column
    {
        return $this->getColumns()
            ->first(static fn (Column $column): bool => $column->isKey());
    }
}
