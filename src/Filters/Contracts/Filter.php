<?php

namespace Honed\Table\Filters\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface Filter
{
    /**
     * Apply the filter to the builder
     */
    public function apply(Builder $builder, ?Request $request = null): void;

    /**
     * Handle the filter operation
     */
    public function handle(Builder $builder): void;

    /**
     * Determine if the filter is being applied
     */
    public function isFiltering(mixed $value): bool;

    /**
     * Retrieve the value of the filter name from the current request.
     *
     * @return int|string|array<int,int|string>|null
     */
    public function getValueFromRequest(?Request $request = null): mixed;

    /**
     * Retrieve the query parameter name of the filter.
     */
    public function getParameterName(): string;
}
