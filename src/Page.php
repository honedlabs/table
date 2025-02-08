<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Core\Concerns\HasValue;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Primitive;

class Page extends Primitive
{
    use HasValue;
    use IsActive;

    public function __construct(int $value, bool $active = false)
    {
        $this->value($value);
        $this->active($active);
    }

    public static function make(int $value, bool $active = false): static
    {
        return resolve(static::class, \compact('value', 'active'));
    }

    public function toArray(): array
    {
        return [
            'value' => $this->getValue(),
            'active' => $this->isActive(),
        ];
    }
}
