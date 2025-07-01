<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use function is_null;

class NumericColumn extends Column
{
    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder('0');

        $this->type(self::NUMERIC);

    }

    /**
     * Format the value of the entry.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function format($value)
    {
        return is_null($value) ? null : $this->formatNumeric($value);
    }
}
