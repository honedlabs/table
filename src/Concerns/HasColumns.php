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
     * @var Collection<BaseColumn>
     */
    protected Collection $cachedColumns;

    /**
     * @var array<int, BaseColumn>
     */
    protected array $columns;

    /**
     * @param  array<BaseColumn>|null  $columns
     */
    protected function setColumns(?array $columns): void
    {
        if (is_null($columns)) {
            return;
        }
        $this->columns = $columns;
    }

    /**
     * @return Collection<BaseColumn>
     */
    public function getColumns(): Collection
    {
        return $this->cachedColumns ??= collect($this->inspect('columns', []))
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

    /**
     * @return BaseColumn|null
     */
    public function getKeyColumn(): ?BaseColumn
    {
        return $this->getColumns()->first(fn (BaseColumn $column): bool => $column->isKey());
    }
}
