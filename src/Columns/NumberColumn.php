<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class NumberColumn extends Column
{
    /**
     * The number of decimal places to display.
     *
     * @var int|null
     */
    protected $decimals;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->type('number');
    }
}
