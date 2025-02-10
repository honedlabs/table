<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Core\Concerns\HasValue;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Primitive;

/**
 * @extends Primitive<string,bool|int>
 */
class Page extends Primitive
{
    use HasValue;
    use IsActive;

    public static function make(int $value, int $active = 0): static
    {
        return resolve(static::class)
            ->value($value)
            ->active($active === $value);
    }

    public function toArray(): array
    {
        return [
            'value' => $this->getValue(),
            'active' => $this->isActive(),
        ];
    }
}
