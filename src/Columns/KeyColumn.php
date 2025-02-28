<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class KeyColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->type('key');
        $this->hidden();
        $this->key();
    }
}
