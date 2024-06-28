<?php

namespace Conquest\Table\Columns\Concerns;

use Conquest\Table\Columns\Column;
use Illuminate\Support\Collection;

trait HasColumns
{
    private Collection $cachedColumns;

    protected array $columns;

    protected function setColumns(array|null $columns): void
    {
        if (is_null($columns)) return;
        $this->columns = $columns;
    }

    /**
     * Define the columns for the table.
     * 
     * @return array
     */
    protected function getColumns(): array
    {
        if (isset($this->columns)) {
            return $this->columns;
        }

        if (method_exists($this, 'columns')) {
            return $this->columns();
        }

        return [];
    }

    private function getTableColumns(): Collection
    {
        return $this->cachedColumns ??= collect($this->getColumns())
            ->filter(static fn (Column $column) => $column->authorized()
        )->values();
        // return collect($this->getColumns())->values();
    }

    /**
     * Retrieve the sortable columns for the table
     * 
     * @return Collection
     */
    protected function getSortableColumns(): Collection
    {
        return $this->getTableColumns()->filter(static fn (Column $column): bool => $column->hasSort());
    }

    /**
     * Retrieve the key column for the table if one exists
     * 
     * @return Column|null
     */
    protected function getKeyColumn(): ?Column
    {
        return $this->getTableColumns()->first(fn (Column $column): bool => $column->isKey());
    }
}