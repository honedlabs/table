<?php

declare(strict_types=1);

namespace Honed\Table\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class PerPage
{
    /**
     * Create a new attribute instance.
     *
     * @param  int|array<int, int>  $perPage
     * @return void
     */
    public function __construct(
        public int|array $perPage
    ) { }

    /**
     * Get the number of records per page.
     * 
     * @return int|array<int, int>
     */
    public function getPerPage()
    {
        return $this->perPage;
    }
}
