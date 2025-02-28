<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class HiddenColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->type('hidden');
        $this->always();
        $this->hidden();
    }
}
