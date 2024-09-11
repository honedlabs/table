<?php

namespace Conquest\Table\Concerns;

use Conquest\Table\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    /**
     * @var array<int, BaseFilter>
     */
    protected array $filters;

    /**
     * @param array<int, BaseFilter>|null $filters
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
     * 
     * @param Builder $builder
     */
    protected function filter(Builder $builder): void
    {
        foreach ($this->getFilters() as $filter) {
            $filter->apply($builder);
        }
    }
}
