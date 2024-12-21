<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Filters\BaseFilter;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * @var array<int,\Honed\Table\Filters\BaseFilter>
     */
    protected $filters;

    /**
     * Set the list of filters to apply to a query.
     *
     * @param  array<int, \Honed\Table\Filters\BaseFilter>|null  $filters
     * @return void
     */
    public function setFilters(?array $filters): void
    {
        if (\is_null($filters)) {
            return;
        }

        $this->filters = $filters;
    }

    /**
     * Determine if the class has no filters.
     *
     * @return bool
     */
    public function missingFilters(): bool
    {
        return $this->getFilters()->isEmpty();
    }

    /**
     * Determine if the class has filters.
     *
     * @return bool
     */
    public function hasFilters(): bool
    {
        return ! $this->missingFilters();
    }

    /**
     * Get the sorts to apply to the resource.
     *
     * @return Collection<\Honed\Table\Filters\BaseFilter>
     */
    public function getFilters(): Collection
    {
        return collect($this->inspect('filters', []));
    }

    /**
     * Apply the filters to a query using the current request
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function filterQuery(Builder $builder): void
    {
        $this->getFilters()
            ->each(static fn (BaseFilter $filter) => $filter->apply($builder));
    }
}
