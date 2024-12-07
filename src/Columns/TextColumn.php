<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class TextColumn extends BaseColumn
{
    public function setUp(): void
    {
        $this->setType('text');
        $this->asString();
    }
}
