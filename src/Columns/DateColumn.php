<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class DateColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->type('date');
        $this->formatDate();
    }
}
