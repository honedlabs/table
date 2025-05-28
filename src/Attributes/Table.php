<?php

declare(strict_types=1);

namespace Honed\Table\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Table
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<\Honed\Table\Table>  $table
     * @return void
     */
    public function __construct(
        public string $table
    ) { }

    /**
     * Get the table class.
     * 
     * @return class-string<\Honed\Table\Table>
     */
    public function getTable()
    {
        return $this->table;
    }
}
