<?php

declare(strict_types=1);

namespace Honed\Table\Sorts;

use Honed\Core\Concerns\IsDefault;

class Sort extends BaseSort
{
    use IsDefault;

    public function setUp(): void
    {
        $this->setType('sort');
    }

    public function isSorting(?string $sortBy, ?string $direction): bool
    {
        return parent::isSorting($sortBy, $direction)
            && ($this->isAgnostic() ? true : $direction === $this->getDirection());
    }
}
