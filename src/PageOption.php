<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Core\Concerns\HasValue;
use Honed\Core\Concerns\CanBeActive;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * @implements Arrayable<string, mixed>
 */
class PageOption implements Arrayable, JsonSerializable
{
    use HasValue;
    use CanBeActive;

    /**
     * Create a new per page record.
     *
     * @param  int  $value
     * @param  int  $current
     * @return static
     */
    public static function make($value, $current = 0)
    {
        return resolve(static::class)
            ->value($value)
            ->active($current === $value);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return [
            'value' => $this->getValue(),
            'active' => $this->isActive(),
        ];
    }
}
