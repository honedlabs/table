<?php

namespace Honed\Table\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Sorts
{
    /**
     * Apply the sort to the builder
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $sortName The sorting field
     * @param string $directionName The sorting direction
     * @return void
     */
    public function apply(Builder $builder, string $sortName, string $directionName): void;

    /**
     * Handle the sorting operation
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param 'asc'|'desc'|null $direction The sorting direction
     * @return void
     */
    public function handle(Builder $builder, ?string $direction = null): void;

    /**
     * Determine if the sort is being applied.
     *
     * @param string|null $sortBy The sorting field
     * @param 'asc'|'desc'|null $direction The sorting direction
     * @return bool
     */
    public function isSorting(?string $sortBy, ?string $direction): bool;

    /**
     * Retrieve the sort value and direction from the current request.
     *
     * @param string $sortName The query parameter name for the sort field.
     * @param string $directionName The query parameter name for the sort direction.
     * @return array{string|null,'asc'|'desc'|null} [sort field, direction]
     */
    public function getValueFromRequest(string $sortName, string $directionName): array;

    /**
     * Retrieve the query parameter name of the sort
     *
     * @internal
     * @return string
     */
    public function getParameterName(): string;
}
