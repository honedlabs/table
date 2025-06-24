<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class BadgeColumn extends Column
{
    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->type(self::BADGE);

        $this->badge();
    }
}
