<?php

namespace Honed\Table\Concerns;

use Honed\Table\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    /**
     * @var array<int,\Honed\Table\Filters\BaseFilter>
     */
    protected $filters;

    /**
     * @param  array<int,\Honed\Table\Filters\BaseFilter>|null  $filters
     */
    protected function setFilters(?array $filters): void
    {
        if (is_null($filters)) {
            return;
        }
        $this->filters = $filters;
    }

    /**
     * @internal
     *
     * @return array<int, BaseFilter>
     */
    protected function definedFilters(): array
    {
        if (isset($this->filters)) {
            return $this->filters;
        }

        if (method_exists($this, 'filters')) {
            return $this->filters();
        }

        return [];
    }

    /**
     * Get the authorized filters.
     *
     * @return array<int, BaseFilter>
     */
    public function getFilters(): array
    {
        return array_filter($this->definedFilters(), fn (BaseFilter $filter) => $filter->isAuthorized());
    }

    /**
     * Apply the authorized filters to the builder.
     */
    protected function applyFilters(Builder $builder): void
    {
        foreach ($this->getFilters() as $filter) {
            $filter->apply($builder);
        }
    }
}
