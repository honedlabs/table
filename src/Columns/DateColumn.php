<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class DateColumn extends Column
{
    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->placeholder('-');

        parent::setUp();

        $this->date();
    }

    /**
     * Format the value of the entry.
     *
     * @param  \Carbon\CarbonInterface|string|int|float|null  $value
     * @return string|null
     */
    public function format($value)
    {
        return $this->formatDate($value);
    }
}
