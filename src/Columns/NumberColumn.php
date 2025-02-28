<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class NumberColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->type('number');
        $this->formatNumber();
    }
}
