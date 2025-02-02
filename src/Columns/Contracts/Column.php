<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Contracts;

interface Column
{
    /**
     * Modify the record value to align it with the column configuration.
     *
     * @template T
     *
     * @param  T  $value
     * @return T|mixed
     */
    public function apply(mixed $value);

    /**
     * Format how the record values are displayed in this column.
     *
     * @template T
     *
     * @param  T  $value
     * @return T|mixed
     */
    public function formatValue(mixed $value);
}
