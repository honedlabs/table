<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class TextColumn extends BaseColumn
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setType('col:text');
        $this->string();
    }
}
