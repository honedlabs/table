<?php

namespace Conquest\Table\Concerns;

use Conquest\Table\Columns\BaseColumn;
use Illuminate\Support\Collection;

trait HasColumns
{
    /**
     * @var Collection<BaseColumn>
     */
    protected Collection $cachedColumns;

    /**
     * @var array<int, BaseColumn>
     */
    protected array $columns;

    /**
     * @param  array<int, BaseColumn>|null  $columns
     */
    protected function setColumns(?array $columns): void
    {
        if (is_null($columns)) {
            return;
        }
        $this->columns = $columns;
    }

    /**
     * @internal
     * @return array<int, BaseColumn>
     */
    protected function definedColumns(): array
    {
        if (isset($this->columns)) {
            return $this->columns;
        }

        if (method_exists($this, 'columns')) {
            return $this->columns();
        }

        return [];
    }

    /**
     * @return Collection<BaseColumn>
     */
    public function getColumns(): Collection
    {
        return $this->cachedColumns ??= collect($this->definedColumns())
            ->filter(fn (BaseColumn $column): bool => $column->isAuthorized());
    }

    /**
     * @return Collection<BaseColumn>
     */
    public function getSortableColumns(): Collection
    {
        return $this->getColumns()->filter(fn (BaseColumn $column): bool => $column->hasSort())->values();
    }

    /**
     * @return Collection<string>
     */
    public function getSearchableColumns(): Collection
    {
        return $this->getColumns()->filter(fn (BaseColumn $column): bool => $column->isSearchable())->pluck('name');
    }

    public function getKeyColumn(): ?BaseColumn
    {
        return $this->getColumns()->first(fn (BaseColumn $column): bool => $column->isKey());
    }

    public function getHeadingColumns(): Collection
    {
        return $this->getColumns()->filter(fn (BaseColumn $column): bool => $column->isActive())->values();
    }
}
