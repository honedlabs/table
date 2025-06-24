<?php

declare(strict_types=1);

namespace Honed\Table\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseTable
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<\Honed\Table\Table>  $tableClass
     */
    public function __construct(
        public string $tableClass
    ) {}
}
