<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class Column extends BaseColumn
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setType('col:default');
    }
}
