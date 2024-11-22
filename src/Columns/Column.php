<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class Column extends BaseColumn
{
    use Concerns\IsSearchable;
    
    public function setUp()
    {
        $this->setType('default');
    }
}
