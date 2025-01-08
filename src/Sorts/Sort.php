<?php

declare(strict_types=1);

namespace Honed\Table\Sorts;

class Sort extends BaseSort
{
    public function setUp(): void
    {
        $this->setType('sort');
    }

    public function isSorting(?string $sortBy, ?string $direction): bool
    {
        return parent::isSorting($sortBy, $direction)
            && ($this->isAgnostic() || $direction === $this->getDirection());
    }
}
