<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class BooleanColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->type('boolean');
        $this->formatBoolean();
    }

    /**
     * Set the labels for the boolean column.
     *
     * @param  string|null  $true
     * @param  string|null  $false
     * @return $this
     */
    public function labels($true = null, $false = null)
    {
        /** @var \Honed\Core\Formatters\BooleanFormatter|null */
        $formatter = $this->getFormatter();

        $formatter?->true($true)->false($false);

        return $this;
    }
}
