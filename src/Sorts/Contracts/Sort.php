<?php

declare(strict_types=1);

namespace Honed\Table\Sorts\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Sort
{
    /**
     * Apply the sort to the builder
     */
    public function apply(Builder $builder, string $sortBy, ?string $direction = 'asc'): void;

    /**
     * Handle the sorting operation
     */
    public function handle(Builder $builder, ?string $direction = null): void;

    /**
     * Determine if the sort is being applied.
     */
    public function isSorting(?string $sortBy, ?string $direction): bool;

    /**
     * Retrieve the query parameter name of the sort.
     */
    public function getParameterName(): string;
}
