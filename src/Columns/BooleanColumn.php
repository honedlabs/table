<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class BooleanColumn extends Column
{
    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->type(self::BOOLEAN);
    }

    /**
     * Format the value of the entry.
     *
     * @param  mixed  $value
     * @return string|null
     */
    public function format($value)
    {
        return $this->formatBoolean($value);
    }
}
