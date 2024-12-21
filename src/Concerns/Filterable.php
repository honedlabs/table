<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
     */
    public function missingFilters(): bool
    {
        return $this->getFilters()->isEmpty();
    }

    /**
     * Determine if the class has filters.
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
     */
    public function filterQuery(Builder $builder): void
    {
        $this->getFilters()
            ->each(static fn (BaseFilter $filter) => $filter->apply($builder));
    }
}
