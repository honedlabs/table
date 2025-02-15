<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class HiddenColumn extends Column
{
    public function setUp(): void
    {
        parent::setUp();

        $this->type('hidden');
        $this->always();
        $this->hidden();
    }
}
