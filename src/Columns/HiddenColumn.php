<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class HiddenColumn extends Column
{
    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->hidden();
    }
}
