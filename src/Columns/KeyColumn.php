<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class KeyColumn extends Column
{
    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->key();
        $this->qualify();
    }
}
