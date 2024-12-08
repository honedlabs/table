<?php

namespace Honed\Table\Concerns;

use Honed\Table\Columns\BaseColumn;
use Illuminate\Support\Collection;

/**
 * @mixin Honed\Core\Concerns\Inspectable
 */
trait HasColumns
{
    /**
     * @var Collection<\Honed\Table\Columns\BaseColumn>
     */
    protected $cachedColumns;

    /**
     * @var array<int,\Honed\Table\Columns\BaseColumn>
     */
    protected $columns;

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
     * Determine if the table has no columns.
     */
    public function missingColumns(): bool
    {
        return $this->getColumns()->isEmpty();
    }

    /**
     * Determine if the table has columns.
     */
    public function hasColumns(): bool
    {
        return ! $this->missingColumns();
    }

    /**
     * Get the columns for the table.
     *
     * @return Collection<\Honed\Table\Columns\BaseColumn>
     */
    public function getColumns(): Collection
    {
        return $this->cachedColumns ??= collect($this->inspect('columns', []))
            ->filter(static fn (BaseColumn $column): bool => $column->isAuthorized());
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
    public function getKeyColumn(): ?BaseColumn
    {
        return $this->getColumns()
            ->first(static fn (BaseColumn $column): bool => $column->isKey());
    }

    /**
     * Retrieve the column attributes.
     *
     * @return array<string, mixed>
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
     * @return Collection<BaseColumn>
     */
    public function getActiveColumns(): Collection
    {
        return $this->getColumns()
            ->filter(fn (BaseColumn $column): bool => $column->isActive())
            ->values();
    }
}
