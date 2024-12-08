<?php

declare(strict_types=1);

namespace Honed\Table\Sorts;

use Honed\Core\Concerns\IsDefault;
use Illuminate\Database\Eloquent\Builder;
use Honed\Table\Filters\Concerns\HasQuery;

class CustomSort extends BaseSort
{
    use IsDefault;
    use HasQuery;

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
