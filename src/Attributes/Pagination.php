<?php

declare(strict_types=1);

namespace Honed\Table\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Pagination
{
    /**
     * Create a new attribute instance.
     *
     * @param  int|array<int, int>  $pagination
     * @return void
     */
    public function __construct(
        public int|array $pagination = 10
    ) {}
}
