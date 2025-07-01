<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class KeyColumn extends Column
{
    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->key();

        $this->qualify();
    }
}
