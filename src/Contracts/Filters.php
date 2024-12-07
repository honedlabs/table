<?php

namespace Honed\Table\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface Filters
{
    public function apply(Builder $builder): void;

    public function handle(Builder $builder): void;

    public function isFiltering(Request $request): bool;

    public function getValueFromRequest(): mixed;
}
