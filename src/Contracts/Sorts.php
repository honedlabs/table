<?php

namespace Honed\Table\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Sorts
{
    /**
     * Apply the sort to the builder
     *
     * @param  string|null  $sortBy  The sorting field
     * @param  string|null  $direction  The sorting direction
     */
    public function apply(Builder $builder, string $sortBy, string $direction = 'asc'): void;

    /**
     * Handle the sorting operation
     *
     * @param  'asc'|'desc'|null  $direction  The sorting direction
     */
    public function handle(Builder $builder, ?string $direction = null): void;

    /**
     * Determine if the sort is being applied.
     *
     * @param  string|null  $sortBy  The sorting field
     * @param  'asc'|'desc'|null  $direction  The sorting direction
     */
    public function isSorting(?string $sortBy, ?string $direction): bool;

    /**
     * Retrieve the query parameter name of the sort
     *
     * @internal
     */
    public function getParameterName(): string;
}
