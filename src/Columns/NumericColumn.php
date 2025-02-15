<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class NumericColumn extends Column
{
    public function setUp(): void
    {
        parent::setUp();

        $this->type('numeric');
        $this->formatNumeric();
    }
}
