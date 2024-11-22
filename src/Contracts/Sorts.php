<?php

namespace Honed\Table\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Sorts
{
    public function apply(Builder $builder, ?string $sortBy = null, ?string $direction = null): void;

    public function handle(Builder $builder, ?string $direction = null): void;

    public function sorting(?string $sortBy, ?string $direction): bool;
}
