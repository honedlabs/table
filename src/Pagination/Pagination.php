<?php

namespace Honed\Table\Pagination;

use Honed\Core\Concerns\HasValue;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Primitive;

class Pagination extends Primitive
{
    use HasValue;
    use IsActive;

    public function __construct(int $value, bool $active = false)
    {
        $this->setValue($value);
        $this->setActive($active);
    }

    public static function make(int $value, bool $active = false): static
    {
        return resolve(static::class, compact(
            'value',
            'active'
        ));
    }

    public function toArray(): array
    {
        return [
            'value' => $this->getValue(),
            'active' => $this->isActive(),
        ];
    }
}
