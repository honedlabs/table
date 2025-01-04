<?php

namespace Honed\Table\Concerns;

use Honed\Table\Columns\BaseColumn;
use Illuminate\Support\Collection;

trait HasColumns
{
    /**
     * Retrieved columns with authorization applied.
     * 
     * @var Collection<\Honed\Table\Columns\BaseColumn>
     */
    protected $cachedColumns;

    /**
     * The columns to be used for the table.
     * 
     * @var array<int,\Honed\Table\Columns\BaseColumn>
     */
    // protected $columns;

    /**
     * Set the columns for the table.
     *
     * @param  array<int,\Honed\Table\Columns\BaseColumn>|null  $columns
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
     * Authorization is applied at this level.
     *
     * @return Collection<\Honed\Table\Columns\BaseColumn>
     */
    public function getColumns(): Collection
    {
        return $this->cachedColumns ??= collect(match(true) {
            \method_exists($this, 'columns') => $this->columns(),
            \property_exists($this, 'columns') => $this->columns,
            default => [],
        })->filter(static fn (BaseColumn $column): bool => $column->isAuthorized());
    }

    /**
     * Get the sortable columns for the table.
     *
     * @return Collection<\Honed\Table\Columns\BaseColumn>
     */
    public function getSortableColumns(): Collection
    {
        return $this->getColumns()
            ->filter(static fn (BaseColumn $column): bool => $column->isSortable())
            ->values();
    }

    /**
     * Get the searchable attributes for the table.
     *
     * @return Collection<string>
     */
    public function getSearchableColumns(): Collection
    {
        return $this->getColumns()
            ->filter(static fn (BaseColumn $column): bool => $column->isSearchable())
            ->pluck('name');
    }

    /**
     * Get the key column for the table.
     */
    public function getKeyColumn(): BaseColumn|null
    {
        return $this->getColumns()
            ->first(static fn (BaseColumn $column): bool => $column->isKey());
    }

    /**
     * Retrieve the column attributes.
     *
     * @return array<string,mixed>
     */
    public function getAttributedColumns(): array
    {
        return $this->getColumns()
            ->mapWithKeys(fn (BaseColumn $column) => [$column->getName() => $column])
            ->toArray();
    }

    /**
     * Get the columns that are active.
     *
     * @return Collection<\Honed\Table\Columns\BaseColumn>
     */
    public function getActiveColumns(): Collection
    {
        return $this->getColumns()
            ->filter(fn (BaseColumn $column): bool => $column->isActive())
            ->values();
    }
}
