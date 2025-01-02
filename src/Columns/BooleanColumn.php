<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class BooleanColumn extends BaseColumn
{
    public function setUp(): void
    {
        $this->setType('boolean');
        $this->boolean();
    }
}
