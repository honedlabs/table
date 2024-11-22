<?php

namespace Honed\Table\Sorts;

use Honed\Core\Concerns\IsDefault;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Sort extends BaseSort
{
    use IsDefault;

    public function setUp(): void
    {
        $this->setType('sort');
    }

    public function sorting(?string $sortBy, ?string $direction): bool
    {
        return $sortBy === $this->getParameterName() && ($this->hasDirection() ? $direction === $this->getDirection() : true);
    }
}
