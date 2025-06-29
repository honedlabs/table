<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;

/**
 * @template TClass of \Honed\Table\Table
 *
 * @extends Pipe<TClass>
 */
class Query extends Pipe
{
    /**
     * Run the query logic.
     */
    public function run(): void
    {
        foreach ($this->instance->getHeadings() as $heading) {
            $this->instance->evaluate($heading->queryCallback());
        }
    }
}
