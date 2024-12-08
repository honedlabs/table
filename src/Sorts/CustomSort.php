<?php

declare(strict_types=1);

namespace Honed\Table\Sorts;

use Honed\Core\Concerns\IsDefault;
use Honed\Table\Filters\Concerns\HasQuery;
use Illuminate\Database\Eloquent\Builder;

class CustomSort extends BaseSort
{
    use HasQuery;
    use IsDefault;

    public function setUp(): void
    {
        $this->setType('sort:custom');
    }

    public function handle(Builder $builder, ?string $direction = null): void
    {
        if ($this->missingQuery()) {
            return;
        }

        $this->getQuery()($builder, $direction);
    }
}
