<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class ImageColumn extends Column
{
    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->type(self::IMAGE);
    }

    /**
     * Format the value of the entry.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function format($value)
    {
        return is_null($value) ? null : $this->formatImage($value);
    }
}
