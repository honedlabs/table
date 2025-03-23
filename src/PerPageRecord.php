<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Core\Concerns\HasValue;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Primitive;

class PerPageRecord extends Primitive
{
    use HasValue;
    use IsActive;

    /**
     * Create a new per page record.
     *
     * @param  int  $value
     * @param  int  $active
     * @return static
     */
    public static function make($value, $active = 0)
    {
        return resolve(static::class)
            ->value($value)
            ->active($active === $value);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'value' => $this->getValue(),
            'active' => $this->isActive(),
        ];
    }
}
