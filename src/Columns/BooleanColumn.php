<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class BooleanColumn extends BaseColumn
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setType('col:bool');
        $this->boolean();
    }
}
