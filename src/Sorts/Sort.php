<?php

declare(strict_types=1);

namespace Honed\Table\Sorts;

use Honed\Core\Concerns\IsDefault;

class Sort extends BaseSort
{
    use IsDefault;
    use Concerns\IsBound;

    public function setUp(): void
    {
        $this->setType('sort');
    }

    public function isSorting(?string $sortBy, ?string $direction): bool
    {
        return $sortBy === $this->getParameterName() && ($this->hasDirection() ? $direction === $this->getDirection() : true);
    }
}
