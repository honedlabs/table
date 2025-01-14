<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Illuminate\Database\Eloquent\Builder;

class CustomFilter extends BaseFilter
{
    use Concerns\HasQuery;

    public function setUp(): void
    {
        $this->setType('filter:custom');
    }

    public function handle(Builder $builder): void
    {
        if (! $this->hasQuery()) {
            return;
        }

        \call_user_func($this->getQuery(), $builder);
    }
}
