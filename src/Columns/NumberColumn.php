<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class NumberColumn extends Column
{
    public function setUp(): void
    {
        parent::setUp();
        $this->number();
    }
}
