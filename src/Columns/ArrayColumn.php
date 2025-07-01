<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use function is_null;

class ArrayColumn extends Column
{
    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->type(self::ARRAY);
    }

    /**
     * Format the value of the entry.
     *
     * @param  array<int, mixed>|\Illuminate\Support\Collection<int, mixed>|null  $value
     * @return array<int, mixed>|string|null
     */
    public function format($value)
    {
        return is_null($value) ? null : $this->formatArray($value);
    }
}
