<?php

namespace Honed\Table\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface Filters
{
    public function apply(Builder $builder): void;

    public function handle(Builder $builder): void;

    public function isFiltering(mixed $value): bool;

    /**
     * Retrieve the value of the filter name from the current request.
     *
     * @return int|string|array<int,int|string>|null
     */
    public function getValueFromRequest(): mixed;

    /**
     * Retrieve the query parameter name of the filter
     *
     * @internal
     */
    public function getParameterName(): string;
}
