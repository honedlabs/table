<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class DateColumn extends Column
{
    public function setUp(): void
    {
        parent::setUp();
        $this->formatDate();
    }
}
