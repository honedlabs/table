<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class DateTimeColumn extends Column
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

        $this->dateTime();
    }

    /**
     * Format the value of the entry.
     *
     * @param  \Carbon\CarbonInterface|string|int|float|null  $value
     * @return string|null
     */
    public function format($value)
    {
        return $this->formatDateTime($value);
    }
}
