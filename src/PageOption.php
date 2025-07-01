<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Core\Concerns\CanBeActive;
use Honed\Core\Concerns\HasValue;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * @implements Arrayable<string, mixed>
 */
class PageOption implements Arrayable, JsonSerializable
{
    use CanBeActive;
    use HasValue;

    /**
     * Create a new per page record.
     */
    public static function make(int $value, int $current = 0): static
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
    public function toArray(): array
    {
        return [
            'value' => $this->getValue(),
            'active' => $this->isActive(),
        ];
    }
}
