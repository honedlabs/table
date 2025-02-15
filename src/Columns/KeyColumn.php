<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class KeyColumn extends Column
{
    public function setUp(): void
    {
        parent::setUp();

        $this->type('key');
        $this->hidden();
        $this->key();
    }
}
