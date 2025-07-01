<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class IconColumn extends Column
{
    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->type(self::ICON);
    }
}
