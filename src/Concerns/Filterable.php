<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Filters\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait Filterable
{
    /**
     * @var array<int,\Honed\Table\Filters\Contracts\Filter>
     */
    protected $filters;

    /**
     * Set the list of filters to apply to a query.
     *
     * @param  array<int, \Honed\Table\Filters\Contracts\Filter>|null  $filters
     */
    public function setFilters(?array $filters): void
    {
        if (\is_null($filters)) {
            return;
        }

        $this->filters = $filters;
    }

    /**
     * Determine if the class has filters.
     */
    public function hasFilters(): bool
    {
        return $this->getFilters()->isNotEmpty();
    }

    /**
     * Get the sorts to apply to the resource.
     *
     * @return Collection<\Honed\Table\Filters\Contracts\Filter>
     */
    public function getFilters(): Collection
    {
        return collect(match (true) {
            \property_exists($this, 'filters') && !\is_null($this->filters) => $this->filters,
            \method_exists($this, 'filters') => $this->filters(),
            default => [],
        });
    }

    /**
     * Apply the filters to a query.
     */
    public function filterQuery(Builder $builder, Request $request = null): void
    {
        $this->getFilters()
            ->each(static fn (Filter $filter) => $filter->apply($builder, $request));
    }
}
