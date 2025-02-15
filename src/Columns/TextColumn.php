<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class TextColumn extends Column
{
    public function setUp(): void
    {
        parent::setUp();

        $this->type('text');
        $this->formatString();
    }
}
