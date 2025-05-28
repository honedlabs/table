<?php

declare(strict_types=1);

namespace Honed\Table\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Select
{
    /**
     * Create a new attribute instance.
     *
     * @param  string|array<int, string>  $select
     * @return void
     */
    public function __construct(
        public string|array $select
    ) { }

    /**
     * Get the select value.
     * 
     * @return string|array<int, string>
     */
    public function getSelect()
    {
        return $this->select;
    }
}
