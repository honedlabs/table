<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Core\Concerns\HasValue;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Primitive;

class PageAmount extends Primitive
{
    use IsActive;
    use HasValue;

    public function __construct(int $value, bool $active = false)
    {
        $this->setValue($value);
        $this->setActive($active);
    }

    public static function make(int $value, bool $active = false): static
    {
        return new static($value, $active);
    }

    public function toArray(): array
    {
        return [
            'value' => $this->getValue(),
            'active' => $this->isActive(),
        ];
    }
}